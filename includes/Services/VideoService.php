<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\VideoServiceInterface;
use Liventra\Contracts\Services\SessionServiceInterface;
use Liventra\Contracts\Services\TimelineServiceInterface;
use Liventra\Resolvers\ProviderResolver;
use Liventra\Entities\VideoAsset;
use Liventra\Entities\PlaybackState;
use Liventra\Entities\PlaybackQuality;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class VideoService
 * Authoritative Video Rendering Engine Implementation (PRD-007)
 * Video Service is NEVER the source of truth; strictly consumes SessionEngine time.
 */
class VideoService implements VideoServiceInterface {

	private $sessionService;
	private $timelineService;
	private $providerResolver;
	private $currentAsset;
	private $currentState;
	private $currentQuality;

	public function __construct(
		SessionServiceInterface $sessionService = null,
		TimelineServiceInterface $timelineService = null,
		ProviderResolver $providerResolver = null
	) {
		$this->sessionService   = $sessionService;
		$this->timelineService  = $timelineService;
		$this->providerResolver = null !== $providerResolver ? $providerResolver : new ProviderResolver();
		$this->currentState     = new PlaybackState( PlaybackState::STATE_IDLE );
		$this->currentQuality   = new PlaybackQuality( 'auto' );
	}

	public function initialize( array $config = array() ): bool {
		EventBus::dispatch( 'video.initialized', $config );
		return true;
	}

	public function loadVideo( VideoAsset $asset ): bool {
		$this->currentAsset = $asset;
		$provider           = $this->providerResolver->resolve( $asset );
		$signedUrl          = $provider->getSignedUrl( $asset );

		$this->currentState = new PlaybackState( PlaybackState::STATE_READY, 0.0, (float) $asset->duration() );
		EventBus::dispatch( 'video.loaded', array(
			'uuid'       => $asset->uuid(),
			'provider'   => $provider->getProviderName(),
			'signed_url' => $signedUrl,
		) );

		return true;
	}

	public function play(): bool {
		$this->currentState = new PlaybackState( PlaybackState::STATE_PLAYING, $this->currentState->getCurrentTime(), $this->currentState->getDuration() );
		EventBus::dispatch( 'video.playing', array( 'time' => $this->currentState->getCurrentTime() ) );
		return true;
	}

	public function pause(): bool {
		$this->currentState = new PlaybackState( PlaybackState::STATE_PAUSED, $this->currentState->getCurrentTime(), $this->currentState->getDuration() );
		EventBus::dispatch( 'video.paused', array( 'time' => $this->currentState->getCurrentTime() ) );
		return true;
	}

	public function seek( float $targetSecond, bool $force = false ): bool {
		// Enforce seek restrictions (PRD-007 Part 8)
		if ( ! $force && null !== $this->currentAsset ) {
			if ( $targetSecond > $this->currentAsset->duration() ) {
				$targetSecond = (float) $this->currentAsset->duration();
			}
		}

		$this->currentState = new PlaybackState( PlaybackState::STATE_SEEKING, $targetSecond, $this->currentState->getDuration() );
		EventBus::dispatch( 'video.seeking', array( 'target' => $targetSecond, 'forced' => $force ) );
		return true;
	}

	/**
	 * Synchronize player against authoritative session offset (PRD-007 Part 5 Thresholds)
	 */
	public function synchronize( float $authoritativeOffset, float $currentVideoTime ): array {
		$diff = abs( $authoritativeOffset - $currentVideoTime );

		$action = 'ignore';
		$requiresSeek = false;

		if ( $diff <= 0.5 ) {
			// Difference <= 500ms -> Ignore
			$action = 'ignore';
		} elseif ( $diff <= 2.5 ) {
			// Difference > 500ms AND <= 2.5s -> Soft correction
			$action = 'soft_correction';
		} else {
			// Difference > 2.5s -> Force seek
			$action       = 'force_seek';
			$requiresSeek = true;
			$this->seek( $authoritativeOffset, true );
			EventBus::dispatch( 'video.resynchronized', array(
				'authoritative_offset' => $authoritativeOffset,
				'video_time'           => $currentVideoTime,
				'drift_seconds'        => $diff,
			) );
		}

		return array(
			'authoritative_offset' => $authoritativeOffset,
			'current_video_time'   => $currentVideoTime,
			'drift_seconds'        => $diff,
			'action'               => $action,
			'requires_seek'        => $requiresSeek,
		);
	}

	public function recoverPlayback( float $authoritativeOffset ): bool {
		$this->currentState = new PlaybackState( PlaybackState::STATE_BUFFERING, $authoritativeOffset, $this->currentState->getDuration() );
		EventBus::dispatch( 'video.buffering', array( 'target' => $authoritativeOffset ) );
		$this->seek( $authoritativeOffset, true );
		$this->play();
		return true;
	}

	public function switchQuality( PlaybackQuality $quality ): bool {
		$this->currentQuality = $quality;
		EventBus::dispatch( 'video.quality.changed', array( 'quality' => $quality->getLabel() ) );
		return true;
	}

	public function getPlaybackState(): PlaybackState {
		return $this->currentState;
	}

	public function destroy(): bool {
		$this->currentState = new PlaybackState( PlaybackState::STATE_IDLE );
		return true;
	}
}
