<?php
namespace Liventra\Providers;

use Liventra\Contracts\Providers\VideoProviderInterface;
use Liventra\Entities\VideoAsset;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class MP4Provider
 * Native MP4 HTML5 Video Provider Adapter (PRD-007 Part 3)
 */
class MP4Provider implements VideoProviderInterface {

	public function getProviderName(): string {
		return 'mp4';
	}

	public function supports( $source ): bool {
		$src = $source instanceof VideoAsset ? $source->source() : (string) $source;
		return (bool) preg_match( '/\.(mp4|m4v)(\?.*)?$/i', $src ) || ( $source instanceof VideoAsset && 'mp4' === $source->provider() );
	}

	public function getSignedUrl( VideoAsset $asset ): string {
		// Append HMAC token for secure MP4 URLs (PRD-007 Part 12)
		$token = md5( $asset->source() . ( defined( 'AUTH_KEY' ) ? AUTH_KEY : 'liventra_salt' ) );
		$delim = false !== strpos( $asset->source(), '?' ) ? '&' : '?';
		return $asset->source() . $delim . 'token=' . $token;
	}
}
