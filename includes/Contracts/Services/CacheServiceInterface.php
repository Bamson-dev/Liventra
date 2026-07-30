<?php
namespace Liventra\Contracts\Services;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface CacheServiceInterface
 * Public Contract for L1/L2/L3 Multi-Level Caching (PRD-017 Part 1 & 5)
 */
interface CacheServiceInterface {

	public function cache( string $key, $value, int $ttl = 3600, array $tags = array() ): bool;
	public function remember( string $key, callable $callback, int $ttl = 3600, array $tags = array() );
	public function forget( string $key ): bool;
	public function flush( array $tags = array() ): bool;
}
