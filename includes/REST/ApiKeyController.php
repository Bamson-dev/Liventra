<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\ApiKeyServiceInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiKeyController
 * Thin REST Controller for API Key Management (PRD-014 Part 16)
 */
class ApiKeyController {

	private $apiKeyService;

	public function __construct( ApiKeyServiceInterface $apiKeyService ) {
		$this->apiKeyService = $apiKeyService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/keys', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'create_key' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function create_key( $request ) {
		$params = $request->get_json_params() ?? array();
		$key    = $this->apiKeyService->issueApiKey( (int) ( $params['user_id'] ?? 1 ), (string) ( $params['name'] ?? 'Default Key' ) );
		return rest_ensure_response( array(
			'key_id'     => $key->keyId(),
			'secret_key' => $key->secretKey(),
		) );
	}
}
