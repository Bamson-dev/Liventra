<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class PerformanceProfile {

	private $profileId;
	private $component;
	private $durationMs;
	private $peakMemoryBytes;

	public function __construct( string $profileId, string $component, float $durationMs, int $peakMemoryBytes ) {
		$this->profileId       = $profileId;
		$this->component       = $component;
		$this->durationMs      = $durationMs;
		$this->peakMemoryBytes = $peakMemoryBytes;
	}

	public function profileId(): string { return $this->profileId; }
	public function component(): string { return $this->component; }
	public function durationMs(): float { return $this->durationMs; }
	public function peakMemoryBytes(): int { return $this->peakMemoryBytes; }
}
