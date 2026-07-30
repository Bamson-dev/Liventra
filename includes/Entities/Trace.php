<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class Trace {

	private $traceId;
	private $correlationId;
	private $spans;

	public function __construct( string $traceId, string $correlationId, array $spans = array() ) {
		$this->traceId       = $traceId;
		$this->correlationId = $correlationId;
		$this->spans         = $spans;
	}

	public function traceId(): string { return $this->traceId; }
	public function correlationId(): string { return $this->correlationId; }
	public function spans(): array { return $this->spans; }
}
