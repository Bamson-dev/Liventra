<?php
namespace Liventra\Events;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class HandlerMetadata
 * Metadata for discovery, diagnostics, and version compatibility (Part 2)
 */
class HandlerMetadata {

	private $name;
	private $supportedEventTypes; // array of string types
	private $version;
	private $author;
	private $priority;
	private $dependencies; // array of required modules/plugins
	private $enabled;
	private $compatibilityVersion;

	public function __construct(
		string $name,
		array $supportedEventTypes,
		string $version = '1.0.0',
		string $author = 'Liventra Core',
		int $priority = 50,
		array $dependencies = array(),
		bool $enabled = true,
		string $compatibilityVersion = '1.0.0'
	) {
		$this->name                 = $name;
		$this->supportedEventTypes = $supportedEventTypes;
		$this->version              = $version;
		$this->author               = $author;
		$this->priority             = $priority;
		$this->dependencies         = $dependencies;
		$this->enabled              = $enabled;
		$this->compatibilityVersion = $compatibilityVersion;
	}

	public function getName(): string { return $this->name; }
	public function getSupportedEventTypes(): array { return $this->supportedEventTypes; }
	public function getVersion(): string { return $this->version; }
	public function getAuthor(): string { return $this->author; }
	public function getPriority(): int { return $this->priority; }
	public function getDependencies(): array { return $this->dependencies; }
	public function isEnabled(): bool { return $this->enabled; }
	public function getCompatibilityVersion(): string { return $this->compatibilityVersion; }

	public function toArray(): array {
		return array(
			'name'                  => $this->name,
			'supported_event_types' => $this->supportedEventTypes,
			'version'               => $this->version,
			'author'                => $this->author,
			'priority'              => $this->priority,
			'dependencies'          => $this->dependencies,
			'enabled'               => $this->enabled,
			'compatibility_version' => $this->compatibilityVersion,
		);
	}
}
