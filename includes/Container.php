<?php
namespace Liventra;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Container
 * Lightweight Dependency Injection Container for Liventra
 */
class Container {

	/**
	 * Singleton Instance
	 *
	 * @var Container|null
	 */
	private static $instance = null;

	/**
	 * Service bindings
	 *
	 * @var array
	 */
	private $services = array();

	/**
	 * Instantiated singletons
	 *
	 * @var array
	 */
	private $instances = array();

	/**
	 * Get Singleton Instance
	 *
	 * @return Container
	 */
	public static function getInstance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Bind abstract to concrete
	 *
	 * @param string          $abstract Interface or class name.
	 * @param callable|string $concrete Resolver or class name.
	 * @param bool            $singleton Whether to cache instance.
	 */
	public function bind( $abstract, $concrete, $singleton = true ) {
		$this->services[ $abstract ] = array(
			'concrete'  => $concrete,
			'singleton' => $singleton,
		);
	}

	/**
	 * Resolve instance by abstract interface
	 *
	 * @param string $abstract Interface name.
	 * @return mixed Resolved object.
	 * @throws \Exception If service not found.
	 */
	public function get( $abstract ) {
		if ( isset( $this->instances[ $abstract ] ) ) {
			return $this->instances[ $abstract ];
		}

		if ( ! isset( $this->services[ $abstract ] ) ) {
			// Fallback auto-instantiation for concrete classes
			if ( class_exists( $abstract ) ) {
				return new $abstract();
			}
			throw new \Exception( "Service [{$abstract}] is not bound in Container." );
		}

		$binding  = $this->services[ $abstract ];
		$concrete = $binding['concrete'];

		$object = is_callable( $concrete ) ? call_user_func( $concrete, $this ) : new $concrete();

		if ( $binding['singleton'] ) {
			$this->instances[ $abstract ] = $object;
		}

		return $object;
	}

	/**
	 * Reset container bindings (useful for tests)
	 */
	public function reset() {
		$this->services  = array();
		$this->instances = array();
	}
}
