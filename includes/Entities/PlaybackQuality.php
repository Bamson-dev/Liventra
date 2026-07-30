<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class PlaybackQuality
 * Domain Entity for Adaptive Quality Levels (PRD-007 Part 9)
 */
class PlaybackQuality {

	private $label; // 'auto' | '1080p' | '720p' | '480p' | '360p'
	private $bitrate;

	public function __construct( string $label, int $bitrate = 0 ) {
		$this->label   = strtolower( $label );
		$this->bitrate = $bitrate;
	}

	public function getLabel(): string { return $this->label; }
	public function getBitrate(): int { return $this->bitrate; }
	public function isAuto(): bool { return 'auto' === $this->label; }
}
