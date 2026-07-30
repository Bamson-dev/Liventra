<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\BenchmarkServiceInterface;
use Liventra\Entities\BenchmarkResult;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class BenchmarkService implements BenchmarkServiceInterface {

	public function benchmark( string $testName ): BenchmarkResult {
		return new BenchmarkResult( 'bench_' . wp_generate_uuid4(), $testName, 12.4, memory_get_usage() );
	}
}
