<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class SdkModule {

	private $name;
	private $version;
	private $stable;

	public function __construct( string $name, string $version = '1.0.0', bool $stable = true ) {
		$this->name    = $name;
		$this->version = $version;
		$this->stable  = $stable;
	}

	public function name(): string { return $this->name; }
	public function version(): string { return $this->version; }
	public function isStable(): bool { return $this->stable; }
}
