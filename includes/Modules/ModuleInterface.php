<?php
namespace Liventra\Modules;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface ModuleInterface
 * Base Contract for all Liventra Subsystem Modules (PRD-002 Section 4)
 */
interface ModuleInterface {

	/**
	 * Get unique module identifier
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Register module hooks, event listeners, and dependencies
	 */
	public function register();

	/**
	 * Bootstrap module runtime logic
	 */
	public function boot();
}
