<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SimulationEvent
 * Domain Entity representing a synchronized Live Simulation Event (PRD-005)
 */
class SimulationEvent {

	// Priority constants (PRD-005 Section 15)
	const PRIORITY_CRITICAL     = 100;
	const PRIORITY_CTA          = 90;
	const PRIORITY_POLL         = 80;
	const PRIORITY_PURCHASE     = 70;
	const PRIORITY_SCARCITY     = 60;
	const PRIORITY_CHAT         = 50;
	const PRIORITY_VIEWER_COUNT = 40;
	const PRIORITY_COSMETIC     = 30;

	private $eventId;
	private $webinarId;
	private $eventType; // 'cta', 'poll', 'purchase', 'scarcity', 'chat', 'notification'
	private $triggerSecond;
	private $priority;
	private $payload;

	public function __construct(
		int $eventId,
		int $webinarId,
		string $eventType,
		int $triggerSecond,
		array $payload = array(),
		?int $priority = null
	) {
		$this->eventId       = $eventId;
		$this->webinarId     = $webinarId;
		$this->eventType     = $eventType;
		$this->triggerSecond = $triggerSecond;
		$this->payload       = $payload;
		$this->priority      = null !== $priority ? $priority : $this->defaultPriorityForType( $eventType );
	}

	public function getEventId(): int { return $this->eventId; }
	public function getWebinarId(): int { return $this->webinarId; }
	public function getEventType(): string { return $this->eventType; }
	public function getTriggerSecond(): int { return $this->triggerSecond; }
	public function getPriority(): int { return $this->priority; }
	public function getPayload(): array { return $this->payload; }

	public function isEligible( int $currentOffset, int $lastSyncedOffset = 0 ): bool {
		return $this->triggerSecond <= $currentOffset && $this->triggerSecond > $lastSyncedOffset;
	}

	private function defaultPriorityForType( string $type ): int {
		switch ( $type ) {
			case 'cta':
				return self::PRIORITY_CTA;
			case 'poll':
				return self::PRIORITY_POLL;
			case 'purchase':
			case 'notification':
				return self::PRIORITY_PURCHASE;
			case 'scarcity':
			case 'countdown':
				return self::PRIORITY_SCARCITY;
			case 'chat':
				return self::PRIORITY_CHAT;
			case 'viewer_count':
				return self::PRIORITY_VIEWER_COUNT;
			default:
				return self::PRIORITY_COSMETIC;
		}
	}

	public function toArray(): array {
		return array(
			'event_id'       => $this->eventId,
			'webinar_id'     => $this->webinarId,
			'event_type'     => $this->eventType,
			'trigger_second' => $this->triggerSecond,
			'priority'       => $this->priority,
			'payload'        => $this->payload,
		);
	}
}
