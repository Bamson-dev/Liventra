<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\CapacityReport;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface CapacityPlanningServiceInterface {
	public function estimateCapacity(): CapacityReport;
}
