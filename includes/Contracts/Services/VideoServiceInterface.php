<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\VideoAsset;
use Liventra\Entities\PlaybackState;
use Liventra\Entities\PlaybackQuality;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface VideoServiceInterface
 * Public Contract for Authoritative Video Rendering Engine (PRD-007 Part 1)
 */
interface VideoServiceInterface {

	/**
	 * Initialize video engine service
	 *
	 * @param array $config Configuration parameters.
	 * @return bool
	 */
	public function initialize( array $config = array() ): bool;

	/**
	 * Load a video asset into provider adapter
	 *
	 * @param VideoAsset $asset Video asset entity.
	 * @return bool
	 */
	public function loadVideo( VideoAsset $asset ): bool;

	/**
	 * Trigger video playback
	 *
	 * @return bool
	 */
	public function play(): bool;

	/**
	 * Trigger video pause (if allowed)
	 *
	 * @return bool
	 */
	public function pause(): bool;

	/**
	 * Perform seek operation with restriction enforcement
	 *
	 * @param float $targetSecond Target playback timestamp.
	 * @param bool  $force Force seek ignoring standard user restrictions.
	 * @return bool
	 */
	public function seek( float $targetSecond, bool $force = false ): bool;

	/**
	 * Synchronize player against authoritative session offset (PRD-007 Part 5)
	 *
	 * @param float $authoritativeOffset Server calculated timestamp.
	 * @param float $currentVideoTime Actual video player timestamp.
	 * @return array Sync correction result.
	 */
	public function synchronize( float $authoritativeOffset, float $currentVideoTime ): array;

	/**
	 * Recover playback after network stall or reconnect (PRD-007 Part 7)
	 *
	 * @param float $authoritativeOffset Target resume timestamp.
	 * @return bool
	 */
	public function recoverPlayback( float $authoritativeOffset ): bool;

	/**
	 * Switch adaptive playback quality (PRD-007 Part 9)
	 *
	 * @param PlaybackQuality $quality Target quality object.
	 * @return bool
	 */
	public function switchQuality( PlaybackQuality $quality ): bool;

	/**
	 * Get current playback state
	 *
	 * @return PlaybackState
	 */
	public function getPlaybackState(): PlaybackState;

	/**
	 * Destroy video instance and clean resources
	 *
	 * @return bool
	 */
	public function destroy(): bool;
}
