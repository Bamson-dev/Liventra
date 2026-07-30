<?php
namespace Liventra\Contracts\Repositories;

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
 * Interface EnterpriseRepositoryInterface
 * Persistence contract for Enterprise Platform (PRD-019 Part 3)
 */
interface EnterpriseRepositoryInterface {

	public function saveOrganization( Organization $org ): bool;
	public function findOrganization( string $orgId ): ?Organization;
	public function saveWorkspace( Workspace $workspace ): bool;
	public function saveTenant( Tenant $tenant ): bool;
	public function findTenantByDomain( string $domain ): ?Tenant;
	public function saveMember( OrganizationMember $member ): bool;
	public function savePolicy( EnterprisePolicy $policy ): bool;
	public function saveBranding( BrandingProfile $branding ): bool;
	public function saveQuota( UsageQuota $quota ): bool;
}
