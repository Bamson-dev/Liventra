<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class Worker {

	private $workerId;
	private $queueName;
	private $status;
	private $processedCount;
	private $lastHeartbeat;

	public function __construct( string $workerId, string $queueName = 'default', string $status = 'active', int $processedCount = 0 ) {
		$this->workerId       = $workerId;
		$this->queueName     = $queueName;
		$this->status        = $status;
		$this->processedCount= $processedCount;
		$this->lastHeartbeat = time();
	}

	public function workerId(): string { return $this->workerId; }
	public function queueName(): string { return $this->queueName; }
	public function status(): string { return $this->status; }
	public function processedCount(): int { return $this->processedCount; }
	public function lastHeartbeat(): int { return $this->lastHeartbeat; }
}
