<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class WaitingRoomState
 * Domain Entity representing Waiting Room Status (PRD-008 Part 2 & 7)
 */
class WaitingRoomState {

	private $webinarId;
	private $attendeeId;
	private $status; // 'waiting' | 'admitted' | 'rejected'
	private $remainingSeconds;
	private $sessionStatus; // 'waiting' | 'live' | 'ended'

	public function __construct(
		int $webinarId,
		int $attendeeId,
		string $status = 'waiting',
		int $remainingSeconds = 0,
		string $sessionStatus = 'waiting'
	) {
		$this->webinarId        = $webinarId;
		$this->attendeeId       = $attendeeId;
		$this->status           = $status;
		$this->remainingSeconds = max( 0, $remainingSeconds );
		$this->sessionStatus    = $sessionStatus;
	}

	public function getWebinarId(): int { return $this->webinarId; }
	public function getAttendeeId(): int { return $this->attendeeId; }
	public function getStatus(): string { return $this->status; }
	public function getRemainingSeconds(): int { return $this->remainingSeconds; }
	public function getSessionStatus(): string { return $this->sessionStatus; }

	public function isAdmitted(): bool { return 'admitted' === $this->status || 'live' === $this->sessionStatus; }

	public function toArray(): array {
		return array(
			'webinar_id'        => $this->webinarId,
			'attendee_id'       => $this->attendeeId,
			'status'            => $this->status,
			'remaining_seconds' => $this->remainingSeconds,
			'session_status'    => $this->sessionStatus,
			'is_admitted'       => $this->isAdmitted(),
		);
	}
}
