<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Permission
 * Domain Entity representing a Role Capability Permission (PRD-013 Part 2)
 */
class Permission {

	private $name;
	private $capability;

	public function __construct( string $name, string $capability ) {
		$this->name       = $name;
		$this->capability = strtolower( $capability );
	}

	public function getName(): string { return $this->name; }
	public function getCapability(): string { return $this->capability; }
}
