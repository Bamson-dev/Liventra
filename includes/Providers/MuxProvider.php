<?php
namespace Liventra\Providers;

use Liventra\Contracts\Providers\VideoProviderInterface;
use Liventra\Entities\VideoAsset;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class MuxProvider
 * Mux Video Infrastructure Provider Adapter (PRD-007 Part 3)
 */
class MuxProvider implements VideoProviderInterface {

	public function getProviderName(): string {
		return 'mux';
	}

	public function supports( $source ): bool {
		$src = $source instanceof VideoAsset ? $source->source() : (string) $source;
		return (bool) preg_match( '/stream\.mux\.com/i', $src ) || ( $source instanceof VideoAsset && 'mux' === $source->provider() );
	}

	public function getSignedUrl( VideoAsset $asset ): string {
		return $asset->source() . '?token=' . md5( $asset->source() . 'mux_jwt' );
	}
}
