<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Tenant;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface TenantServiceInterface {
	public function assignTenant( string $orgId, string $domain ): Tenant;
	public function resolveTenant( string $host ): ?Tenant;
}
