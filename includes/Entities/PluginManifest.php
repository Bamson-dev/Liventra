<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class PluginManifest
 * Domain Entity representing Plugin Metadata Manifest (PRD-018 Part 2 & 5)
 */
class PluginManifest {

	private $id;
	private $name;
	private $author;
	private $version;
	private $sdkVersion;
	private $permissions;
	private $dependencies;

	public function __construct(
		string $id,
		string $name,
		string $author,
		string $version = '1.0.0',
		string $sdkVersion = '^1.0',
		array $permissions = array(),
		array $dependencies = array()
	) {
		$this->id           = $id;
		$this->name         = $name;
		$this->author       = $author;
		$this->version      = $version;
		$this->sdkVersion   = $sdkVersion;
		$this->permissions  = $permissions;
		$this->dependencies = $dependencies;
	}

	public function id(): string { return $this->id; }
	public function name(): string { return $this->name; }
	public function author(): string { return $this->author; }
	public function version(): string { return $this->version; }
	public function sdkVersion(): string { return $this->sdkVersion; }
	public function permissions(): array { return $this->permissions; }
	public function dependencies(): array { return $this->dependencies; }
}
