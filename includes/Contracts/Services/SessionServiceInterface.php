<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Session;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface SessionServiceInterface
 * Public Contract for Authoritative Session Engine (PRD-004 Section 18)
 */
interface SessionServiceInterface {

	/**
	 * Resolve active session for a webinar given schedule config
	 *
	 * @param int    $webinarId Webinar ID.
	 * @param string $scheduleType 'fixed' | 'jit' | 'instant'.
	 * @param array  $config Schedule config parameters.
	 * @param int|null $currentTimestamp UTC Unix timestamp.
	 * @return array Session metadata.
	 */
	public function resolveActiveSession( int $webinarId, string $scheduleType, array $config, ?int $currentTimestamp = null ): array;

	/**
	 * Evaluate full session status state machine
	 *
	 * @param int $scheduledStart UTC Unix timestamp.
	 * @param int $durationSeconds Total video duration in seconds.
	 * @param int|null $currentTimestamp Current server UTC Unix timestamp.
	 * @return array Session state evaluation.
	 */
	public function evaluateSessionState( int $scheduledStart, int $durationSeconds, ?int $currentTimestamp = null ): array;

	/**
	 * Calculate authoritative playback offset (Late join rule: offset = max(0, server_now - start))
	 *
	 * @param int $scheduledStart UTC Unix timestamp.
	 * @param int|null $currentTimestamp Server UTC Unix timestamp.
	 * @return int Elapsed playback seconds.
	 */
	public function calculatePlaybackOffset( int $scheduledStart, ?int $currentTimestamp = null ): int;

	/**
	 * Synchronize attendee heartbeat and return drift correction payload
	 *
	 * @param string $attendeeToken Attendee authentication token.
	 * @param int    $scheduledStart UTC start timestamp.
	 * @param int    $durationSeconds Total duration.
	 * @param int    $clientElapsed Current client elapsed seconds.
	 * @param int|null $currentTimestamp Server UTC timestamp.
	 * @return array Sync payload.
	 */
	public function synchronizeAttendee( string $attendeeToken, int $scheduledStart, int $durationSeconds, int $clientElapsed, ?int $currentTimestamp = null ): array;

	/**
	 * Handle attendee reconnection calculation
	 *
	 * @param string $attendeeToken Attendee authentication token.
	 * @param int    $scheduledStart UTC start timestamp.
	 * @param int    $durationSeconds Total duration.
	 * @param int|null $currentTimestamp Server UTC timestamp.
	 * @return array Reconnection sync payload.
	 */
	public function handleReconnect( string $attendeeToken, int $scheduledStart, int $durationSeconds, ?int $currentTimestamp = null ): array;
}
