<?php
namespace Liventra\Modules\Webinar;

use Liventra\Modules\ModuleInterface;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class WebinarModule
 * Module 2 — Webinar Management (PRD-002 Section 4)
 * Owns webinar creation, editing, cloning, configuration.
 * Never owns playback synchronization.
 */
class WebinarModule implements ModuleInterface {

	public function get_name() {
		return 'webinar';
	}

	public function register() {
		// Register WP Admin menu and hooks when in admin context
		if ( function_exists( 'add_action' ) ) {
			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		}
	}

	public function boot() {
		// Module runtime boot
	}

	public function register_admin_menu() {
		if ( function_exists( 'add_menu_page' ) ) {
			add_menu_page(
				__( 'Liventra Webinars', 'liventra' ),
				__( 'Liventra', 'liventra' ),
				'manage_options',
				'liventra',
				array( $this, 'render_admin_dashboard' ),
				'dashicons-video-alt3',
				30
			);
		}
	}

	public function render_admin_dashboard() {
		echo '<div class="wrap"><h1>' . esc_html__( 'Liventra Evergreen Webinars', 'liventra' ) . '</h1></div>';
	}
}
