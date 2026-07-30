<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\ApiGatewayInterface;
use Liventra\Entities\ApiRequest;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiGatewayController
 * Thin REST API Gateway Controller (PRD-014 Part 16)
 */
class ApiGatewayController {

	private $gatewayService;

	public function __construct( ApiGatewayInterface $gatewayService ) {
		$this->gatewayService = $gatewayService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/gateway/(?P<path>.*)', array(
			'methods'             => array( 'GET', 'POST' ),
			'callback'            => array( $this, 'handle_gateway_v1' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function handle_gateway_v1( $request ) {
		$apiReq   = new ApiRequest( wp_generate_uuid4(), $request->get_route(), $request->get_method(), $request->get_params(), $request->get_headers(), 'v1' );
		$response = $this->gatewayService->dispatch( $apiReq );
		return rest_ensure_response( $response->toArray() );
	}
}
