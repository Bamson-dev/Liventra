<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\AdminStudioServiceInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class AdminStudioController
 * Thin REST API Controller for Admin Studio (PRD-012 Part 14)
 */
class AdminStudioController {

	private $studioService;

	public function __construct( AdminStudioServiceInterface $studioService ) {
		$this->studioService = $studioService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) {
			return;
		}

		register_rest_route( 'liventra/v1', '/studio/dashboard', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_dashboard' ),
			'permission_callback'=> '__return_true',
		) );

		register_rest_route( 'liventra/v1', '/studio/webinars', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'create_webinar' ),
			'permission_callback'=> '__return_true',
		) );

		register_rest_route( 'liventra/v1', '/studio/webinars/(?P<id>\d+)/publish', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'publish_webinar' ),
			'permission_callback'=> '__return_true',
		) );

		register_rest_route( 'liventra/v1', '/studio/webinars/(?P<id>\d+)/preview', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'preview_webinar' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function get_dashboard( $request ) {
		return rest_ensure_response( $this->studioService->getDashboard() );
	}

	public function create_webinar( $request ) {
		$params = $request->get_json_params() ?? array();
		return rest_ensure_response( $this->studioService->createWebinar( $params ) );
	}

	public function publish_webinar( $request ) {
		$id = (int) $request['id'];
		return rest_ensure_response( $this->studioService->publishWebinar( $id ) );
	}

	public function preview_webinar( $request ) {
		$id     = (int) $request['id'];
		$params = $request->get_json_params() ?? array();
		$offset = (int) ( $params['offset_seconds'] ?? 0 );
		return rest_ensure_response( $this->studioService->previewWebinar( $id, $offset ) );
	}
}
