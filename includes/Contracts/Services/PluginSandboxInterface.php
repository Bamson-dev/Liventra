<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Plugin;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface PluginSandboxInterface {
	public function executeInSandbox( Plugin $plugin, callable $callback );
	public function enforceCapabilities( Plugin $plugin, array $requiredCapabilities ): bool;
}
