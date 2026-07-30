<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class AttendeeIdentity
 * Domain Entity representing an Attendee Identity Profile (PRD-008 Part 2)
 */
class AttendeeIdentity {

	private $attendeeId;
	private $email;
	private $firstName;
	private $lastName;
	private $ipAddress;
	private $userAgent;

	public function __construct(
		int $attendeeId,
		string $email,
		string $firstName = '',
		string $lastName = '',
		?string $ipAddress = null,
		?string $userAgent = null
	) {
		$this->attendeeId = $attendeeId;
		$this->email      = strtolower( trim( $email ) );
		$this->firstName  = $firstName;
		$this->lastName   = $lastName;
		$this->ipAddress  = $ipAddress;
		$this->userAgent  = $userAgent;
	}

	public function getAttendeeId(): int { return $this->attendeeId; }
	public function getEmail(): string { return $this->email; }
	public function getFirstName(): string { return $this->firstName; }
	public function getLastName(): string { return $this->lastName; }
	public function getIpAddress(): ?string { return $this->ipAddress; }
	public function getUserAgent(): ?string { return $this->userAgent; }

	public function getDisplayName(): string {
		return trim( $this->firstName . ' ' . $this->lastName ) ?: $this->email;
	}
}
