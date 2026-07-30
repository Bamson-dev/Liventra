<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\SimulationEvent;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface LiveSimulationServiceInterface
 * Contract for the Live Simulation Experience Engine (PRD-005 Section 21)
 */
interface LiveSimulationServiceInterface {

	/**
	 * Get all eligible simulation events spooled at or before current offset
	 *
	 * @param array $timelineEvents Raw timeline events definition array.
	 * @param int   $currentOffset Current authoritative session playback offset.
	 * @param int   $lastSyncedOffset Previously synced playback offset.
	 * @return array Array of SimulationEvent objects.
	 */
	public function getEligibleEvents( array $timelineEvents, int $currentOffset, int $lastSyncedOffset = 0 ): array;

	/**
	 * Resolve calculated viewer count for current playback offset
	 *
	 * @param array $config Viewer count model config (fixed, curve, scripted).
	 * @param int   $currentOffset Current authoritative session playback offset.
	 * @param int   $durationSeconds Total webinar video duration.
	 * @return int Computed viewer count.
	 */
	public function resolveViewerCount( array $config, int $currentOffset, int $durationSeconds ): int;

	/**
	 * Resolve poll lifecycle state for a given timeline poll event
	 *
	 * @param array $pollPayload Poll definition payload.
	 * @param int   $triggerSecond Event trigger offset.
	 * @param int   $currentOffset Current playback offset.
	 * @return array Poll lifecycle state ('scheduled', 'visible', 'voting', 'closed', 'results').
	 */
	public function resolvePollState( array $pollPayload, int $triggerSecond, int $currentOffset ): array;

	/**
	 * Resolve event priority collision queue
	 *
	 * @param array $events Array of raw or SimulationEvent items.
	 * @return array Priority-sorted events.
	 */
	public function resolveDisplayQueue( array $events ): array;
}
