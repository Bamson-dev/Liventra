<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class PluginDependency {

	private $pluginId;
	private $constraint;

	public function __construct( string $pluginId, string $constraint = '*' ) {
		$this->pluginId   = $pluginId;
		$this->constraint = $constraint;
	}

	public function pluginId(): string { return $this->pluginId; }
	public function constraint(): string { return $this->constraint; }
}
