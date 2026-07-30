<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\PluginSandboxInterface;
use Liventra\Entities\Plugin;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class PluginSandbox implements PluginSandboxInterface {

	public function executeInSandbox( Plugin $plugin, callable $callback ) {
		try {
			return call_user_func( $callback );
		} catch ( \Throwable $e ) {
			EventBus::dispatch( 'plugin.sandbox.violation', array( 'plugin_id' => $plugin->pluginId(), 'error' => $e->getMessage() ) );
			return null;
		}
	}

	public function enforceCapabilities( Plugin $plugin, array $requiredCapabilities ): bool {
		$granted = $plugin->manifest()->permissions();
		foreach ( $requiredCapabilities as $cap ) {
			if ( ! in_array( $cap, $granted, true ) ) {
				EventBus::dispatch( 'plugin.sandbox.violation', array( 'plugin_id' => $plugin->pluginId(), 'missing_capability' => $cap ) );
				return false;
			}
		}
		return true;
	}
}
