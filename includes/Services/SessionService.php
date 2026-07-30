<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\SessionServiceInterface;
use Liventra\Contracts\Repositories\SessionRepositoryInterface;
use Liventra\EventBus;
use Liventra\Entities\Session;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SessionService
 * Authoritative Implementation of Session Engine (PRD-004)
 * Uses Dependency Injection and emits EventBus lifecycle events.
 */
class SessionService implements SessionServiceInterface {

	/**
	 * Session Repository Dependency
	 *
	 * @var SessionRepositoryInterface|null
	 */
	private $sessionRepository;

	/**
	 * Constructor Injection
	 *
	 * @param SessionRepositoryInterface|null $sessionRepository Session repository dependency.
	 */
	public function __construct( SessionRepositoryInterface $sessionRepository = null ) {
		$this->sessionRepository = $sessionRepository;
	}

	/**
	 * Resolve active session for a webinar given schedule mode
	 */
	public function resolveActiveSession( int $webinarId, string $scheduleType, array $config, ?int $currentTimestamp = null ): array {
		$now = null !== $currentTimestamp ? $currentTimestamp : time();

		$start_time = \Liventra\Modules\Session\SessionEngine::calculate_next_start_time( $scheduleType, $config, $now );
		$duration   = isset( $config['duration_seconds'] ) ? (int) $config['duration_seconds'] : 3600;

		$end_time = $start_time + $duration;

		$scheduledStart = new \DateTimeImmutable( '@' . $start_time );
		$scheduledEnd   = new \DateTimeImmutable( '@' . $end_time );

		$session = new Session( 1, $webinarId, wp_generate_uuid4(), $scheduledStart, $scheduledEnd, 'waiting' );

		EventBus::dispatch( 'session.waiting', array( 'webinar_id' => $webinarId, 'scheduled_start' => $start_time ) );

		return array_merge( $session->toArray(), array( 'duration_seconds' => $duration ) );
	}

	/**
	 * Evaluate full session status state machine (PRD-004 Section 4 & 6)
	 */
	public function evaluateSessionState( int $scheduledStart, int $durationSeconds, ?int $currentTimestamp = null ): array {
		$now = null !== $currentTimestamp ? $currentTimestamp : time();

		$start_dt = new \DateTimeImmutable( '@' . $scheduledStart );
		$end_dt   = new \DateTimeImmutable( '@' . ( $scheduledStart + $durationSeconds ) );
		$now_dt   = new \DateTimeImmutable( '@' . $now );

		$session = new Session( 1, 1, 'session-uuid', $start_dt, $end_dt, 'waiting' );

		if ( $session->isWaiting( $now_dt ) ) {
			return array(
				'state'                     => 'waiting_room',
				'elapsed_seconds'           => 0,
				'remaining_waiting_seconds' => $session->remainingWaitingSeconds( $now_dt ),
				'server_time'               => $now,
				'can_play'                  => false,
			);
		}

		if ( $session->isLive( $now_dt, $durationSeconds ) ) {
			EventBus::dispatch( 'session.live', array( 'elapsed' => $session->elapsedSeconds( $now_dt ) ) );

			return array(
				'state'                     => 'live',
				'elapsed_seconds'           => $session->elapsedSeconds( $now_dt ),
				'remaining_waiting_seconds' => 0,
				'server_time'               => $now,
				'can_play'                  => true,
			);
		}

		EventBus::dispatch( 'session.ended', array( 'duration' => $durationSeconds ) );

		return array(
			'state'                     => 'ended',
			'elapsed_seconds'           => $durationSeconds,
			'remaining_waiting_seconds' => 0,
			'server_time'               => $now,
			'can_play'                  => false,
		);
	}

	/**
	 * Calculate authoritative playback offset (Late join rule: offset = max(0, server_now - start))
	 */
	public function calculatePlaybackOffset( int $scheduledStart, ?int $currentTimestamp = null ): int {
		$now = null !== $currentTimestamp ? $currentTimestamp : time();
		return max( 0, $now - $scheduledStart );
	}

	/**
	 * Synchronize attendee heartbeat and return drift correction payload (PRD-004 Section 12 & 13)
	 */
	public function synchronizeAttendee( string $attendeeToken, int $scheduledStart, int $durationSeconds, int $clientElapsed, ?int $currentTimestamp = null ): array {
		$statePayload = $this->evaluateSessionState( $scheduledStart, $durationSeconds, $currentTimestamp );

		$serverElapsed = $statePayload['elapsed_seconds'];
		$drift         = abs( $clientElapsed - $serverElapsed );

		$requiresSeek = ( 'live' === $statePayload['state'] && $drift > 2.5 );

		if ( $requiresSeek ) {
			EventBus::dispatch( 'session.resynchronized', array(
				'token'          => $attendeeToken,
				'drift_seconds'  => $drift,
				'server_elapsed' => $serverElapsed,
			) );
		}

		return array_merge( $statePayload, array(
			'drift_seconds' => $drift,
			'requires_seek' => $requiresSeek,
		) );
	}

	/**
	 * Handle attendee reconnection calculation (PRD-004 Section 11)
	 */
	public function handleReconnect( string $attendeeToken, int $scheduledStart, int $durationSeconds, ?int $currentTimestamp = null ): array {
		EventBus::dispatch( 'session.reconnected', array( 'token' => $attendeeToken ) );
		return $this->evaluateSessionState( $scheduledStart, $durationSeconds, $currentTimestamp );
	}
}
