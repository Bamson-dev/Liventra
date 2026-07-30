<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Plugin
 * Domain Entity representing an Installed Third-Party Plugin (PRD-018 Part 2)
 */
class Plugin {

	private $pluginId;
	private $manifest;
	private $signature;
	private $active;
	private $installedAt;

	public function __construct(
		string $pluginId,
		PluginManifest $manifest,
		?PluginSignature $signature = null,
		bool $active = false
	) {
		$this->pluginId    = $pluginId;
		$this->manifest    = $manifest;
		$this->signature   = $signature;
		$this->active      = $active;
		$this->installedAt = time();
	}

	public function pluginId(): string { return $this->pluginId; }
	public function manifest(): PluginManifest { return $this->manifest; }
	public function signature(): ?PluginSignature { return $this->signature; }
	public function isActive(): bool { return $this->active; }
	public function setActive( bool $active ): void { $this->active = $active; }
}
