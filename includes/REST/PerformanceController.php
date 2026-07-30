<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\PerformanceServiceInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class PerformanceController
 * Thin REST Controller for Runtime Performance & Capacity (PRD-017)
 */
class PerformanceController {

	private $performanceService;

	public function __construct( PerformanceServiceInterface $performanceService ) {
		$this->performanceService = $performanceService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/performance/capacity', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_capacity' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function get_capacity( $request ) {
		$report = $this->performanceService->estimateCapacity();
		return rest_ensure_response( array(
			'report_id'                => $report->reportId(),
			'max_concurrent_attendees' => $report->maxConcurrentAttendees(),
			'recommended_workers'      => $report->recommendedWorkers(),
			'estimated_bandwidth_gb'   => $report->estimatedBandwidthGb(),
		) );
	}
}
