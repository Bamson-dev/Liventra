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
	 * Boot all registered modules
	 */
	public function boot() {
		if ( $this->booted ) {
			return;
		}

		foreach ( $this->modules as $module ) {
			$module->boot();
		}

		$this->booted = true;
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
