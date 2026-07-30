<?php
namespace Liventra\Resolvers;

use Liventra\Contracts\Providers\VideoProviderInterface;
use Liventra\Providers\MP4Provider;
use Liventra\Providers\HLSProvider;
use Liventra\Providers\BunnyProvider;
use Liventra\Providers\VimeoProvider;
use Liventra\Providers\MuxProvider;
use Liventra\Entities\VideoAsset;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ProviderResolver
 * Media Provider Resolver (PRD-007 Part 3)
 */
class ProviderResolver {

	private $providers = array();

	public function __construct( array $providers = array() ) {
		if ( empty( $providers ) ) {
			$this->providers = array(
				new MP4Provider(),
				new HLSProvider(),
				new BunnyProvider(),
				new VimeoProvider(),
				new MuxProvider(),
			);
		} else {
			$this->providers = $providers;
		}
	}

	public function registerProvider( VideoProviderInterface $provider ) {
		$this->providers[] = $provider;
	}

	public function resolve( $source ): VideoProviderInterface {
		foreach ( $this->providers as $provider ) {
			if ( $provider->supports( $source ) ) {
				return $provider;
			}
		}

		// Fallback to MP4 provider
		return new MP4Provider();
	}
}
