<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\MarketplaceServiceInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class MarketplaceController
 * Thin REST Controller for Plugin Marketplace (PRD-018)
 */
class MarketplaceController {

	private $marketplaceService;

	public function __construct( MarketplaceServiceInterface $marketplaceService ) {
		$this->marketplaceService = $marketplaceService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/marketplace/listings', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_listings' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function get_listings( $request ) {
		$listings = $this->marketplaceService->search();
		return rest_ensure_response( array( 'listings' => $listings ) );
	}
}
