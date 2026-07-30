<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\MarketplaceListing;
use Liventra\Entities\PluginPackage;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface MarketplaceServiceInterface {
	public function search( string $query = '' ): array;
	public function publishPlugin( PluginPackage $package ): MarketplaceListing;
}
