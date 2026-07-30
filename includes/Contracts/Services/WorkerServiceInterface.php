<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Worker;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface WorkerServiceInterface {
	public function runWorker( string $queueName ): Worker;
}
