<?php
namespace Liventra;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Autoloader
 * PSR-4 Compliant Autoloader for Liventra Namespace
 */
class Autoloader {

	/**
	 * Namespace prefix
	 *
	 * @var string
	 */
	protected static $prefix = 'Liventra\\';

	/**
	 * Base directory for namespace prefix
	 *
	 * @var string
	 */
	protected static $base_dir;

	/**
	 * Register the autoloader with SPL
	 */
	public static function register( $base_dir = null ) {
		self::$base_dir = $base_dir ? rtrim( $base_dir, '/\\' ) . '/' : __DIR__ . '/';

		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoload callback
	 *
	 * @param string $class Fully-qualified class name.
	 * @return bool
	 */
	public static function autoload( $class ) {
		// Does the class use the prefix?
		$len = strlen( self::$prefix );
		if ( 0 !== strncmp( self::$prefix, $class, $len ) ) {
			return false;
		}

		// Get relative class name
		$relative_class = substr( $class, $len );

		// Replace namespace separators with directory separators, append .php
		$file = self::$base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		// If file exists, require it
		if ( file_exists( $file ) ) {
			require_once $file;
			return true;
		}

		return false;
	}
}
