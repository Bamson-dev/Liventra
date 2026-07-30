<?php
namespace Liventra\Modules\Session;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SessionEngine
 * Core Time Anchor Calculation & Schedule Engine (PRD-002 Section 4, 6 & 7)
 */
class SessionEngine {

	/**
	 * Schedule Types
	 */
	const SCHEDULE_FIXED_RECURRING = 'fixed_recurring';
	const SCHEDULE_JUST_IN_TIME   = 'just_in_time';
	const SCHEDULE_INSTANT        = 'instant';

	/**
	 * Calculate Next Available Session Start Time
	 *
	 * @param string $schedule_type Schedule type.
	 * @param array  $config Schedule configuration parameters.
	 * @param int|null $current_timestamp UTC Unix timestamp for calculation baseline.
	 * @return int UTC Unix timestamp of session start.
	 */
	public static function calculate_next_start_time( $schedule_type, array $config, $current_timestamp = null ) {
		$now = null !== $current_timestamp ? (int) $current_timestamp : time();

		switch ( $schedule_type ) {
			case self::SCHEDULE_JUST_IN_TIME:
				$interval_minutes = isset( $config['interval_minutes'] ) ? max( 1, (int) $config['interval_minutes'] ) : 15;
				$interval_seconds = $interval_minutes * 60;

				$remainder = $now % $interval_seconds;
				return $now + ( $interval_seconds - $remainder );

			case self::SCHEDULE_INSTANT:
				$delay_seconds = isset( $config['delay_seconds'] ) ? max( 0, (int) $config['delay_seconds'] ) : 120; // Default 2 min
				return $now + $delay_seconds;

			case self::SCHEDULE_FIXED_RECURRING:
			default:
				$times = isset( $config['daily_times'] ) && is_array( $config['daily_times'] ) ? $config['daily_times'] : array( '14:00', '20:00' );
				sort( $times );

				$today_date = date( 'Y-m-d', $now );

				foreach ( $times as $time_str ) {
					$candidate = strtotime( $today_date . ' ' . $time_str . ' UTC' );
					if ( $candidate > $now ) {
						return $candidate;
					}
				}

				// If no time left today, pick first time tomorrow
				$tomorrow_date = date( 'Y-m-d', $now + 86400 );
				return strtotime( $tomorrow_date . ' ' . $times[0] . ' UTC' );
		}
	}

	/**
	 * Evaluate Full Session Status
	 *
	 * @param int $scheduled_start_time UTC timestamp of session start.
	 * @param int $duration_seconds Total webinar video duration.
	 * @param int|null $current_time UTC timestamp override.
	 * @return array Session state payload.
	 */
	public static function evaluate_status( $scheduled_start_time, $duration_seconds, $current_time = null ) {
		$now = null !== $current_time ? (int) $current_time : time();
		$start = (int) $scheduled_start_time;
		$duration = (int) $duration_seconds;

		$elapsed = $now - $start;

		if ( $elapsed < 0 ) {
			return array(
				'state'            => 'waiting_room',
				'elapsed_seconds'  => 0,
				'waiting_seconds'  => abs( $elapsed ),
				'can_play'         => false,
				'server_timestamp' => $now,
			);
		}

		if ( $elapsed < $duration ) {
			return array(
				'state'            => 'live',
				'elapsed_seconds'  => $elapsed,
				'waiting_seconds'  => 0,
				'can_play'         => true,
				'server_timestamp' => $now,
			);
		}

		return array(
			'state'            => 'ended',
			'elapsed_seconds'  => $duration,
			'waiting_seconds'  => 0,
			'can_play'         => false,
			'server_timestamp' => $now,
		);
	}
}
