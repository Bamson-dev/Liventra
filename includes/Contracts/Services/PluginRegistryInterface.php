<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Plugin;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface PluginRegistryInterface {
	public function registerPlugin( Plugin $plugin ): bool;
	public function discoverPlugins(): array;
}
