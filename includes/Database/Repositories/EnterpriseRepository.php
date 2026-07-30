<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\EnterpriseRepositoryInterface;
use Liventra\Entities\Organization;
use Liventra\Entities\Workspace;
use Liventra\Entities\Tenant;
use Liventra\Entities\OrganizationMember;
use Liventra\Entities\EnterprisePolicy;
use Liventra\Entities\BrandingProfile;
use Liventra\Entities\UsageQuota;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class EnterpriseRepository
 * Persistence implementation for Enterprise Platform (PRD-003 & PRD-019)
 */
class EnterpriseRepository implements EnterpriseRepositoryInterface {

	private $inMemoryOrgs       = array();
	private $inMemoryWorkspaces = array();
	private $inMemoryTenants    = array();
	private $inMemoryMembers    = array();
	private $inMemoryPolicies   = array();
	private $inMemoryBranding   = array();
	private $inMemoryQuotas     = array();

	public function saveOrganization( Organization $org ): bool {
		$this->inMemoryOrgs[ $org->orgId() ] = $org;
		return true;
	}

	public function findOrganization( string $orgId ): ?Organization {
		return isset( $this->inMemoryOrgs[ $orgId ] ) ? $this->inMemoryOrgs[ $orgId ] : null;
	}

	public function saveWorkspace( Workspace $workspace ): bool {
		$this->inMemoryWorkspaces[ $workspace->workspaceId() ] = $workspace;
		return true;
	}

	public function saveTenant( Tenant $tenant ): bool {
		$this->inMemoryTenants[ $tenant->domain() ] = $tenant;
		return true;
	}

	public function findTenantByDomain( string $domain ): ?Tenant {
		return isset( $this->inMemoryTenants[ $domain ] ) ? $this->inMemoryTenants[ $domain ] : null;
	}

	public function saveMember( OrganizationMember $member ): bool {
		$this->inMemoryMembers[ $member->memberId() ] = $member;
		return true;
	}

	public function savePolicy( EnterprisePolicy $policy ): bool {
		$this->inMemoryPolicies[ $policy->orgId() ] = $policy;
		return true;
	}

	public function saveBranding( BrandingProfile $branding ): bool {
		$this->inMemoryBranding[ $branding->orgId() ] = $branding;
		return true;
	}

	public function saveQuota( UsageQuota $quota ): bool {
		$this->inMemoryQuotas[ $quota->orgId() ] = $quota;
		return true;
	}
}
