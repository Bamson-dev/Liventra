<?php
namespace Liventra;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class EventBus
 * Central Event Dispatcher for Decoupled Module Communication (PRD-002 Section 8)
 */
class EventBus {

	/**
	 * Listener Callbacks indexed by event name
	 *
	 * @var array
	 */
	protected static $listeners = array();

	/**
	 * Subscribe a listener callback to an event
	 *
	 * @param string   $event_name Name of event (e.g. 'session.started', 'timeline.cta_triggered').
	 * @param callable $callback   Callback function receiving payload data.
	 * @param int      $priority   Priority (lower numbers execute earlier).
	 */
	public static function on( $event_name, callable $callback, $priority = 10 ) {
		if ( ! isset( self::$listeners[ $event_name ] ) ) {
			self::$listeners[ $event_name ] = array();
		}

		self::$listeners[ $event_name ][] = array(
			'callback' => $callback,
			'priority' => $priority,
		);

		// Sort by priority
		usort( self::$listeners[ $event_name ], function( $a, $b ) {
			return $a['priority'] - $b['priority'];
		} );
	}

	/**
	 * Dispatch an event to all registered listeners
	 *
	 * @param string $event_name Name of event.
	 * @param mixed  $payload    Data passed to listener callbacks.
	 * @return array Results returned by listeners.
	 */
	public static function dispatch( $event_name, $payload = null ) {
		$results = array();

		if ( ! isset( self::$listeners[ $event_name ] ) ) {
			return $results;
		}

		foreach ( self::$listeners[ $event_name ] as $listener ) {
			$results[] = call_user_func( $listener['callback'], $payload, $event_name );
		}

		return $results;
	}

	/**
	 * Remove a registered listener or all listeners for an event
	 *
	 * @param string        $event_name Event name.
	 * @param callable|null $callback   Specific callback to remove, or null to clear all.
	 */
	public static function off( $event_name, callable $callback = null ) {
		if ( ! isset( self::$listeners[ $event_name ] ) ) {
			return;
		}

		if ( null === $callback ) {
			unset( self::$listeners[ $event_name ] );
			return;
		}

		self::$listeners[ $event_name ] = array_filter(
			self::$listeners[ $event_name ],
			function( $listener ) use ( $callback ) {
				return $listener['callback'] !== $callback;
			}
		);
	}

	/**
	 * Reset all event listeners (useful for testing)
	 */
	public static function reset() {
		self::$listeners = array();
	}
}
