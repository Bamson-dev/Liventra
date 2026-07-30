<?php
namespace Liventra\Events;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class EventResult
 * Result object returned by Event Handlers (Part 8)
 */
class EventResult {

	private $eventUuid;
	private $status; // 'executed' | 'skipped' | 'failed'
	private $payload;
	private $durationMs;
	private $errorMessage;

	public function __construct(
		string $eventUuid,
		string $status = 'executed',
		array $payload = array(),
		float $durationMs = 0.0,
		?string $errorMessage = null
	) {
		$this->eventUuid    = $eventUuid;
		$this->status       = $status;
		$this->payload      = $payload;
		$this->durationMs   = $durationMs;
		$this->errorMessage = $errorMessage;
	}

	public function getEventUuid(): string { return $this->eventUuid; }
	public function getStatus(): string { return $this->status; }
	public function getPayload(): array { return $this->payload; }
	public function getDurationMs(): float { return $this->durationMs; }
	public function getErrorMessage(): ?string { return $this->errorMessage; }

	public function isSuccess(): bool { return 'executed' === $this->status; }
	public function isFailed(): bool { return 'failed' === $this->status; }

	public static function success( string $eventUuid, array $payload = array(), float $durationMs = 0.0 ): self {
		return new self( $eventUuid, 'executed', $payload, $durationMs );
	}

	public static function failure( string $eventUuid, string $error, float $durationMs = 0.0 ): self {
		return new self( $eventUuid, 'failed', array(), $durationMs, $error );
	}
}
