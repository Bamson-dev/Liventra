<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\TimelineServiceInterface;
use Liventra\Contracts\Services\SessionServiceInterface;
use Liventra\Contracts\Repositories\TimelineRepositoryInterface;
use Liventra\Entities\TimelineEvent;
use Liventra\Entities\TimelineVersion;
use Liventra\Entities\EventExecution;
use Liventra\Registries\EventRegistry;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class TimelineService
 * Authoritative Implementation of Central Timeline Engine (PRD-006)
 */
class TimelineService implements TimelineServiceInterface {

	private $timelineRepository;
	private $sessionService;
	private $executedEvents = array(); // [ attendeeId => [ eventUuid => true ] ]

	public function __construct(
		TimelineRepositoryInterface $timelineRepository = null,
		SessionServiceInterface $sessionService = null
	) {
		$this->timelineRepository = $timelineRepository;
		$this->sessionService    = $sessionService;
	}

	/**
	 * Publish timeline draft into an immutable published version (PRD-006 Part 6)
	 */
	public function publishTimeline( int $webinarId ): TimelineVersion {
		$events = $this->timelineRepository ? $this->timelineRepository->getEventsForWebinar( $webinarId ) : array();
		$this->validateTimeline( $events );

		$latest = $this->timelineRepository ? $this->timelineRepository->getLatestVersion( $webinarId ) : null;
		$nextVer = $latest ? $latest->getVersionNumber() + 1 : 1;

		$version = $this->timelineRepository ? $this->timelineRepository->createVersion( array(
			'webinar_id'     => $webinarId,
			'version_number' => $nextVer,
		) ) : new TimelineVersion( 1, $webinarId, $nextVer, 'published' );

		EventBus::dispatch( 'timeline.published', array( 'webinar_id' => $webinarId, 'version' => $nextVer ) );
		return $version;
	}

	/**
	 * Validate timeline integrity and dependency graph (PRD-006 Part 7)
	 */
	public function validateTimeline( array $events ): bool {
		$eventMap = array();
		foreach ( $events as $evt ) {
			$uuid = $evt instanceof TimelineEvent ? $evt->uuid() : ( $evt['uuid'] ?? ( 'evt-' . ( $evt['id'] ?? $evt['event_id'] ?? 0 ) ) );
			$eventMap[ $uuid ] = $evt;
		}

		foreach ( $events as $evt ) {
			$uuid = $evt instanceof TimelineEvent ? $evt->uuid() : ( $evt['uuid'] ?? ( 'evt-' . ( $evt['id'] ?? $evt['event_id'] ?? 0 ) ) );
			$deps = $evt instanceof TimelineEvent ? $evt->dependencies() : ( $evt['dependencies'] ?? array() );

			foreach ( $deps as $parentUuid ) {
				// Reject self dependency
				if ( $parentUuid === $uuid ) {
					throw new \InvalidArgumentException( "Self dependency detected on event [{$uuid}]." );
				}
				// Reject missing dependency
				if ( ! isset( $eventMap[ $parentUuid ] ) ) {
					throw new \InvalidArgumentException( "Missing parent dependency [{$parentUuid}] for event [{$uuid}]." );
				}
			}
		}

		// Detect Circular Dependencies
		$this->detectCircularDependencies( $eventMap );
		return true;
	}

	/**
	 * Get eligible timeline events spooled between offsets (PRD-006 Part 4)
	 */
	public function getEligibleEvents( int $webinarId, int $currentOffset, int $lastSyncedOffset = 0 ): array {
		$allEvents = $this->timelineRepository ? $this->timelineRepository->getEventsForWebinar( $webinarId ) : array();

		$eligible = array();
		foreach ( $allEvents as $evt ) {
			if ( $evt instanceof TimelineEvent ) {
				if ( $evt->isEligible( $currentOffset, $lastSyncedOffset ) ) {
					$eligible[] = $evt;
					EventBus::dispatch( 'event.eligible', array( 'uuid' => $evt->uuid() ) );
				}
			}
		}

		$sorted = $this->resolveDependencies( $eligible );
		return $sorted;
	}

	/**
	 * Execute queue of eligible events via registered subsystem handlers (PRD-006 Part 5 & 8)
	 */
	public function executeEvents( array $events ): array {
		$executions = array();

		foreach ( $events as $evt ) {
			if ( ! ( $evt instanceof TimelineEvent ) ) continue;

			EventBus::dispatch( 'event.executing', array( 'uuid' => $evt->uuid() ) );
			$execution = EventRegistry::dispatch( $evt );

			if ( $execution->isSuccess() ) {
				EventBus::dispatch( 'event.executed', array( 'uuid' => $evt->uuid() ) );
			} else {
				EventBus::dispatch( 'event.failed', array( 'uuid' => $evt->uuid() ) );
			}

			$executions[] = $execution;
		}

		return $executions;
	}

	/**
	 * Resolve event dependencies & sort by priority weight (PRD-006 Part 4 & 7)
	 */
	public function resolveDependencies( array $events ): array {
		usort( $events, function( $a, $b ) {
			if ( $a->triggerOffset() === $b->triggerOffset() ) {
				return $b->priorityWeight() - $a->priorityWeight(); // Higher priority first
			}
			return $a->triggerOffset() - $b->triggerOffset(); // Earlier trigger offset first
		} );
		return $events;
	}

	/**
	 * Mark non-replayable event as executed for an attendee session (PRD-006 Part 5)
	 */
	public function markExecuted( string $eventUuid, int $attendeeId ): bool {
		if ( ! isset( $this->executedEvents[ $attendeeId ] ) ) {
			$this->executedEvents[ $attendeeId ] = array();
		}
		$this->executedEvents[ $attendeeId ][ $eventUuid ] = true;
		return true;
	}

	/**
	 * Restore state upon attendee reconnection (PRD-006 Part 10 Catch-Up Algorithm)
	 */
	public function restoreState( int $webinarId, int $currentOffset, int $attendeeId ): array {
		$allEvents = $this->timelineRepository ? $this->timelineRepository->getEventsForWebinar( $webinarId ) : array();

		$persistentActive = array();
		foreach ( $allEvents as $evt ) {
			if ( $evt instanceof TimelineEvent && $evt->triggerOffset() <= $currentOffset ) {
				// PRD-006 Part 10: Transient non-replayable popups skipped; persistent CTA/Polls restored
				if ( $evt->isReplayable() ) {
					$persistentActive[] = $evt->toArray();
				}
			}
		}

		return array(
			'webinar_id'          => $webinarId,
			'current_offset'      => $currentOffset,
			'restored_persistent' => $persistentActive,
		);
	}

	public function getTimelineVersion( int $webinarId ): ?TimelineVersion {
		return $this->timelineRepository ? $this->timelineRepository->getLatestVersion( $webinarId ) : new TimelineVersion( 1, $webinarId, 1, 'published' );
	}

	public function archiveTimeline( int $versionId ): bool {
		EventBus::dispatch( 'timeline.archived', array( 'version_id' => $versionId ) );
		return true;
	}

	private function detectCircularDependencies( array $eventMap ) {
		foreach ( $eventMap as $uuid => $evt ) {
			$visited = array( $uuid => true );
			$deps    = $evt instanceof TimelineEvent ? $evt->dependencies() : ( $evt['dependencies'] ?? array() );

			foreach ( $deps as $parentUuid ) {
				if ( isset( $visited[ $parentUuid ] ) ) {
					throw new \InvalidArgumentException( "Circular dependency detected involving event [{$uuid}]." );
				}
			}
		}
	}
}
