<?php
namespace Liventra\Modules\Timeline;

use Liventra\Modules\ModuleInterface;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class TimelineModule
 * Module 5 — Timeline Engine (PRD-002 Section 4)
 * Executes timed events in response to Session Engine timestamps.
 * Never determines playback. Filter events just-in-time for security.
 */
class TimelineModule implements ModuleInterface {

	public function get_name() {
		return 'timeline';
	}

	public function register() {
		EventBus::on( 'timeline.fetch_events', array( $this, 'get_triggered_events' ) );
	}

	public function boot() {
		// Timeline Engine boot
	}

	/**
	 * Retrieve events that have triggered up to current elapsed seconds
	 * (Prevents sending future timeline events to frontend)
	 *
	 * @param array $all_events Array of event definition arrays.
	 * @param int   $elapsed_seconds Current session elapsed time.
	 * @param int   $last_synced_offset Previously synced elapsed time offset.
	 * @return array Events ready for dispatch to frontend.
	 */
	public function get_triggered_events( array $all_events, $elapsed_seconds, $last_synced_offset = 0 ) {
		$spooled = array();

		foreach ( $all_events as $event ) {
			$trigger_time = isset( $event['trigger_time'] ) ? (int) $event['trigger_time'] : 0;

			// Include event ONLY if trigger_time <= current elapsed AND trigger_time > last_synced
			if ( $trigger_time <= $elapsed_seconds && $trigger_time > $last_synced_offset ) {
				$spooled[] = $event;
			}
		}

		return $spooled;
	}
}
