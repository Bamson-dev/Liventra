<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Session
 * Domain Entity representing a Webinar Session (PRD-003 & PRD-004)
 */
class Session {

	private $sessionId;
	private $webinarId;
	private $sessionUuid;
	private $scheduledStart; // \DateTimeImmutable
	private $scheduledEnd;   // \DateTimeImmutable
	private $status;         // 'waiting' | 'live' | 'ended' | 'cancelled'
	private $attendeeCount;

	public function __construct(
		int $sessionId,
		int $webinarId,
		string $sessionUuid,
		\DateTimeImmutable $scheduledStart,
		\DateTimeImmutable $scheduledEnd,
		string $status = 'waiting',
		int $attendeeCount = 0
	) {
		$this->sessionId      = $sessionId;
		$this->webinarId      = $webinarId;
		$this->sessionUuid     = $sessionUuid;
		$this->scheduledStart = $scheduledStart;
		$this->scheduledEnd   = $scheduledEnd;
		$this->status         = $status;
		$this->attendeeCount  = $attendeeCount;
	}

	// Getters
	public function getSessionId(): int { return $this->sessionId; }
	public function getWebinarId(): int { return $this->webinarId; }
	public function getSessionUuid(): string { return $this->sessionUuid; }
	public function getScheduledStart(): \DateTimeImmutable { return $this->scheduledStart; }
	public function getScheduledEnd(): \DateTimeImmutable { return $this->scheduledEnd; }
	public function getStatus(): string { return $this->status; }
	public function getAttendeeCount(): int { return $this->attendeeCount; }

	// Domain Logic Methods (PRD-004 State Machine)
	public function isWaiting( \DateTimeImmutable $now ): bool {
		return $now < $this->scheduledStart && 'cancelled' !== $this->status;
	}

	public function isLive( \DateTimeImmutable $now, int $durationSeconds ): bool {
		$elapsed = $now->getTimestamp() - $this->scheduledStart->getTimestamp();
		return $elapsed >= 0 && $elapsed < $durationSeconds && 'cancelled' !== $this->status && 'ended' !== $this->status;
	}

	public function hasEnded( \DateTimeImmutable $now, int $durationSeconds ): bool {
		$elapsed = $now->getTimestamp() - $this->scheduledStart->getTimestamp();
		return $elapsed >= $durationSeconds || 'ended' === $this->status;
	}

	public function elapsedSeconds( \DateTimeImmutable $now ): int {
		return max( 0, $now->getTimestamp() - $this->scheduledStart->getTimestamp() );
	}

	public function remainingWaitingSeconds( \DateTimeImmutable $now ): int {
		return max( 0, $this->scheduledStart->getTimestamp() - $now->getTimestamp() );
	}

	public function canJoin( \DateTimeImmutable $now, int $durationSeconds ): bool {
		return ! $this->hasEnded( $now, $durationSeconds ) && 'cancelled' !== $this->status;
	}

	/**
	 * Convert entity to array payload
	 *
	 * @return array
	 */
	public function toArray(): array {
		return array(
			'session_id'      => $this->sessionId,
			'webinar_id'      => $this->webinarId,
			'session_uuid'    => $this->sessionUuid,
			'scheduled_start' => $this->scheduledStart->format( 'Y-m-d H:i:s' ),
			'scheduled_end'   => $this->scheduledEnd->format( 'Y-m-d H:i:s' ),
			'status'          => $this->status,
			'attendee_count'  => $this->attendeeCount,
		);
	}
}
