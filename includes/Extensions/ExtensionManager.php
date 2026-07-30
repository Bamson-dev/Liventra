<?php
namespace Liventra\Extensions;

use Liventra\Contracts\Events\EventHandlerInterface;
use Liventra\Registries\EventRegistry;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ExtensionManager
 * Central Extension Manager & Dependency Validation Engine (Part 6 & 7)
 */
class ExtensionManager {

	private static $registeredExtensions = array();

	/**
	 * Register an extension module or handler with dependency validation (Part 7)
	 *
	 * @param EventHandlerInterface|object $extension Extension handler or module instance.
	 * @param array                        $requirements Compatibility requirements.
	 * @return bool True if registered, throws Exception if incompatible.
	 */
	public static function registerExtension( $extension, array $requirements = array() ) {
		// Validate PHP Version Requirement
		if ( isset( $requirements['min_php_version'] ) ) {
			if ( version_compare( PHP_VERSION, $requirements['min_php_version'], '<' ) ) {
				throw new \RuntimeException( "Extension requirement failed: PHP version [{$requirements['min_php_version']}] required, [PHP_VERSION] installed." );
			}
		}

		// Validate Liventra Core Version Requirement
		if ( isset( $requirements['min_liventra_version'] ) && defined( 'LIVENTRA_VERSION' ) ) {
			if ( version_compare( LIVENTRA_VERSION, $requirements['min_liventra_version'], '<' ) ) {
				throw new \RuntimeException( "Extension requirement failed: Liventra version [{$requirements['min_liventra_version']}] required." );
			}
		}

		// Validate Conflicting Modules
		if ( isset( $requirements['conflicts'] ) && is_array( $requirements['conflicts'] ) ) {
			foreach ( $requirements['conflicts'] as $conflict ) {
				if ( isset( self::$registeredExtensions[ $conflict ] ) ) {
					throw new \RuntimeException( "Extension conflict detected with module [{$conflict}]." );
				}
			}
		}

		$name = isset( $requirements['name'] ) ? $requirements['name'] : ( is_object( $extension ) ? get_class( $extension ) : 'Extension' );

		if ( $extension instanceof EventHandlerInterface ) {
			EventRegistry::registerHandler( $extension );
		}

		self::$registeredExtensions[ $name ] = array(
			'instance'     => $extension,
			'requirements' => $requirements,
		);

		return true;
	}

	public static function getRegisteredExtensions(): array {
		return self::$registeredExtensions;
	}

	public static function reset() {
		self::$registeredExtensions = array();
	}
}
