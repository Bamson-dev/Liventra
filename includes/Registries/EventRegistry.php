<?php
namespace Liventra\Registries;

use Liventra\Contracts\Events\EventHandlerInterface;
use Liventra\Entities\TimelineEvent;
use Liventra\Entities\EventExecution;
use Liventra\Events\EventResult;
use Liventra\Events\HandlerMetadata;
use Liventra\Events\EventTypeRegistry;
use Liventra\Extensions\ExtensionDiagnostics;
use Liventra\Attributes\HandlesEvent;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class EventRegistry
 * Pluggable Event Registry & Handler Discovery System (Part 4, 8 & 9)
 * Supports EventHandlerInterface, PHP 8 Attributes, Legacy String Callbacks & Failure Isolation.
 */
class EventRegistry {

	/**
	 * Legacy string callbacks: [ event_type => [ callback, ... ] ]
	 */
	private static $legacyHandlers = array();

	/**
	 * Pluggable EventHandlerInterface instances: [ event_type => [ EventHandlerInterface, ... ] ]
	 */
	private static $interfaceHandlers = array();

	/**
	 * Discovery cache flag
	 */
	private static $discovered = false;

	/**
	 * Register a legacy string callback (Backward Compatibility)
	 */
	public static function register( string $eventType, callable $handler ) {
		if ( ! isset( self::$legacyHandlers[ $eventType ] ) ) {
			self::$legacyHandlers[ $eventType ] = array();
		}
		self::$legacyHandlers[ $eventType ][] = $handler;
	}

	/**
	 * Register an EventHandlerInterface implementation (Pluggable Extension API)
	 */
	public static function registerHandler( EventHandlerInterface $handler ) {
		$metadata = $handler->metadata();
		$types    = $metadata->getSupportedEventTypes();

		foreach ( $types as $type ) {
			if ( ! isset( self::$interfaceHandlers[ $type ] ) ) {
				self::$interfaceHandlers[ $type ] = array();
			}
			self::$interfaceHandlers[ $type ][] = $handler;

			// Sort by priority weight
			usort( self::$interfaceHandlers[ $type ], function( $a, $b ) {
				return $b->priority() - $a->priority();
			} );
		}
	}

	/**
	 * Attribute-based declarative registration parser (Part 3)
	 */
	public static function registerAttributeHandler( $handlerObject ) {
		if ( ! is_object( $handlerObject ) ) return;

		$reflection = new \ReflectionClass( $handlerObject );

		// Parse PHP 8 Attributes if available
		if ( method_exists( $reflection, 'getAttributes' ) ) {
			$attributes = $reflection->getAttributes( HandlesEvent::class );
			foreach ( $attributes as $attr ) {
				/** @var HandlesEvent $instance */
				$instance = $attr->newInstance();
				$type     = $instance->getEventType();

				if ( $handlerObject instanceof EventHandlerInterface ) {
					self::registerHandler( $handlerObject );
				} else {
					self::register( $type, array( $handlerObject, 'handle' ) );
				}
			}
		}
	}

	/**
	 * Dispatch event to registered subsystem and third-party handlers with failure isolation (Part 8 & 9)
	 *
	 * @param TimelineEvent $event Event entity.
	 * @return EventExecution Result object.
	 */
	public static function dispatch( TimelineEvent $event ): EventExecution {
		$type      = $event->eventType();
		$startTime = microtime( true );
		$success   = true;

		// 1. Dispatch to Pluggable EventHandlerInterface instances
		if ( isset( self::$interfaceHandlers[ $type ] ) ) {
			foreach ( self::$interfaceHandlers[ $type ] as $handler ) {
				/** @var EventHandlerInterface $handler */
				$handlerName = $handler->metadata()->getName();
				$hStart      = microtime( true );

				try {
					$result  = $handler->handle( $event );
					$hDuration = ( microtime( true ) - $hStart ) * 1000;
					ExtensionDiagnostics::recordInvocation( $handlerName, $hDuration, $result->isSuccess() );

					if ( $result->isFailed() ) {
						$success = false;
					}
				} catch ( \Throwable $e ) {
					$hDuration = ( microtime( true ) - $hStart ) * 1000;
					ExtensionDiagnostics::recordInvocation( $handlerName, $hDuration, false );
					$success = false; // Isolated failure - does not break other handlers
				}
			}
		}

		// 2. Dispatch to Legacy Callback Handlers (Backward Compatibility)
		if ( isset( self::$legacyHandlers[ $type ] ) ) {
			foreach ( self::$legacyHandlers[ $type ] as $cb ) {
				$hStart = microtime( true );
				try {
					call_user_func( $cb, $event );
					$hDuration = ( microtime( true ) - $hStart ) * 1000;
					ExtensionDiagnostics::recordInvocation( 'LegacyCallback', $hDuration, true );
				} catch ( \Throwable $e ) {
					$hDuration = ( microtime( true ) - $hStart ) * 1000;
					ExtensionDiagnostics::recordInvocation( 'LegacyCallback', $hDuration, false );
					$success = false;
				}
			}
		}

		return new EventExecution( 0, $event->uuid(), 0, $success ? 'executed' : 'failed' );
	}

	/**
	 * Reset all registered handlers (testing helper)
	 */
	public static function reset() {
		self::$legacyHandlers    = array();
		self::$interfaceHandlers = array();
		self::$discovered       = false;
		ExtensionDiagnostics::reset();
	}
}
