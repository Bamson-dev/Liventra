<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class VideoProvider
 * Domain Entity representing a Media Provider Configuration (PRD-007 Part 2 & 3)
 */
class VideoProvider {

	private $name;
	private $supportsSignedUrls;
	private $supportsAdaptiveQuality;

	public function __construct( string $name, bool $supportsSignedUrls = true, bool $supportsAdaptiveQuality = true ) {
		$this->name                    = strtolower( $name );
		$this->supportsSignedUrls      = $supportsSignedUrls;
		$this->supportsAdaptiveQuality = $supportsAdaptiveQuality;
	}

	public function getName(): string { return $this->name; }
	public function supportsSignedUrls(): bool { return $this->supportsSignedUrls; }
	public function supportsAdaptiveQuality(): bool { return $this->supportsAdaptiveQuality; }
}
