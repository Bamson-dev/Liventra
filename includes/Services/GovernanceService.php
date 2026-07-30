<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\GovernanceServiceInterface;
use Liventra\Contracts\Repositories\EnterpriseRepositoryInterface;
use Liventra\Entities\EnterprisePolicy;
use Liventra\Entities\AuditExport;
use Liventra\Entities\UsageQuota;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class GovernanceService implements GovernanceServiceInterface {

	private $repository;

	public function __construct( EnterpriseRepositoryInterface $repository = null ) {
		$this->repository = $repository;
	}

	public function applyPolicy( string $orgId, array $policyData ): EnterprisePolicy {
		$polId  = 'pol_' . wp_generate_uuid4();
		$policy = new EnterprisePolicy(
			$polId,
			$orgId,
			(bool) ( $policyData['mfa_required'] ?? true ),
			(array) ( $policyData['ip_restrictions'] ?? array() ),
			(int) ( $policyData['password_min_length'] ?? 12 )
		);

		if ( $this->repository ) {
			$this->repository->savePolicy( $policy );
		}

		EventBus::dispatch( 'policy.updated', array( 'org_id' => $orgId ) );
		return $policy;
	}

	public function exportAudit( string $orgId ): AuditExport {
		$expId  = 'exp_' . wp_generate_uuid4();
		$export = new AuditExport( $expId, $orgId, 450, "https://storage.liventra.io/exports/{$expId}.csv" );
		return $export;
	}

	public function checkQuota( string $orgId, string $resourceType ): bool {
		$quota = new UsageQuota( $orgId, 100, 10000, 500, 5 );
		if ( $this->repository ) {
			$this->repository->saveQuota( $quota );
		}

		if ( $quota->isExceeded() ) {
			EventBus::dispatch( 'quota.exceeded', array( 'org_id' => $orgId, 'resource' => $resourceType ) );
			return false;
		}
		return true;
	}
}
