<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\WorkerServiceInterface;
use Liventra\Contracts\Repositories\PerformanceRepositoryInterface;
use Liventra\Entities\Worker;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class WorkerService implements WorkerServiceInterface {

	private $repository;

	public function __construct( PerformanceRepositoryInterface $repository = null ) {
		$this->repository = $repository;
	}

	public function runWorker( string $queueName ): Worker {
		$workerId = 'wrk_' . wp_generate_uuid4();
		$worker   = new Worker( $workerId, $queueName, 'active', 1 );

		if ( $this->repository ) {
			$this->repository->saveWorker( $worker );
		}

		EventBus::dispatch( 'performance.worker.started', array( 'worker_id' => $workerId, 'queue' => $queueName ) );
		return $worker;
	}
}
