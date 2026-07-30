<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\SdkServiceInterface;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class SdkService implements SdkServiceInterface {

	private $hooks    = array();
	private $services = array();

	public function registerHook( string $hookName, callable $callback, int $priority = 10 ): bool {
		$this->hooks[ $hookName ][] = array( 'callback' => $callback, 'priority' => $priority );
		EventBus::subscribe( $hookName, $callback );
		return true;
	}

	public function registerService( string $serviceName, object $serviceInstance ): bool {
		$this->services[ $serviceName ] = $serviceInstance;
		return true;
	}

	public function registerRestEndpoint( string $route, string $method, callable $callback ): bool {
		if ( function_exists( 'register_rest_route' ) ) {
			register_rest_route( 'liventra/v1', '/ext/' . ltrim( $route, '/' ), array(
				'methods'             => strtoupper( $method ),
				'callback'            => $callback,
				'permission_callback'=> '__return_true',
			) );
		}
		return true;
	}
}
