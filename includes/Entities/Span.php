<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class Span {

	private $spanId;
	private $traceId;
	private $name;
	private $parentSpanId;
	private $startTime;
	private $endTime;

	public function __construct( string $spanId, string $traceId, string $name, string $parentSpanId = '' ) {
		$this->spanId       = $spanId;
		$this->traceId      = $traceId;
		$this->name         = $name;
		$this->parentSpanId = $parentSpanId;
		$this->startTime    = microtime( true );
		$this->endTime      = null;
	}

	public function finish(): void {
		$this->endTime = microtime( true );
	}

	public function spanId(): string { return $this->spanId; }
	public function traceId(): string { return $this->traceId; }
	public function name(): string { return $this->name; }
	public function parentSpanId(): string { return $this->parentSpanId; }
	public function startTime(): float { return $this->startTime; }
	public function endTime(): ?float { return $this->endTime; }
	public function duration(): float {
		return null !== $this->endTime ? ( $this->endTime - $this->startTime ) : ( microtime( true ) - $this->startTime );
	}
}
