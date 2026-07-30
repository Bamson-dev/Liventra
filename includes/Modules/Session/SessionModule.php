<?php
namespace Liventra\Modules\Session;

use Liventra\Modules\ModuleInterface;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SessionModule
 * Module 4 — Session Engine (PRD-002 Section 4 & 6)
 * Single Source of Truth for Session Authority, Time Synchronization & Status Calculation.
 */
class SessionModule implements ModuleInterface {

	public function get_name() {
		return 'session';
	}

	public function register() {
		EventBus::on( 'session.sync_request', array( $this, 'calculate_session_sync' ) );
	}

	public function boot() {
		// Session Module Runtime
	}

	/**
	 * Calculate Authoritative Playback Position and State
	 *
	 * @param int $scheduled_start_timestamp UTC Unix timestamp of session start.
	 * @param int $video_duration_seconds Total video duration in seconds.
	 * @param int|null $current_time_override Optional UTC Unix timestamp for testing.
	 * @return array Session sync status payload.
	 */
	public function calculate_session_sync( $scheduled_start_timestamp, $video_duration_seconds, $current_time_override = null ) {
		$now = null !== $current_time_override ? (int) $current_time_override : time();

		$elapsed = $now - (int) $scheduled_start_timestamp;

		if ( $elapsed < 0 ) {
			// Waiting Room Phase
			return array(
				'state'                     => 'waiting_room',
				'elapsed_seconds'           => 0,
				'remaining_waiting_seconds' => abs( $elapsed ),
				'server_time'               => $now,
				'can_play'                  => false,
			);
		} elseif ( $elapsed >= 0 && $elapsed < (int) $video_duration_seconds ) {
			// Live Session Phase
			return array(
				'state'                     => 'live',
				'elapsed_seconds'           => $elapsed,
				'remaining_waiting_seconds' => 0,
				'server_time'               => $now,
				'can_play'                  => true,
			);
		} else {
			// Ended Phase
			return array(
				'state'                     => 'ended',
				'elapsed_seconds'           => (int) $video_duration_seconds,
				'remaining_waiting_seconds' => 0,
				'server_time'               => $now,
				'can_play'                  => false,
			);
		}
	}
}
