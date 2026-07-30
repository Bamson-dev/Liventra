<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class SlowQuery {

	private $query;
	private $executionTime;
	private $threshold;

	public function __construct( string $query, float $executionTime, float $threshold = 0.5 ) {
		$this->query         = $query;
		$this->executionTime = $executionTime;
		$this->threshold     = $threshold;
	}

	public function query(): string { return $this->query; }
	public function executionTime(): float { return $this->executionTime; }
	public function threshold(): float { return $this->threshold; }
}
