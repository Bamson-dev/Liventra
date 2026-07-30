<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\QueueServiceInterface;
use Liventra\Contracts\Repositories\PerformanceRepositoryInterface;
use Liventra\Entities\QueueJob;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class QueueService implements QueueServiceInterface {

	private $repository;

	public function __construct( PerformanceRepositoryInterface $repository = null ) {
		$this->repository = $repository;
	}

	public function dispatch( string $queueName, array $payload ): QueueJob {
		$jobId = 'job_' . wp_generate_uuid4();
		$job   = new QueueJob( $jobId, $queueName, $payload );

		if ( $this->repository ) {
			$this->repository->saveQueueJob( $job );
		}

		EventBus::dispatch( 'performance.job.dispatched', array( 'job_id' => $jobId, 'queue' => $queueName ) );
		return $job;
	}

	public function dispatchDelayed( string $queueName, array $payload, int $delaySeconds ): QueueJob {
		$jobId = 'job_' . wp_generate_uuid4();
		$job   = new QueueJob( $jobId, $queueName, $payload, 0, 'pending', time() + $delaySeconds );

		if ( $this->repository ) {
			$this->repository->saveQueueJob( $job );
		}

		EventBus::dispatch( 'performance.job.dispatched', array( 'job_id' => $jobId, 'delay' => $delaySeconds ) );
		return $job;
	}

	public function dispatchBatch( string $queueName, array $batchPayloads ): array {
		$jobs = array();
		foreach ( $batchPayloads as $payload ) {
			$jobs[] = $this->dispatch( $queueName, (array) $payload );
		}
		return $jobs;
	}
}
