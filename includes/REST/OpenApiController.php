<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\OpenApiServiceInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class OpenApiController
 * Thin REST Controller for OpenAPI 3.1 Documentation (PRD-014 Part 16)
 */
class OpenApiController {

	private $openApiService;

	public function __construct( OpenApiServiceInterface $openApiService ) {
		$this->openApiService = $openApiService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/openapi.json', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_openapi_spec' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function get_openapi_spec( $request ) {
		return rest_ensure_response( json_decode( $this->openApiService->generateOpenApi(), true ) );
	}
}
