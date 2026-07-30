<?php
namespace Liventra\Contracts\Providers;

use Liventra\Entities\VideoAsset;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface VideoProviderInterface
 * Contract for Media Provider Adapters (MP4, HLS, Bunny, Vimeo, Mux) (PRD-007 Part 3)
 */
interface VideoProviderInterface {

	/**
	 * Get provider identifier string
	 *
	 * @return string ('mp4' | 'hls' | 'bunny' | 'vimeo' | 'mux')
	 */
	public function getProviderName(): string;

	/**
	 * Check if provider supports a given source string or asset
	 *
	 * @param string|VideoAsset $source Source URL or asset entity.
	 * @return bool
	 */
	public function supports( $source ): bool;

	/**
	 * Prepare signed secure playback URL/token for asset (PRD-007 Part 12)
	 *
	 * @param VideoAsset $asset Video asset entity.
	 * @return string Secure playback URL.
	 */
	public function getSignedUrl( VideoAsset $asset ): string;
}
