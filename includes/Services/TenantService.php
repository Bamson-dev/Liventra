<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\TenantServiceInterface;
use Liventra\Contracts\Repositories\EnterpriseRepositoryInterface;
use Liventra\Entities\Tenant;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class TenantService implements TenantServiceInterface {

	private $repository;

	public function __construct( EnterpriseRepositoryInterface $repository = null ) {
		$this->repository = $repository;
	}

	public function assignTenant( string $orgId, string $domain ): Tenant {
		$tId    = 'tnt_' . wp_generate_uuid4();
		$tenant = new Tenant( $tId, $orgId, $domain, explode( '.', $domain )[0] );

		if ( $this->repository ) {
			$this->repository->saveTenant( $tenant );
		}

		return $tenant;
	}

	public function resolveTenant( string $host ): ?Tenant {
		$tenant = $this->repository ? $this->repository->findTenantByDomain( $host ) : null;
		if ( $tenant ) {
			EventBus::dispatch( 'tenant.resolved', array( 'tenant_id' => $tenant->tenantId(), 'host' => $host ) );
		}
		return $tenant;
	}
}
