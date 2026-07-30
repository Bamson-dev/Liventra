<?php
namespace Liventra\Providers;

use Liventra\Contracts\Providers\VideoProviderInterface;
use Liventra\Entities\VideoAsset;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class BunnyProvider
 * Bunny Stream Video Provider Adapter (PRD-007 Part 3)
 */
class BunnyProvider implements VideoProviderInterface {

	public function getProviderName(): string {
		return 'bunny';
	}

	public function supports( $source ): bool {
		$src = $source instanceof VideoAsset ? $source->source() : (string) $source;
		return (bool) preg_match( '/b-cdn\.net/i', $src ) || ( $source instanceof VideoAsset && 'bunny' === $source->provider() );
	}

	public function getSignedUrl( VideoAsset $asset ): string {
		return $asset->source() . '?token=' . md5( $asset->source() . 'bunny_key' );
	}
}
