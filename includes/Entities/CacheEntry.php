<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class CacheEntry
 * Domain Entity representing L1/L2 Multi-level Cache Item (PRD-017 Part 2 & 5)
 */
class CacheEntry {

	private $key;
	private $value;
	private $ttl;
	private $tags;
	private $createdAt;

	public function __construct( string $key, $value, int $ttl = 3600, array $tags = array() ) {
		$this->key       = $key;
		$this->value     = $value;
		$this->ttl       = $ttl;
		$this->tags      = $tags;
		$this->createdAt = time();
	}

	public function key(): string { return $this->key; }
	public function value() { return $this->value; }
	public function ttl(): int { return $this->ttl; }
	public function tags(): array { return $this->tags; }
	public function isExpired(): bool { return time() > ( $this->createdAt + $this->ttl ); }
}
