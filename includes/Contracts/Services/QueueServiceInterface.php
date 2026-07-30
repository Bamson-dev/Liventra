<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\QueueJob;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface QueueServiceInterface
 * Public Contract for Async Queue Platform (PRD-017 Part 1 & 6)
 */
interface QueueServiceInterface {

	public function dispatch( string $queueName, array $payload ): QueueJob;
	public function dispatchDelayed( string $queueName, array $payload, int $delaySeconds ): QueueJob;
	public function dispatchBatch( string $queueName, array $batchPayloads ): array;
}
