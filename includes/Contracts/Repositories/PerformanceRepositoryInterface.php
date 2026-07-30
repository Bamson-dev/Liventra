<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\CacheEntry;
use Liventra\Entities\QueueJob;
use Liventra\Entities\Worker;
use Liventra\Entities\BenchmarkResult;
use Liventra\Entities\CapacityReport;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface PerformanceRepositoryInterface
 * Persistence contract for Runtime Optimization Platform (PRD-017 Part 3)
 */
interface PerformanceRepositoryInterface {

	public function saveCacheEntry( CacheEntry $entry ): bool;
	public function findCacheEntry( string $key ): ?CacheEntry;
	public function deleteCacheEntry( string $key ): bool;
	public function saveQueueJob( QueueJob $job ): bool;
	public function saveWorker( Worker $worker ): bool;
	public function saveBenchmarkResult( BenchmarkResult $result ): bool;
	public function saveCapacityReport( CapacityReport $report ): bool;
}
