<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class QueueStatistics {

	private $pendingJobs;
	private $failedJobs;
	private $processedJobs;

	public function __construct( int $pendingJobs = 0, int $failedJobs = 0, int $processedJobs = 0 ) {
		$this->pendingJobs   = $pendingJobs;
		$this->failedJobs    = $failedJobs;
		$this->processedJobs = $processedJobs;
	}

	public function pendingJobs(): int { return $this->pendingJobs; }
	public function failedJobs(): int { return $this->failedJobs; }
	public function processedJobs(): int { return $this->processedJobs; }
}
