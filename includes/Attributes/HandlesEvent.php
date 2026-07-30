<?php
namespace Liventra\Attributes;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Attribute HandlesEvent
 * Declarative Event Registration Attribute (Part 3)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class HandlesEvent {

	private $eventType;
	private $priority;

	public function __construct( string $eventType, int $priority = 50 ) {
		$this->eventType = $eventType;
		$this->priority  = $priority;
	}

	public function getEventType(): string {
		return $this->eventType;
	}

	public function getPriority(): int {
		return $this->priority;
	}
}
