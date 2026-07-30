<?php
namespace Liventra\Providers;

use Liventra\Contracts\Providers\VideoProviderInterface;
use Liventra\Entities\VideoAsset;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class HLSProvider
 * HTTP Live Streaming (HLS / .m3u8) Provider Adapter (PRD-007 Part 3)
 */
class HLSProvider implements VideoProviderInterface {

	public function getProviderName(): string {
		return 'hls';
	}

	public function supports( $source ): bool {
		$src = $source instanceof VideoAsset ? $source->source() : (string) $source;
		return (bool) preg_match( '/\.m3u8(\?.*)?$/i', $src ) || ( $source instanceof VideoAsset && 'hls' === $source->provider() );
	}

	public function getSignedUrl( VideoAsset $asset ): string {
		$token = md5( $asset->source() . 'hls_secret' );
		$delim = false !== strpos( $asset->source(), '?' ) ? '&' : '?';
		return $asset->source() . $delim . 'token=' . $token;
	}
}
