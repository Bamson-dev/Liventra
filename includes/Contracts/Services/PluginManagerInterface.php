<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Plugin;
use Liventra\Entities\PluginManifest;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface PluginManagerInterface
 * Public Contract for Plugin Manager & Lifecycle Engine (PRD-018 Part 1)
 */
interface PluginManagerInterface {

	/**
	 * Install third-party plugin from package manifest (PRD-018 Part 4)
	 *
	 * @param PluginManifest $manifest Plugin manifest.
	 * @return Plugin
	 */
	public function install( PluginManifest $manifest ): Plugin;

	/**
	 * Uninstall plugin
	 *
	 * @param string $pluginId Plugin ID string.
	 * @return bool
	 */
	public function uninstall( string $pluginId ): bool;

	/**
	 * Enable installed plugin
	 *
	 * @param string $pluginId Plugin ID string.
	 * @return bool
	 */
	public function enable( string $pluginId ): bool;

	/**
	 * Disable plugin
	 *
	 * @param string $pluginId Plugin ID string.
	 * @return bool
	 */
	public function disable( string $pluginId ): bool;

	/**
	 * Update plugin to newer version
	 *
	 * @param string $pluginId Plugin ID string.
	 * @param string $targetVersion Target version string.
	 * @return Plugin
	 */
	public function update( string $pluginId, string $targetVersion ): Plugin;
}
