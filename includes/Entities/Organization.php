<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Organization
 * Domain Entity representing an Enterprise Organization (PRD-019 Part 2)
 */
class Organization {

	private $orgId;
	private $name;
	private $slug;
	private $ownerId;
	private $active;

	public function __construct( string $orgId, string $name, string $slug, int $ownerId, bool $active = true ) {
		$this->orgId   = $orgId;
		$this->name    = $name;
		$this->slug    = $slug;
		$this->ownerId = $ownerId;
		$this->active  = $active;
	}

	public function orgId(): string { return $this->orgId; }
	public function name(): string { return $this->name; }
	public function slug(): string { return $this->slug; }
	public function ownerId(): int { return $this->ownerId; }
	public function isActive(): bool { return $this->active; }
}
