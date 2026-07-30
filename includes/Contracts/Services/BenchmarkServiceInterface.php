<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\BenchmarkResult;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface BenchmarkServiceInterface {
	public function benchmark( string $testName ): BenchmarkResult;
}
