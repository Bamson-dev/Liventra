<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\MarketplaceServiceInterface;
use Liventra\Contracts\Repositories\PluginRepositoryInterface;
use Liventra\Entities\MarketplaceListing;
use Liventra\Entities\PluginPackage;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class MarketplaceService implements MarketplaceServiceInterface {

	private $repository;

	public function __construct( PluginRepositoryInterface $repository = null ) {
		$this->repository = $repository;
		$this->seedCatalog();
	}

	private function seedCatalog() {
		if ( $this->repository ) {
			$this->repository->saveMarketplaceListing( new MarketplaceListing( 'plg_zapier', 'Zapier Automation Pro', 'Liventra Core Team', 'Connect 5,000+ apps directly to Liventra' ) );
			$this->repository->saveMarketplaceListing( new MarketplaceListing( 'plg_hubspot', 'HubSpot CRM Sync', 'HubSpot Ecosystem', 'Real-time attendee syncing with HubSpot CRM' ) );
		}
	}

	public function search( string $query = '' ): array {
		EventBus::dispatch( 'plugin.marketplace.synced', array( 'query' => $query ) );
		return $this->repository ? $this->repository->getMarketplaceListings() : array();
	}

	public function publishPlugin( PluginPackage $package ): MarketplaceListing {
		$listing = new MarketplaceListing( $package->packageId(), $package->manifest()->name(), $package->manifest()->author() );
		if ( $this->repository ) {
			$this->repository->saveMarketplaceListing( $listing );
		}
		return $listing;
	}
}
