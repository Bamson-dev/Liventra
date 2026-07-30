<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\TimelineEvent;
use Liventra\Entities\TimelineVersion;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface TimelineServiceInterface
 * Public Contract for Authoritative Timeline Engine (PRD-006 Part 1)
 */
interface TimelineServiceInterface {

	/**
	 * Publish timeline draft into an immutable published version
	 *
	 * @param int $webinarId Webinar ID.
	 * @return TimelineVersion
	 */
	public function publishTimeline( int $webinarId ): TimelineVersion;

	/**
	 * Validate timeline integrity and dependency graph
	 *
	 * @param array $events Array of TimelineEvent entities or definition arrays.
	 * @return bool True if valid, throws Exception if invalid/circular.
	 */
	public function validateTimeline( array $events ): bool;

	/**
	 * Get eligible timeline events spooled between last synced offset and current offset
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $currentOffset Current playback offset.
	 * @param int $lastSyncedOffset Previously synced offset.
	 * @return array Array of TimelineEvent entities.
	 */
	public function getEligibleEvents( int $webinarId, int $currentOffset, int $lastSyncedOffset = 0 ): array;

	/**
	 * Execute queue of eligible events via registered subsystem handlers
	 *
	 * @param array $events Array of TimelineEvent entities.
	 * @return array Array of EventExecution results.
	 */
	public function executeEvents( array $events ): array;

	/**
	 * Resolve event dependencies for a set of events
	 *
	 * @param array $events Array of TimelineEvent entities.
	 * @return array Dependency-sorted array of TimelineEvent entities.
	 */
	public function resolveDependencies( array $events ): array;

	/**
	 * Mark non-replayable event as executed for an attendee session
	 *
	 * @param string $eventUuid Event UUID.
	 * @param int    $attendeeId Attendee ID.
	 * @return bool
	 */
	public function markExecuted( string $eventUuid, int $attendeeId ): bool;

	/**
	 * Restore state upon attendee reconnection
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $currentOffset Current playback offset.
	 * @param int $attendeeId Attendee ID.
	 * @return array Restored state payload with active persistent events.
	 */
	public function restoreState( int $webinarId, int $currentOffset, int $attendeeId ): array;

	/**
	 * Get current published timeline version
	 *
	 * @param int $webinarId Webinar ID.
	 * @return TimelineVersion|null
	 */
	public function getTimelineVersion( int $webinarId ): ?TimelineVersion;

	/**
	 * Archive older timeline version
	 *
	 * @param int $versionId Version ID.
	 * @return bool
	 */
	public function archiveTimeline( int $versionId ): bool;
}
