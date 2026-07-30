<?php
namespace Liventra\Contracts\Services;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface EnterpriseIdentityServiceInterface {
	public function authenticateEnterprise( string $samlResponse ): array;
	public function provisionIdentity( string $orgId, array $attributes ): int;
}
