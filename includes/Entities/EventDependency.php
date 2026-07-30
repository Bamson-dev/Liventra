<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class EventDependency
 * Domain Entity representing an Event Dependency Rule (PRD-006 Part 7)
 */
class EventDependency {

	private $eventUuid;
	private $parentUuid;

	public function __construct( string $eventUuid, string $parentUuid ) {
		if ( $eventUuid === $parentUuid ) {
			throw new \InvalidArgumentException( 'Self-dependency is forbidden.' );
		}
		$this->eventUuid  = $eventUuid;
		$this->parentUuid = $parentUuid;
	}

	public function getEventUuid(): string { return $this->eventUuid; }
	public function getParentUuid(): string { return $this->parentUuid; }
}
