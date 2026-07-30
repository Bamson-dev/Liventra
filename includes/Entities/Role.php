<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Role
 * Domain Entity representing an RBAC User Role (PRD-013 Part 2 & 5)
 */
class Role {

	private $name;
	private $permissions;
	private $parentRole;

	public function __construct( string $name, array $permissions = array(), ?string $parentRole = null ) {
		$this->name        = strtolower( $name );
		$this->permissions = $permissions;
		$this->parentRole  = $parentRole;
	}

	public function getName(): string { return $this->name; }
	public function getPermissions(): array { return $this->permissions; }
	public function getParentRole(): ?string { return $this->parentRole; }

	public function hasCapability( string $capability ): bool {
		return in_array( strtolower( $capability ), $this->permissions, true );
	}
}
