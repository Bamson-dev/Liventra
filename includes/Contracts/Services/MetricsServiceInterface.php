<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Metric;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface MetricsServiceInterface {
	public function recordMetric( string $name, float $value, string $type = 'gauge', array $tags = array() ): Metric;
}
