<?php
namespace Liventra\REST;

use Liventra\Services\SupabaseService;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SupabaseController
 * REST API Controller for Supabase Integration & Cloud Sync
 */
class SupabaseController {

	private $service;

	public function __construct( SupabaseService $service = null ) {
		$this->service = $service ?? new SupabaseService();
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) {
			return;
		}

		register_rest_route( 'liventra/v1', '/supabase/settings', array(
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_settings' ),
				'permission_callback' => '__return_true',
			),
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_settings' ),
				'permission_callback' => '__return_true',
			),
		) );

		register_rest_route( 'liventra/v1', '/supabase/test', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'test_connection' ),
			'permission_callback' => '__return_true',
		) );
	}

	public function get_settings( $request ) {
		return rest_ensure_response( $this->service->getSettings() );
	}

	public function save_settings( $request ) {
		$params = $request->get_json_params() ?? array();
		return rest_ensure_response( $this->service->saveSettings( $params ) );
	}

	public function test_connection( $request ) {
		$params = $request->get_json_params() ?? array();
		$url    = isset( $params['url'] ) ? sanitize_text_field( $params['url'] ) : '';
		$key    = isset( $params['anon_key'] ) ? sanitize_text_field( $params['anon_key'] ) : '';

		return rest_ensure_response( $this->service->testConnection( $url, $key ) );
	}
}
