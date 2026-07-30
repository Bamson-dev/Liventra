<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\PluginRepositoryInterface;
use Liventra\Entities\Plugin;
use Liventra\Entities\MarketplaceListing;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class PluginRepository
 * Persistence implementation for Plugin SDK & Marketplace Platform (PRD-003 & PRD-018)
 */
class PluginRepository implements PluginRepositoryInterface {

	private $inMemoryPlugins     = array();
	private $inMemoryListings    = array();

	public function savePlugin( Plugin $plugin ): bool {
		$this->inMemoryPlugins[ $plugin->pluginId() ] = $plugin;
		return true;
	}

	public function findPlugin( string $pluginId ): ?Plugin {
		return isset( $this->inMemoryPlugins[ $pluginId ] ) ? $this->inMemoryPlugins[ $pluginId ] : null;
	}

	public function deletePlugin( string $pluginId ): bool {
		unset( $this->inMemoryPlugins[ $pluginId ] );
		return true;
	}

	public function saveMarketplaceListing( MarketplaceListing $listing ): bool {
		$this->inMemoryListings[ $listing->listingId() ] = $listing;
		return true;
	}

	public function getMarketplaceListings(): array {
		return array_values( $this->inMemoryListings );
	}
}
