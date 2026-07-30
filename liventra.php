<?php
/**
 * Plugin Name: Liventra
 * Plugin URI:  https://liventra.com
 * Description: Transform pre-recorded videos into realistic live webinar experiences on WordPress.
 * Version:     1.0.6
 * Author:      Bamidele Matthew
 * Author URI:  https://liventra.com
 * Text Domain: liventra
 * Domain Path: /languages
 * License:     GPLv2 or later
 * Requires at least: 5.8
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Plugin Constants
define( 'LIVENTRA_VERSION', '1.0.6' );
define( 'LIVENTRA_MIN_PHP_VERSION', '7.4' );
define( 'LIVENTRA_MIN_WP_VERSION', '5.8' );
define( 'LIVENTRA_FILE', __FILE__ );
define( 'LIVENTRA_PATH', plugin_dir_path( __FILE__ ) );
define( 'LIVENTRA_URL', plugin_dir_url( __FILE__ ) );
define( 'LIVENTRA_BASENAME', plugin_basename( __FILE__ ) );

// Load PSR-4 Autoloader
require_once LIVENTRA_PATH . 'includes/Autoloader.php';

// Register Autoloader
\Liventra\Autoloader::register();

/**
 * Plugin Activation Callback
 */
function liventra_activate() {
	\Liventra\Plugin::activate();
}
register_activation_hook( LIVENTRA_FILE, 'liventra_activate' );

/**
 * Plugin Deactivation Callback
 */
function liventra_deactivate() {
	\Liventra\Plugin::deactivate();
}
register_deactivation_hook( LIVENTRA_FILE, 'liventra_deactivate' );

/**
 * Bootstrap Liventra Plugin Container
 */
function liventra() {
	return \Liventra\Plugin::instance();
}

// Initialize Plugin on plugins_loaded hook
add_action( 'plugins_loaded', function() {
	liventra()->boot();
} );
