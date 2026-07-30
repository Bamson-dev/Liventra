<?php
namespace Liventra\Providers;

use Liventra\Contracts\Providers\VideoProviderInterface;
use Liventra\Entities\VideoAsset;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class VimeoProvider
 * Vimeo Pro / OTT Video Provider Adapter (PRD-007 Part 3)
 */
class VimeoProvider implements VideoProviderInterface {

	public function getProviderName(): string {
		return 'vimeo';
	}

	public function supports( $source ): bool {
		$src = $source instanceof VideoAsset ? $source->source() : (string) $source;
		return (bool) preg_match( '/vimeo\.com/i', $src ) || ( $source instanceof VideoAsset && 'vimeo' === $source->provider() );
	}

	public function getSignedUrl( VideoAsset $asset ): string {
		return $asset->source();
	}
}
