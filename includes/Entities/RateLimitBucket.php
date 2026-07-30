<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class RateLimitBucket
 * Domain Entity representing Token Bucket Rate Limiting (PRD-013 Part 2 & 9)
 */
class RateLimitBucket {

	private $key;
	private $tokens;
	private $capacity;
	private $lastRefilled;

	public function __construct( string $key, float $tokens, int $capacity = 60, ?int $lastRefilled = null ) {
		$this->key          = $key;
		$this->tokens       = $tokens;
		$this->capacity     = $capacity;
		$this->lastRefilled = null !== $lastRefilled ? $lastRefilled : time();
	}

	public function getKey(): string { return $this->key; }
	public function getTokens(): float { return $this->tokens; }
	public function getCapacity(): int { return $this->capacity; }
	public function getLastRefilled(): int { return $this->lastRefilled; }
}
