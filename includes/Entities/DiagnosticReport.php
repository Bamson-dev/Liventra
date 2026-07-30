<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class DiagnosticReport {

	private $reportId;
	private $summary;
	private $metrics;
	private $health;
	private $timestamp;

	public function __construct( string $reportId, string $summary, array $metrics = array(), array $health = array() ) {
		$this->reportId  = $reportId;
		$this->summary   = $summary;
		$this->metrics   = $metrics;
		$this->health    = $health;
		$this->timestamp = time();
	}

	public function reportId(): string { return $this->reportId; }
	public function summary(): string { return $this->summary; }
	public function metrics(): array { return $this->metrics; }
	public function health(): array { return $this->health; }
}
