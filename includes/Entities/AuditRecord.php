<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class AuditRecord
 * Domain Entity representing Immutable Security Audit Records (PRD-013 Part 2 & 11)
 */
class AuditRecord {

	private $recordId;
	private $action;
	private $actorId;
	private $target;
	private $details;
	private $timestamp;

	public function __construct(
		int $recordId,
		string $action,
		int $actorId,
		string $target,
		array $details = array(),
		?\DateTimeImmutable $timestamp = null
	) {
		$this->recordId  = $recordId;
		$this->action    = strtolower( $action );
		$this->actorId   = $actorId;
		$this->target    = $target;
		$this->details   = $details;
		$this->timestamp = null !== $timestamp ? $timestamp : new \DateTimeImmutable();
	}

	public function getRecordId(): int { return $this->recordId; }
	public function getAction(): string { return $this->action; }
	public function getActorId(): int { return $this->actorId; }
	public function getTarget(): string { return $this->target; }
	public function getDetails(): array { return $this->details; }
	public function getTimestamp(): \DateTimeImmutable { return $this->timestamp; }
}
