<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SecurityEvent
 * Domain Entity representing Security Audit Telemetry (PRD-013 Part 2)
 */
class SecurityEvent {

	private $eventId;
	private $type;
	private $userId;
	private $ipAddress;
	private $payload;
	private $timestamp;

	public function __construct(
		string $eventId,
		string $type,
		int $userId = 0,
		string $ipAddress = '127.0.0.1',
		array $payload = array(),
		?\DateTimeImmutable $timestamp = null
	) {
		$this->eventId   = $eventId;
		$this->type      = strtolower( $type );
		$this->userId    = $userId;
		$this->ipAddress = $ipAddress;
		$this->payload   = $payload;
		$this->timestamp = null !== $timestamp ? $timestamp : new \DateTimeImmutable();
	}

	public function getEventId(): string { return $this->eventId; }
	public function getType(): string { return $this->type; }
	public function getUserId(): int { return $this->userId; }
	public function getIpAddress(): string { return $this->ipAddress; }
	public function getPayload(): array { return $this->payload; }
	public function getTimestamp(): \DateTimeImmutable { return $this->timestamp; }
}
