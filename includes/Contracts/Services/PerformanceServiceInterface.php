<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\BenchmarkResult;
use Liventra\Entities\CapacityReport;
use Liventra\Entities\PerformanceProfile;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface PerformanceServiceInterface
 * Public Contract for Runtime Optimization & Performance Platform (PRD-017 Part 1)
 */
interface PerformanceServiceInterface {

	/**
	 * Profile execution block
	 *
	 * @param string   $component Component name.
	 * @param callable $callback Callable code block.
	 * @return PerformanceProfile
	 */
	public function profile( string $component, callable $callback ): PerformanceProfile;

	/**
	 * Run performance benchmark (PRD-017 Part 14)
	 *
	 * @param string $testName Benchmark test name.
	 * @return BenchmarkResult
	 */
	public function benchmark( string $testName ): BenchmarkResult;

	/**
	 * Estimate system capacity based on active metrics (PRD-017 Part 13)
	 *
	 * @return CapacityReport
	 */
	public function estimateCapacity(): CapacityReport;

	/**
	 * Warm multi-level caches (PRD-017 Part 5)
	 *
	 * @param array $tags Cache tags to precompute.
	 * @return bool
	 */
	public function warmCache( array $tags = array() ): bool;
}
