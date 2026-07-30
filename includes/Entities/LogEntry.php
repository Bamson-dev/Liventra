<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class LogEntry
 * Domain Entity representing Structured Telemetry Log (PRD-016 Part 2 & 5)
 */
class LogEntry {

	private $logId;
	private $timestamp;
	private $correlationId;
	private $requestId;
	private $level;
	private $message;
	private $context;

	public function __construct(
		string $logId,
		string $correlationId,
		string $requestId = '',
		string $level = 'info',
		string $message = '',
		array $context = array()
	) {
		$this->logId         = $logId;
		$this->timestamp     = microtime( true );
		$this->correlationId = $correlationId;
		$this->requestId     = $requestId;
		$this->level         = strtolower( $level );
		$this->message       = $message;
		$this->context       = $context;
	}

	public function logId(): string { return $this->logId; }
	public function timestamp(): float { return $this->timestamp; }
	public function correlationId(): string { return $this->correlationId; }
	public function requestId(): string { return $this->requestId; }
	public function level(): string { return $this->level; }
	public function message(): string { return $this->message; }
	public function context(): array { return $this->context; }
}
