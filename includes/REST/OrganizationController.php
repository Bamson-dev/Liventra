<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\OrganizationServiceInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class OrganizationController
 * Thin REST Controller for Enterprise Platform (PRD-019)
 */
class OrganizationController {

	private $orgService;

	public function __construct( OrganizationServiceInterface $orgService ) {
		$this->orgService = $orgService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/enterprise/organizations', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'create_org' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function create_org( $request ) {
		$params = $request->get_json_params() ?? array();
		$org    = $this->orgService->createOrganization(
			(string) ( $params['name'] ?? 'Acme Corp' ),
			(int) ( $params['owner_id'] ?? 1 )
		);
		return rest_ensure_response( array(
			'org_id' => $org->orgId(),
			'name'   => $org->name(),
			'slug'   => $org->slug(),
		) );
	}
}
