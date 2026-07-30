<?php
namespace Liventra\Events;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class EventTypeRegistry
 * Centralized registry for standard Liventra event types (Part 5)
 */
class EventTypeRegistry {

	/**
	 * Standard Core Event Types
	 */
	const CTA_SHOW              = 'cta.show';
	const CTA_HIDE              = 'cta.hide';
	const POLL_OPEN             = 'poll.open';
	const POLL_CLOSE            = 'poll.close';
	const CHAT_MESSAGE          = 'chat.message';
	const PURCHASE_NOTIFICATION = 'purchase.notification';
	const VIEWER_UPDATE         = 'viewer.update';
	const SCARCITY_UPDATE       = 'scarcity.update';
	const BONUS_UNLOCK          = 'bonus.unlock';
	const ANALYTICS_MARKER      = 'analytics.marker';
	const SYSTEM_EVENT          = 'system.event';

	private static $customTypes = array();

	/**
	 * Register a custom event type for third-party extensions
	 *
	 * @param string $eventType Custom type string (e.g. 'crm.sync').
	 */
	public static function registerCustomType( string $eventType ) {
		self::$customTypes[ $eventType ] = true;
	}

	/**
	 * Check if an event type is valid
	 *
	 * @param string $eventType Event type identifier.
	 * @return bool
	 */
	public static function isValid( string $eventType ): bool {
		$standard = array(
			self::CTA_SHOW, self::CTA_HIDE,
			self::POLL_OPEN, self::POLL_CLOSE,
			self::CHAT_MESSAGE, self::PURCHASE_NOTIFICATION,
			self::VIEWER_UPDATE, self::SCARCITY_UPDATE,
			self::BONUS_UNLOCK, self::ANALYTICS_MARKER,
			self::SYSTEM_EVENT,
			'cta', 'poll', 'chat', 'purchase', 'notification', 'scarcity', 'countdown' // Fallback legacy types
		);

		return in_array( $eventType, $standard, true ) || isset( self::$customTypes[ $eventType ] );
	}
}
