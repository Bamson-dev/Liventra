<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\PluginManagerInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class PluginController
 * Thin REST API Controller for Plugin Management (PRD-018)
 */
class PluginController {

	private $pluginManager;

	public function __construct( PluginManagerInterface $pluginManager ) {
		$this->pluginManager = $pluginManager;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/plugins/(?P<id>[a-zA-Z0-9_-]+)/enable', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'enable_plugin' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function enable_plugin( $request ) {
		$id     = $request['id'] ?? '';
		$res    = $this->pluginManager->enable( $id );
		return rest_ensure_response( array( 'plugin_id' => $id, 'enabled' => $res ) );
	}
}
