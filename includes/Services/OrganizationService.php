<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\OrganizationServiceInterface;
use Liventra\Contracts\Repositories\EnterpriseRepositoryInterface;
use Liventra\Contracts\Services\SecurityServiceInterface;
use Liventra\Contracts\Services\AnalyticsServiceInterface;
use Liventra\Entities\Organization;
use Liventra\Entities\OrganizationMember;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class OrganizationService
 * Authoritative Enterprise Organizations & Multi-Tenant Platform Orchestrator (PRD-019)
 */
class OrganizationService implements OrganizationServiceInterface {

	private $repository;
	private $securityService;
	private $analyticsService;

	public function __construct(
		EnterpriseRepositoryInterface $repository = null,
		SecurityServiceInterface $securityService = null,
		AnalyticsServiceInterface $analyticsService = null
	) {
		$this->repository       = $repository;
		$this->securityService = $securityService;
		$this->analyticsService = $analyticsService;
	}

	public function createOrganization( string $name, int $ownerId ): Organization {
		$orgId = 'org_' . wp_generate_uuid4();
		$slug  = strtolower( preg_replace( '/[^a-zA-Z0-9]/', '-', $name ) );
		$org   = new Organization( $orgId, $name, $slug, $ownerId, true );

		if ( $this->repository ) {
			$this->repository->saveOrganization( $org );
		}

		$this->joinOrganization( $orgId, $ownerId, 'owner' );

		EventBus::dispatch( 'organization.created', array( 'org_id' => $orgId, 'owner_id' => $ownerId ) );
		return $org;
	}

	public function joinOrganization( string $orgId, int $userId, string $role = 'member' ): OrganizationMember {
		$memId  = 'mem_' . wp_generate_uuid4();
		$member = new OrganizationMember( $memId, $orgId, $userId, $role );

		if ( $this->repository ) {
			$this->repository->saveMember( $member );
		}

		EventBus::dispatch( 'organization.member.joined', array( 'org_id' => $orgId, 'user_id' => $userId, 'role' => $role ) );
		return $member;
	}

	public function inviteMember( string $orgId, string $email, string $role = 'member' ): bool {
		EventBus::dispatch( 'organization.member.invited', array( 'org_id' => $orgId, 'email' => $email, 'role' => $role ) );
		return true;
	}

	public function assignRole( string $orgId, int $userId, string $role ): bool {
		$this->joinOrganization( $orgId, $userId, $role );
		EventBus::dispatch( 'organization.updated', array( 'org_id' => $orgId, 'updated_user' => $userId, 'new_role' => $role ) );
		return true;
	}
}
