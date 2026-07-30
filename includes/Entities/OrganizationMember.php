<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class OrganizationMember {

	private $memberId;
	private $orgId;
	private $userId;
	private $role; // 'owner' | 'admin' | 'billing_admin' | 'workspace_admin' | 'editor' | 'analyst' | 'viewer'

	public function __construct( string $memberId, string $orgId, int $userId, string $role = 'member' ) {
		$this->memberId = $memberId;
		$this->orgId    = $orgId;
		$this->userId   = $userId;
		$this->role     = strtolower( $role );
	}

	public function memberId(): string { return $this->memberId; }
	public function orgId(): string { return $this->orgId; }
	public function userId(): int { return $this->userId; }
	public function role(): string { return $this->role; }
}
