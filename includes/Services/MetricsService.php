<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\MetricsServiceInterface;
use Liventra\Entities\Metric;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class MetricsService implements MetricsServiceInterface {
	public function recordMetric( string $name, float $value, string $type = 'gauge', array $tags = array() ): Metric {
		return new Metric( 'met_' . wp_generate_uuid4(), $name, $type, $value, $tags );
	}
}
