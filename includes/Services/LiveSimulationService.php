<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\LiveSimulationServiceInterface;
use Liventra\Entities\SimulationEvent;
use Liventra\Entities\ViewerCountModel;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class LiveSimulationService
 * Authoritative Implementation of Live Simulation Engine (PRD-005)
 */
class LiveSimulationService implements LiveSimulationServiceInterface {

	/**
	 * Get all eligible simulation events spooled at or before current offset (JIT Security - PRD-005 Section 19)
	 */
	public function getEligibleEvents( array $timelineEvents, int $currentOffset, int $lastSyncedOffset = 0 ): array {
		$eligible = array();

		foreach ( $timelineEvents as $raw ) {
			$eventId       = isset( $raw['id'] ) ? (int) $raw['id'] : 0;
			$webinarId     = isset( $raw['webinar_id'] ) ? (int) $raw['webinar_id'] : 0;
			$eventType     = isset( $raw['event_type'] ) ? (string) $raw['event_type'] : 'cta';
			$triggerSecond = isset( $raw['trigger_second'] ) ? (int) $raw['trigger_second'] : ( isset( $raw['trigger_time'] ) ? (int) $raw['trigger_time'] : 0 );
			$payload       = isset( $raw['payload'] ) ? ( is_array( $raw['payload'] ) ? $raw['payload'] : (array) json_decode( $raw['payload'], true ) ) : ( isset( $raw['event_payload'] ) ? (array) $raw['event_payload'] : array() );

			$event = new SimulationEvent( $eventId, $webinarId, $eventType, $triggerSecond, $payload );

			if ( $event->isEligible( $currentOffset, $lastSyncedOffset ) ) {
				$eligible[] = $event;
			}
		}

		return $this->resolveDisplayQueue( $eligible );
	}

	/**
	 * Resolve calculated viewer count for current playback offset (PRD-005 Section 6)
	 */
	public function resolveViewerCount( array $config, int $currentOffset, int $durationSeconds ): int {
		$model = new ViewerCountModel( $config );
		return $model->calculateCount( $currentOffset, $durationSeconds );
	}

	/**
	 * Resolve poll lifecycle state for a given timeline poll event (PRD-005 Section 10)
	 */
	public function resolvePollState( array $pollPayload, int $triggerSecond, int $currentOffset ): array {
		$visibleWindow = isset( $pollPayload['voting_duration_seconds'] ) ? (int) $pollPayload['voting_duration_seconds'] : 60;
		$resultsWindow = isset( $pollPayload['results_duration_seconds'] ) ? (int) $pollPayload['results_duration_seconds'] : 30;

		$elapsedSinceTrigger = $currentOffset - $triggerSecond;

		if ( $elapsedSinceTrigger < 0 ) {
			$phase = 'scheduled';
		} elseif ( $elapsedSinceTrigger <= $visibleWindow ) {
			$phase = 'voting';
		} elseif ( $elapsedSinceTrigger <= ( $visibleWindow + $resultsWindow ) ) {
			$phase = 'results';
		} else {
			$phase = 'closed';
		}

		return array_merge( $pollPayload, array(
			'phase'                  => $phase,
			'elapsed_since_trigger'  => max( 0, $elapsedSinceTrigger ),
			'voting_time_remaining'  => max( 0, $visibleWindow - $elapsedSinceTrigger ),
		) );
	}

	/**
	 * Resolve event priority collision queue (PRD-005 Section 15 & 16)
	 */
	public function resolveDisplayQueue( array $events ): array {
		usort( $events, function( $a, $b ) {
			$pA = $a instanceof SimulationEvent ? $a->getPriority() : ( isset( $a['priority'] ) ? (int) $a['priority'] : 50 );
			$pB = $b instanceof SimulationEvent ? $b->getPriority() : ( isset( $b['priority'] ) ? (int) $b['priority'] : 50 );

			// Higher priority first
			return $pB - $pA;
		} );

		return $events;
	}
}
