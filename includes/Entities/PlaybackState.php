<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class PlaybackState
 * Domain Entity representing Video Player State Machine (PRD-007 Part 2)
 */
class PlaybackState {

	const STATE_IDLE      = 'idle';
	const STATE_LOADING   = 'loading';
	const STATE_BUFFERING = 'buffering';
	const STATE_READY     = 'ready';
	const STATE_PLAYING   = 'playing';
	const STATE_PAUSED    = 'paused';
	const STATE_SEEKING   = 'seeking';
	const STATE_ENDED     = 'ended';
	const STATE_ERROR     = 'error';

	private $status;
	private $currentTime;
	private $duration;
	private $errorMessage;

	public function __construct(
		string $status = self::STATE_IDLE,
		float $currentTime = 0.0,
		float $duration = 0.0,
		?string $errorMessage = null
	) {
		$this->status       = $status;
		$this->currentTime  = max( 0.0, $currentTime );
		$this->duration     = max( 0.0, $duration );
		$this->errorMessage = $errorMessage;
	}

	public function getStatus(): string { return $this->status; }
	public function getCurrentTime(): float { return $this->currentTime; }
	public function getDuration(): float { return $this->duration; }
	public function getErrorMessage(): ?string { return $this->errorMessage; }

	public function isPlaying(): bool { return self::STATE_PLAYING === $this->status; }
	public function isBuffering(): bool { return self::STATE_BUFFERING === $this->status; }
	public function isEnded(): bool { return self::STATE_ENDED === $this->status; }
	public function hasError(): bool { return self::STATE_ERROR === $this->status; }
}
