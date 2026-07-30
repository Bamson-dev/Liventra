<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\WebhookSubscription;
use Liventra\Entities\WebhookDelivery;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface WebhookServiceInterface
 * Public Contract for Outbound Webhook Subscriptions & Deliveries (PRD-014 Part 1 & 7)
 */
interface WebhookServiceInterface {

	/**
	 * Register new webhook subscription
	 *
	 * @param string $targetUrl Destination URL.
	 * @param array  $events Subscribed event names.
	 * @param string $secret Signing secret.
	 * @return WebhookSubscription
	 */
	public function registerWebhook( string $targetUrl, array $events, string $secret = '' ): WebhookSubscription;

	/**
	 * Deliver webhook payload for event trigger
	 *
	 * @param string $eventName Event name.
	 * @param array  $payload Event payload data.
	 * @return array Array of WebhookDelivery entities.
	 */
	public function triggerEvent( string $eventName, array $payload ): array;

	/**
	 * Generate HMAC signature for webhook payload
	 *
	 * @param string $payload Body string.
	 * @param string $secret Secret string.
	 * @return string Signature.
	 */
	public function generateWebhookSignature( string $payload, string $secret ): string;

	/**
	 * Verify webhook signature
	 *
	 * @param string $payload Body string.
	 * @param string $signature Signature string.
	 * @param string $secret Secret string.
	 * @return bool
	 */
	public function verifyWebhookSignature( string $payload, string $signature, string $secret ): bool;
}
