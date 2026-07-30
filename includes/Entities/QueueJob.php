<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class QueueJob {

	private $jobId;
	private $queueName;
	private $payload;
	private $attempts;
	private $status; // 'pending' | 'processing' | 'completed' | 'failed'
	private $availableAt;

	public function __construct(
		string $jobId,
		string $queueName,
		array $payload,
		int $attempts = 0,
		string $status = 'pending',
		?int $availableAt = null
	) {
		$this->jobId       = $jobId;
		$this->queueName   = strtolower( $queueName );
		$this->payload     = $payload;
		$this->attempts    = $attempts;
		$this->status      = $status;
		$this->availableAt = null !== $availableAt ? $availableAt : time();
	}

	public function jobId(): string { return $this->jobId; }
	public function queueName(): string { return $this->queueName; }
	public function payload(): array { return $this->payload; }
	public function attempts(): int { return $this->attempts; }
	public function status(): string { return $this->status; }
	public function availableAt(): int { return $this->availableAt; }
}
