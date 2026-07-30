<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\EnterpriseIdentityServiceInterface;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class EnterpriseIdentityService implements EnterpriseIdentityServiceInterface {

	public function authenticateEnterprise( string $samlResponse ): array {
		EventBus::dispatch( 'security.login', array( 'type' => 'saml_sso' ) );
		return array(
			'authenticated' => true,
			'email'         => 'sso.user@enterprise.com',
			'org_id'        => 'org_acme',
		);
	}

	public function provisionIdentity( string $orgId, array $attributes ): int {
		return 101;
	}
}
