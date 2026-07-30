<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class TimelineEvent
 * Strongly typed Domain Entity representing a Timeline Event (PRD-006 Part 2)
 */
class TimelineEvent {

	private $uuid;
	private $eventId;
	private $webinarId;
	private $triggerSecond;
	private $priority;
	private $payload;
	private $replayable;
	private $enabled;
	private $version;
	private $eventType;
	private $dependencies; // array of required parent UUIDs
	private $createdAt;

	public function __construct(
		string $uuid,
		int $eventId,
		int $webinarId,
		string $eventType,
		int $triggerSecond,
		array $payload = array(),
		bool $replayable = true,
		bool $enabled = true,
		int $priority = 50,
		int $version = 1,
		array $dependencies = array(),
		?\DateTimeImmutable $createdAt = null
	) {
		$this->uuid          = $uuid;
		$this->eventId       = $eventId;
		$this->webinarId     = $webinarId;
		$this->eventType     = $eventType;
		$this->triggerSecond = $triggerSecond;
		$this->payload       = $payload;
		$this->replayable    = $replayable;
		$this->enabled       = $enabled;
		$this->priority      = $priority;
		$this->version       = $version;
		$this->dependencies  = $dependencies;
		$this->createdAt    = null !== $createdAt ? $createdAt : new \DateTimeImmutable();
	}

	// Methods specified in PRD-006 Part 2
	public function isEligible( int $currentOffset, int $lastSyncedOffset = 0 ): bool {
		return $this->enabled && $this->triggerSecond <= $currentOffset && $this->triggerSecond > $lastSyncedOffset;
	}

	public function isReplayable(): bool { return $this->replayable; }
	public function isEnabled(): bool { return $this->enabled; }
	public function priorityWeight(): int { return $this->priority; }
	public function triggerOffset(): int { return $this->triggerSecond; }
	public function version(): int { return $this->version; }
	public function payload(): array { return $this->payload; }
	public function uuid(): string { return $this->uuid; }
	public function eventId(): int { return $this->eventId; }
	public function webinarId(): int { return $this->webinarId; }
	public function eventType(): string { return $this->eventType; }
	public function dependencies(): array { return $this->dependencies; }
	public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

	public function toArray(): array {
		return array(
			'uuid'           => $this->uuid,
			'event_id'       => $this->eventId,
			'webinar_id'     => $this->webinarId,
			'event_type'     => $this->eventType,
			'trigger_second' => $this->triggerSecond,
			'priority'       => $this->priority,
			'replayable'     => $this->replayable,
			'enabled'        => $this->enabled,
			'version'        => $this->version,
			'dependencies'   => $this->dependencies,
			'payload'        => $this->payload,
		);
	}
}
