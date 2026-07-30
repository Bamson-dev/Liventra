<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class EventExecution
 * Domain Entity representing the execution result of a Timeline Event (PRD-006 Part 5)
 */
class EventExecution {

	private $executionId;
	private $eventUuid;
	private $attendeeId;
	private $status; // 'executed' | 'skipped' | 'failed'
	private $executedAt;

	public function __construct(
		int $executionId,
		string $eventUuid,
		int $attendeeId,
		string $status = 'executed',
		?\DateTimeImmutable $executedAt = null
	) {
		$this->executionId = $executionId;
		$this->eventUuid   = $eventUuid;
		$this->attendeeId  = $attendeeId;
		$this->status      = $status;
		$this->executedAt  = null !== $executedAt ? $executedAt : new \DateTimeImmutable();
	}

	public function getExecutionId(): int { return $this->executionId; }
	public function getEventUuid(): string { return $this->eventUuid; }
	public function getAttendeeId(): int { return $this->attendeeId; }
	public function getStatus(): string { return $this->status; }
	public function isSuccess(): bool { return 'executed' === $this->status; }
}
