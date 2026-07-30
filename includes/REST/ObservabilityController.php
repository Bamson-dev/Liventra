<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\ObservabilityServiceInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ObservabilityController
 * Thin REST Controller for Operations & Diagnostics (PRD-016)
 */
class ObservabilityController {

	private $observabilityService;

	public function __construct( ObservabilityServiceInterface $observabilityService ) {
		$this->observabilityService = $observabilityService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/operations/health', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_health' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function get_health( $request ) {
		$health = $this->observabilityService->runHealthChecks();
		return rest_ensure_response( array( 'status' => 'healthy', 'probes' => $health ) );
	}
}
