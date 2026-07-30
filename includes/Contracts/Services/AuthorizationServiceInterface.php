<?php
namespace Liventra\Contracts\Services;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface AuthorizationServiceInterface
 * Public Contract for Role-Based Access Control (RBAC) & Authorization (PRD-013 Part 1)
 */
interface AuthorizationServiceInterface {

	/**
	 * Authenticate user request credentials
	 *
	 * @param array $credentials Request credentials.
	 * @return array Authenticated user identity payload.
	 */
	public function authenticate( array $credentials ): array;

	/**
	 * Authorize user action against a required capability
	 *
	 * @param int    $userId User ID.
	 * @param string $capability Target capability (e.g. 'publish_webinar').
	 * @param array  $context Context payload (e.g. webinar_id).
	 * @return bool
	 */
	public function authorize( int $userId, string $capability, array $context = array() ): bool;

	/**
	 * Check capability resolution for role
	 *
	 * @param string $role Role key ('super_admin', 'owner', 'administrator', 'editor', 'analyst', 'support', 'viewer').
	 * @param string $capability Capability key.
	 * @return bool
	 */
	public function hasCapability( string $role, string $capability ): bool;

	/**
	 * Resolve user role key
	 *
	 * @param int $userId User ID.
	 * @return string Role key.
	 */
	public function resolveRole( int $userId ): string;
}
