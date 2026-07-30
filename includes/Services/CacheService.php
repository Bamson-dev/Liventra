<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\CacheServiceInterface;
use Liventra\Contracts\Repositories\PerformanceRepositoryInterface;
use Liventra\Entities\CacheEntry;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class CacheService implements CacheServiceInterface {

	private $repository;
	private $l1Memory = array();

	public function __construct( PerformanceRepositoryInterface $repository = null ) {
		$this->repository = $repository;
	}

	public function cache( string $key, $value, int $ttl = 3600, array $tags = array() ): bool {
		$entry = new CacheEntry( $key, $value, $ttl, $tags );
		$this->l1Memory[ $key ] = $entry;

		if ( $this->repository ) {
			$this->repository->saveCacheEntry( $entry );
		}
		return true;
	}

	public function remember( string $key, callable $callback, int $ttl = 3600, array $tags = array() ) {
		if ( isset( $this->l1Memory[ $key ] ) && ! $this->l1Memory[ $key ]->isExpired() ) {
			EventBus::dispatch( 'performance.cache.hit', array( 'key' => $key ) );
			return $this->l1Memory[ $key ]->value();
		}

		$entry = $this->repository ? $this->repository->findCacheEntry( $key ) : null;
		if ( $entry && ! $entry->isExpired() ) {
			$this->l1Memory[ $key ] = $entry;
			EventBus::dispatch( 'performance.cache.hit', array( 'key' => $key ) );
			return $entry->value();
		}

		EventBus::dispatch( 'performance.cache.miss', array( 'key' => $key ) );
		$value = call_user_func( $callback );
		$this->cache( $key, $value, $ttl, $tags );
		return $value;
	}

	public function forget( string $key ): bool {
		unset( $this->l1Memory[ $key ] );
		if ( $this->repository ) {
			$this->repository->deleteCacheEntry( $key );
		}
		EventBus::dispatch( 'performance.cache.invalidated', array( 'key' => $key ) );
		return true;
	}

	public function flush( array $tags = array() ): bool {
		$this->l1Memory = array();
		return true;
	}
}
