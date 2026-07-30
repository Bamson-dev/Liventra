<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Organization;
use Liventra\Entities\OrganizationMember;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface OrganizationServiceInterface
 * Public Contract for Enterprise Organizations & Multi-Tenant Platform (PRD-019 Part 1)
 */
interface OrganizationServiceInterface {

	/**
	 * Create organization entity (PRD-019 Part 4)
	 *
	 * @param string $name Org name.
	 * @param int    $ownerId Owner user ID.
	 * @return Organization
	 */
	public function createOrganization( string $name, int $ownerId ): Organization;

	/**
	 * Join organization
	 *
	 * @param string $orgId Org ID.
	 * @param int    $userId User ID.
	 * @param string $role Organization role.
	 * @return OrganizationMember
	 */
	public function joinOrganization( string $orgId, int $userId, string $role = 'member' ): OrganizationMember;

	/**
	 * Invite member to organization
	 *
	 * @param string $orgId Org ID.
	 * @param string $email Member email address.
	 * @param string $role Assigned role.
	 * @return bool
	 */
	public function inviteMember( string $orgId, string $email, string $role = 'member' ): bool;

	/**
	 * Assign role to member
	 *
	 * @param string $orgId Org ID.
	 * @param int    $userId User ID.
	 * @param string $role Scoped role string.
	 * @return bool
	 */
	public function assignRole( string $orgId, int $userId, string $role ): bool;
}
