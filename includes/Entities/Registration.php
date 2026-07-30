<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Registration
 * Domain Entity representing a Webinar Registration Record (PRD-008 Part 2 & 5)
 */
class Registration {

	// Lifecycle constants (PRD-008 Part 5)
	const STATUS_PENDING   = 'pending';
	const STATUS_CONFIRMED = 'confirmed';
	const STATUS_WAITING   = 'waiting';
	const STATUS_ADMITTED  = 'admitted';
	const STATUS_JOINED    = 'joined';
	const STATUS_COMPLETED = 'completed';
	const STATUS_CANCELLED = 'cancelled';
	const STATUS_EXPIRED   = 'expired';
	const STATUS_REJECTED  = 'rejected';

	private $registrationId;
	private $webinarId;
	private $attendeeId;
	private $email;
	private $firstName;
	private $lastName;
	private $status;
	private $registeredAt;

	public function __construct(
		int $registrationId,
		int $webinarId,
		int $attendeeId,
		string $email,
		string $firstName = '',
		string $lastName = '',
		string $status = self::STATUS_CONFIRMED,
		?\DateTimeImmutable $registeredAt = null
	) {
		$this->registrationId = $registrationId;
		$this->webinarId       = $webinarId;
		$this->attendeeId      = $attendeeId;
		$this->email           = strtolower( trim( $email ) );
		$this->firstName       = $firstName;
		$this->lastName        = $lastName;
		$this->status          = $status;
		$this->registeredAt   = null !== $registeredAt ? $registeredAt : new \DateTimeImmutable();
	}

	public function getRegistrationId(): int { return $this->registrationId; }
	public function getWebinarId(): int { return $this->webinarId; }
	public function getAttendeeId(): int { return $this->attendeeId; }
	public function getEmail(): string { return $this->email; }
	public function getFirstName(): string { return $this->firstName; }
	public function getLastName(): string { return $this->lastName; }
	public function getStatus(): string { return $this->status; }
	public function getRegisteredAt(): \DateTimeImmutable { return $this->registeredAt; }

	public function isConfirmed(): bool { return self::STATUS_CONFIRMED === $this->status || self::STATUS_JOINED === $this->status; }
	public function isCancelled(): bool { return self::STATUS_CANCELLED === $this->status; }

	public function toArray(): array {
		return array(
			'registration_id' => $this->registrationId,
			'webinar_id'      => $this->webinarId,
			'attendee_id'     => $this->attendeeId,
			'email'           => $this->email,
			'first_name'      => $this->firstName,
			'last_name'       => $this->lastName,
			'status'          => $this->status,
			'registered_at'   => $this->registeredAt->format( \DateTimeInterface::ATOM ),
		);
	}
}
