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
		// Register WP Admin menu and assets when in admin context
		if ( function_exists( 'add_action' ) ) {
			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		}
	}

	public function boot() {
		// Module runtime boot
	}

	public function register_admin_menu() {
		if ( function_exists( 'add_menu_page' ) ) {
			add_menu_page(
				__( 'Liventra Studio', 'liventra' ),
				__( 'Liventra', 'liventra' ),
				'manage_options',
				'liventra',
				array( $this, 'render_admin_dashboard' ),
				'dashicons-video-alt3',
				30
			);

			add_submenu_page(
				'liventra',
				__( 'Admin Studio', 'liventra' ),
				__( 'Studio & Builder', 'liventra' ),
				'manage_options',
				'liventra',
				array( $this, 'render_admin_dashboard' )
			);

			add_submenu_page(
				'liventra',
				__( 'Organizations & Workspaces', 'liventra' ),
				__( 'Organizations', 'liventra' ),
				'manage_options',
				'liventra-organizations',
				array( $this, 'render_organizations_page' )
			);

			add_submenu_page(
				'liventra',
				__( 'Plugins & Marketplace', 'liventra' ),
				__( 'Plugins & SDK', 'liventra' ),
				'manage_options',
				'liventra-plugins',
				array( $this, 'render_plugins_page' )
			);

			add_submenu_page(
				'liventra',
				__( 'Operations & Observability', 'liventra' ),
				__( 'Operations & Health', 'liventra' ),
				'manage_options',
				'liventra-operations',
				array( $this, 'render_operations_page' )
			);

			add_submenu_page(
				'liventra',
				__( 'Performance & Capacity', 'liventra' ),
				__( 'Performance', 'liventra' ),
				'manage_options',
				'liventra-performance',
				array( $this, 'render_performance_page' )
			);
		}
	}

	public function enqueue_admin_assets( $hook ) {
		if ( false === strpos( $hook, 'liventra' ) ) {
			return;
		}

		if ( function_exists( 'wp_enqueue_style' ) && function_exists( 'plugins_url' ) ) {
			wp_enqueue_style( 'liventra-admin-css', plugins_url( 'assets/css/admin.css', LIVENTRA_FILE ), array(), LIVENTRA_VERSION );
			wp_enqueue_script( 'liventra-admin-studio-js', plugins_url( 'assets/js/admin-studio.js', LIVENTRA_FILE ), array(), LIVENTRA_VERSION, true );
			wp_enqueue_script( 'liventra-org-manager-js', plugins_url( 'assets/js/organization-manager.js', LIVENTRA_FILE ), array(), LIVENTRA_VERSION, true );
			wp_enqueue_script( 'liventra-plugin-manager-js', plugins_url( 'assets/js/plugin-manager.js', LIVENTRA_FILE ), array(), LIVENTRA_VERSION, true );
			wp_enqueue_script( 'liventra-ops-dashboard-js', plugins_url( 'assets/js/operations-dashboard.js', LIVENTRA_FILE ), array(), LIVENTRA_VERSION, true );
			wp_enqueue_script( 'liventra-perf-dashboard-js', plugins_url( 'assets/js/performance-dashboard.js', LIVENTRA_FILE ), array(), LIVENTRA_VERSION, true );
		}
	}

	public function render_admin_dashboard() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Liventra Admin Studio & Visual Webinar Builder', 'liventra' ) . '</h1>';
		echo '<div id="liventra-admin-studio"></div>';
		echo '<script>document.addEventListener("DOMContentLoaded", function() { if (window.LiventraAdminStudio) { new LiventraAdminStudio({ containerId: "liventra-admin-studio" }); } });</script>';
		echo '</div>';
	}

	public function render_organizations_page() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Enterprise Organizations & Workspaces', 'liventra' ) . '</h1>';
		echo '<div id="liventra-org-manager"></div>';
		echo '<script>document.addEventListener("DOMContentLoaded", function() { if (window.LiventraOrganizationManager) { new LiventraOrganizationManager({ containerId: "liventra-org-manager" }); } });</script>';
		echo '</div>';
	}

	public function render_plugins_page() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Plugin SDK & Marketplace Catalog', 'liventra' ) . '</h1>';
		echo '<div id="liventra-plugin-manager"></div>';
		echo '<script>document.addEventListener("DOMContentLoaded", function() { if (window.LiventraPluginManager) { new LiventraPluginManager({ containerId: "liventra-plugin-manager" }); } });</script>';
		echo '</div>';
	}

	public function render_operations_page() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Operations, Health & Observability', 'liventra' ) . '</h1>';
		echo '<div id="liventra-operations-dashboard"></div>';
		echo '<script>document.addEventListener("DOMContentLoaded", function() { if (window.LiventraOperationsDashboard) { new LiventraOperationsDashboard({ containerId: "liventra-operations-dashboard" }); } });</script>';
		echo '</div>';
	}

	public function render_performance_page() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Performance, Scalability & Capacity Dashboard', 'liventra' ) . '</h1>';
		echo '<div id="liventra-performance-dashboard"></div>';
		echo '<script>document.addEventListener("DOMContentLoaded", function() { if (window.LiventraPerformanceDashboard) { new LiventraPerformanceDashboard({ containerId: "liventra-performance-dashboard" }); } });</script>';
		echo '</div>';
	}
}
