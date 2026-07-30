<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class HealthStatus {

	private $subsystem;
	private $status; // 'healthy' | 'degraded' | 'unhealthy'
	private $details;
	private $timestamp;

	public function __construct( string $subsystem, string $status = 'healthy', array $details = array() ) {
		$this->subsystem = $subsystem;
		$this->status    = strtolower( $status );
		$this->details   = $details;
		$this->timestamp = time();
	}

	public function subsystem(): string { return $this->subsystem; }
	public function status(): string { return $this->status; }
	public function details(): array { return $this->details; }
}
