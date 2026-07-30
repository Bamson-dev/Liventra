<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class CacheStatistics {

	private $hits;
	private $misses;
	private $keysCount;

	public function __construct( int $hits = 0, int $misses = 0, int $keysCount = 0 ) {
		$this->hits      = $hits;
		$this->misses    = $misses;
		$this->keysCount = $keysCount;
	}

	public function hits(): int { return $this->hits; }
	public function misses(): int { return $this->misses; }
	public function keysCount(): int { return $this->keysCount; }
	public function hitRatio(): float {
		$total = $this->hits + $this->misses;
		return $total > 0 ? ( $this->hits / $total ) * 100.0 : 0.0;
	}
}
