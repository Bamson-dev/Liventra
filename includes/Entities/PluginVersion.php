<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class PluginVersion {

	private $version;
	private $changelog;

	public function __construct( string $version, string $changelog = '' ) {
		$this->version   = $version;
		$this->changelog = $changelog;
	}

	public function version(): string { return $this->version; }
	public function changelog(): string { return $this->changelog; }
}
