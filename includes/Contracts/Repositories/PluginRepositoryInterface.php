<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\Plugin;
use Liventra\Entities\MarketplaceListing;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface PluginRepositoryInterface
 * Persistence contract for Plugin SDK & Marketplace Platform (PRD-018 Part 3)
 */
interface PluginRepositoryInterface {

	public function savePlugin( Plugin $plugin ): bool;
	public function findPlugin( string $pluginId ): ?Plugin;
	public function deletePlugin( string $pluginId ): bool;
	public function saveMarketplaceListing( MarketplaceListing $listing ): bool;
	public function getMarketplaceListings(): array;
}
