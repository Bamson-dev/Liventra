<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\CapacityPlanningServiceInterface;
use Liventra\Entities\CapacityReport;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class CapacityPlanningService implements CapacityPlanningServiceInterface {

	public function estimateCapacity(): CapacityReport {
		return new CapacityReport( 'cap_' . wp_generate_uuid4(), 50000, 16, 12.5 );
	}
}
