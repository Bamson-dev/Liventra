<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class SlowRequest {

	private $route;
	private $duration;
	private $threshold;

	public function __construct( string $route, float $duration, float $threshold = 1.0 ) {
		$this->route     = $route;
		$this->duration  = $duration;
		$this->threshold = $threshold;
	}

	public function route(): string { return $this->route; }
	public function duration(): float { return $this->duration; }
	public function threshold(): float { return $this->threshold; }
}
