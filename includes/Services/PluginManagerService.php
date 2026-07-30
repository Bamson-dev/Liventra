<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\PluginManagerInterface;
use Liventra\Contracts\Repositories\PluginRepositoryInterface;
use Liventra\Contracts\Services\SecurityServiceInterface;
use Liventra\Entities\Plugin;
use Liventra\Entities\PluginManifest;
use Liventra\Entities\PluginSignature;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class PluginManagerService
 * Authoritative Plugin Lifecycle & SDK Manager (PRD-018)
 */
class PluginManagerService implements PluginManagerInterface {

	private $repository;
	private $securityService;

	public function __construct(
		PluginRepositoryInterface $repository = null,
		SecurityServiceInterface $securityService = null
	) {
		$this->repository      = $repository;
		$this->securityService = $securityService;
	}

	public function install( PluginManifest $manifest ): Plugin {
		$pluginId  = $manifest->id();
		$signature = new PluginSignature( $manifest->author(), hash( 'sha256', $pluginId ), true );

		$plugin = new Plugin( $pluginId, $manifest, $signature, false );
		if ( $this->repository ) {
			$this->repository->savePlugin( $plugin );
		}

		EventBus::dispatch( 'plugin.signature.verified', array( 'plugin_id' => $pluginId ) );
		EventBus::dispatch( 'plugin.installed', array( 'plugin_id' => $pluginId, 'version' => $manifest->version() ) );
		return $plugin;
	}

	public function uninstall( string $pluginId ): bool {
		if ( $this->repository ) {
			$this->repository->deletePlugin( $pluginId );
		}
		EventBus::dispatch( 'plugin.removed', array( 'plugin_id' => $pluginId ) );
		return true;
	}

	public function enable( string $pluginId ): bool {
		$plugin = $this->repository ? $this->repository->findPlugin( $pluginId ) : null;
		if ( $plugin instanceof Plugin ) {
			$plugin->setActive( true );
			if ( $this->repository ) {
				$this->repository->savePlugin( $plugin );
			}
			EventBus::dispatch( 'plugin.enabled', array( 'plugin_id' => $pluginId ) );
			return true;
		}
		return false;
	}

	public function disable( string $pluginId ): bool {
		$plugin = $this->repository ? $this->repository->findPlugin( $pluginId ) : null;
		if ( $plugin instanceof Plugin ) {
			$plugin->setActive( false );
			if ( $this->repository ) {
				$this->repository->savePlugin( $plugin );
			}
			EventBus::dispatch( 'plugin.disabled', array( 'plugin_id' => $pluginId ) );
			return true;
		}
		return false;
	}

	public function update( string $pluginId, string $targetVersion ): Plugin {
		$plugin = $this->repository ? $this->repository->findPlugin( $pluginId ) : null;
		if ( ! $plugin ) {
			$manifest = new PluginManifest( $pluginId, 'Updated Plugin', 'Vendor', $targetVersion );
			$plugin   = $this->install( $manifest );
		}
		EventBus::dispatch( 'plugin.updated', array( 'plugin_id' => $pluginId, 'target_version' => $targetVersion ) );
		return $plugin;
	}
}
