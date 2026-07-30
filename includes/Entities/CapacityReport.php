<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class CapacityReport {

	private $reportId;
	private $maxConcurrentAttendees;
	private $recommendedWorkers;
	private $estimatedBandwidthGb;

	public function __construct( string $reportId, int $maxConcurrentAttendees, int $recommendedWorkers, float $estimatedBandwidthGb ) {
		$this->reportId               = $reportId;
		$this->maxConcurrentAttendees = $maxConcurrentAttendees;
		$this->recommendedWorkers     = $recommendedWorkers;
		$this->estimatedBandwidthGb   = $estimatedBandwidthGb;
	}

	public function reportId(): string { return $this->reportId; }
	public function maxConcurrentAttendees(): int { return $this->maxConcurrentAttendees; }
	public function recommendedWorkers(): int { return $this->recommendedWorkers; }
	public function estimatedBandwidthGb(): float { return $this->estimatedBandwidthGb; }
}
