<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\PerformanceRepositoryInterface;
use Liventra\Entities\CacheEntry;
use Liventra\Entities\QueueJob;
use Liventra\Entities\Worker;
use Liventra\Entities\BenchmarkResult;
use Liventra\Entities\CapacityReport;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class PerformanceRepository
 * Persistence implementation for Runtime Optimization Platform (PRD-003 & PRD-017)
 */
class PerformanceRepository implements PerformanceRepositoryInterface {

	private $inMemoryCache      = array();
	private $inMemoryQueue      = array();
	private $inMemoryWorkers    = array();
	private $inMemoryBenchmarks = array();
	private $inMemoryCapacity   = array();

	public function saveCacheEntry( CacheEntry $entry ): bool {
		$this->inMemoryCache[ $entry->key() ] = $entry;
		return true;
	}

	public function findCacheEntry( string $key ): ?CacheEntry {
		return isset( $this->inMemoryCache[ $key ] ) ? $this->inMemoryCache[ $key ] : null;
	}

	public function deleteCacheEntry( string $key ): bool {
		unset( $this->inMemoryCache[ $key ] );
		return true;
	}

	public function saveQueueJob( QueueJob $job ): bool {
		$this->inMemoryQueue[ $job->jobId() ] = $job;
		return true;
	}

	public function saveWorker( Worker $worker ): bool {
		$this->inMemoryWorkers[ $worker->workerId() ] = $worker;
		return true;
	}

	public function saveBenchmarkResult( BenchmarkResult $result ): bool {
		$this->inMemoryBenchmarks[] = $result;
		return true;
	}

	public function saveCapacityReport( CapacityReport $report ): bool {
		$this->inMemoryCapacity[ $report->reportId() ] = $report;
		return true;
	}
}
