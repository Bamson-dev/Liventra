<?php
namespace Liventra;

use Liventra\Database\Migrator;
use Liventra\Modules\ModuleInterface;
use Liventra\Modules\Webinar\WebinarModule;
use Liventra\Modules\Registration\RegistrationModule;
use Liventra\Modules\Session\SessionModule;
use Liventra\Modules\Timeline\TimelineModule;
use Liventra\Modules\Video\VideoModule;
use Liventra\Modules\CTA\CtaModule;
use Liventra\Modules\Chat\ChatModule;
use Liventra\Modules\Notification\NotificationModule;
use Liventra\Modules\Analytics\AnalyticsModule;
use Liventra\Modules\Cloud\CloudModule;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Plugin
 * Core Singleton Plugin Container (PRD-002 Section 4: Module 1 — Core Plugin)
 */
class Plugin {

	/**
	 * Singleton Instance
	 *
	 * @var Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Registered Subsystem Modules
	 *
	 * @var ModuleInterface[]
	 */
	protected $modules = array();

	/**
	 * Is booted flag
	 *
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * Get Singleton Instance
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor
	 */
	private function __construct() {
		$this->register_default_modules();
	}

	/**
	 * Register Core Modules
	 */
	protected function register_default_modules() {
		$this->register_module( new WebinarModule() );
		$this->register_module( new RegistrationModule() );
		$this->register_module( new SessionModule() );
		$this->register_module( new TimelineModule() );
		$this->register_module( new VideoModule() );
		$this->register_module( new CtaModule() );
		$this->register_module( new ChatModule() );
		$this->register_module( new NotificationModule() );
		$this->register_module( new AnalyticsModule() );
		$this->register_module( new CloudModule() );
	}

	/**
	 * Register a module with the plugin container
	 *
	 * @param ModuleInterface $module Module instance.
	 */
	public function register_module( ModuleInterface $module ) {
		$this->modules[ $module->get_name() ] = $module;
		$module->register();
	}

	/**
	 * Get a registered module by name
	 *
	 * @param string $name Module identifier.
	 * @return ModuleInterface|null
	 */
	public function get_module( $name ) {
		return isset( $this->modules[ $name ] ) ? $this->modules[ $name ] : null;
	}

	/**
	 * Get all registered modules
	 *
	 * @return ModuleInterface[]
	 */
	public function get_modules() {
		return $this->modules;
	}

	/**
	 * Boot all registered modules and REST API routes
	 */
	public function boot() {
		if ( $this->booted ) {
			return;
		}

		foreach ( $this->modules as $module ) {
			$module->boot();
		}

		if ( function_exists( 'add_action' ) ) {
			add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		}

		$this->booted = true;
	}

	/**
	 * Register all REST API Controllers on rest_api_init hook
	 */
	public function register_rest_routes() {
		$container = Container::getInstance();

		// Bind service interfaces to concrete implementations
		$container->bind( 'Liventra\Contracts\Services\AnalyticsServiceInterface', 'Liventra\Services\AnalyticsService' );
		$container->bind( 'Liventra\Contracts\Services\AdminStudioServiceInterface', function( $c ) {
			return new \Liventra\Services\AdminStudioService(
				null, null, null, null, null,
				$c->get( 'Liventra\Contracts\Services\AnalyticsServiceInterface' )
			);
		} );
		$container->bind( 'Liventra\Contracts\Services\OrganizationServiceInterface', 'Liventra\Services\OrganizationService' );
		$container->bind( 'Liventra\Contracts\Services\PluginManagerInterface', 'Liventra\Services\PluginManagerService' );
		$container->bind( 'Liventra\Contracts\Services\ObservabilityServiceInterface', 'Liventra\Services\ObservabilityService' );
		$container->bind( 'Liventra\Contracts\Services\PerformanceServiceInterface', 'Liventra\Services\PerformanceService' );

		// Register REST Controllers
		try {
			$adminStudioService = $container->get( 'Liventra\Contracts\Services\AdminStudioServiceInterface' );
			$adminStudioCtrl    = new \Liventra\REST\AdminStudioController( $adminStudioService );
			$adminStudioCtrl->register_routes();

			$orgService = $container->get( 'Liventra\Contracts\Services\OrganizationServiceInterface' );
			$orgCtrl    = new \Liventra\REST\OrganizationController( $orgService );
			$orgCtrl->register_routes();

			$pluginService = new \Liventra\Services\PluginManagerService();
			$pluginCtrl    = new \Liventra\REST\PluginController( $pluginService );
			$pluginCtrl->register_routes();

			$mktCtrl = new \Liventra\REST\MarketplaceController( new \Liventra\Services\MarketplaceService() );
			$mktCtrl->register_routes();

			$opsService = new \Liventra\Services\ObservabilityService();
			$opsCtrl    = new \Liventra\REST\ObservabilityController( $opsService );
			$opsCtrl->register_routes();

			$perfService = new \Liventra\Services\PerformanceService();
			$perfCtrl    = new \Liventra\REST\PerformanceController( $perfService );
			$perfCtrl->register_routes();

			$sessionService = new \Liventra\Services\SessionService();
			$sessionCtrl    = new \Liventra\REST\SessionController( $sessionService );
			$sessionCtrl->register_routes();

			$supabaseService = new \Liventra\Services\SupabaseService();
			$supabaseCtrl    = new \Liventra\REST\SupabaseController( $supabaseService );
			$supabaseCtrl->register_routes();
		} catch ( \Exception $e ) {
			// Silent catch to prevent boot crashes
		}
	}

	/**
	 * Plugin Activation Handler
	 */
	public static function activate() {
		Migrator::run();
	}

	/**
	 * Plugin Deactivation Handler
	 */
	public static function deactivate() {
		// Clean transient caches
	}
}
