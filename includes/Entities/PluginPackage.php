<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class PluginPackage {

	private $packageId;
	private $manifest;
	private $signature;
	private $checksum;

	public function __construct( string $packageId, PluginManifest $manifest, PluginSignature $signature, string $checksum ) {
		$this->packageId = $packageId;
		$this->manifest  = $manifest;
		$this->signature = $signature;
		$this->checksum  = $checksum;
	}

	public function packageId(): string { return $this->packageId; }
	public function manifest(): PluginManifest { return $this->manifest; }
	public function signature(): PluginSignature { return $this->signature; }
	public function checksum(): string { return $this->checksum; }
}
