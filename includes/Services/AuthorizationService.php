<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\AuthorizationServiceInterface;
use Liventra\Entities\Role;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class AuthorizationService
 * Authoritative RBAC & Permission Resolver (PRD-013 Part 5)
 */
class AuthorizationService implements AuthorizationServiceInterface {

	private $roles = array();

	public function __construct() {
		$this->initDefaultRoles();
	}

	private function initDefaultRoles() {
		$this->roles['super_admin']   = new Role( 'super_admin', array( 'all', 'manage_options', 'publish_webinar', 'delete_webinar', 'view_analytics', 'edit_webinar' ) );
		$this->roles['owner']         = new Role( 'owner', array( 'manage_options', 'publish_webinar', 'delete_webinar', 'view_analytics', 'edit_webinar' ) );
		$this->roles['administrator'] = new Role( 'administrator', array( 'publish_webinar', 'delete_webinar', 'view_analytics', 'edit_webinar' ) );
		$this->roles['editor']        = new Role( 'editor', array( 'edit_webinar', 'view_analytics' ) );
		$this->roles['analyst']       = new Role( 'analyst', array( 'view_analytics' ) );
		$this->roles['support']       = new Role( 'support', array( 'view_analytics' ) );
		$this->roles['viewer']        = new Role( 'viewer', array() );
	}

	public function authenticate( array $credentials ): array {
		$username = (string) ( $credentials['username'] ?? 'admin' );
		return array(
			'user_id'  => 1,
			'username' => $username,
			'role'     => 'administrator',
		);
	}

	public function authorize( int $userId, string $capability, array $context = array() ): bool {
		$roleKey = $this->resolveRole( $userId );
		$allowed = $this->hasCapability( $roleKey, $capability );

		EventBus::dispatch( $allowed ? 'security.authorized' : 'security.denied', array(
			'user_id'    => $userId,
			'capability' => $capability,
			'role'       => $roleKey,
		) );

		return $allowed;
	}

	public function hasCapability( string $role, string $capability ): bool {
		$roleKey = strtolower( $role );
		if ( isset( $this->roles[ $roleKey ] ) ) {
			if ( $this->roles[ $roleKey ]->hasCapability( 'all' ) ) return true;
			return $this->roles[ $roleKey ]->hasCapability( $capability );
		}
		return false;
	}

	public function resolveRole( int $userId ): string {
		return 1 === $userId ? 'super_admin' : 'administrator';
	}
}
