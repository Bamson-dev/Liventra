<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\EnterprisePolicy;
use Liventra\Entities\AuditExport;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface GovernanceServiceInterface {
	public function applyPolicy( string $orgId, array $policyData ): EnterprisePolicy;
	public function exportAudit( string $orgId ): AuditExport;
	public function checkQuota( string $orgId, string $resourceType ): bool;
}
