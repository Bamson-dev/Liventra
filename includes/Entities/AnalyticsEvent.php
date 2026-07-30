<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class AnalyticsEvent
 * Domain Entity representing a Normalized Intelligence Analytics Event (PRD-011 Part 2)
 */
class AnalyticsEvent {

	private $uuid;
	private $attendeeId;
	private $webinarId;
	private $sessionId;
	private $eventType;
	private $payload;
	private $source;
	private $timestamp;

	public function __construct(
		string $uuid,
		int $webinarId,
		int $attendeeId,
		string $eventType,
		array $payload = array(),
		int $sessionId = 0,
		string $source = 'server',
		?\DateTimeImmutable $timestamp = null
	) {
		$this->uuid       = $uuid;
		$this->webinarId  = $webinarId;
		$this->attendeeId = $attendeeId;
		$this->eventType  = strtolower( $eventType );
		$this->payload    = $payload;
		$this->sessionId  = $sessionId;
		$this->source     = $source;
		$this->timestamp  = null !== $timestamp ? $timestamp : new \DateTimeImmutable();
	}

	public function uuid(): string { return $this->uuid; }
	public function webinarId(): int { return $this->webinarId; }
	public function attendeeId(): int { return $this->attendeeId; }
	public function sessionId(): int { return $this->sessionId; }
	public function eventType(): string { return $this->eventType; }
	public function payload(): array { return $this->payload; }
	public function source(): string { return $this->source; }
	public function timestamp(): \DateTimeImmutable { return $this->timestamp; }

	public function toArray(): array {
		return array(
			'uuid'        => $this->uuid,
			'webinar_id'  => $this->webinarId,
			'attendee_id' => $this->attendeeId,
			'session_id'  => $this->sessionId,
			'event_type'  => $this->eventType,
			'payload'     => $this->payload,
			'source'      => $this->source,
			'timestamp'   => $this->timestamp->format( \DateTimeInterface::ATOM ),
		);
	}
}
