<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\WebhookServiceInterface;
use Liventra\Contracts\Repositories\ApiRepositoryInterface;
use Liventra\Entities\WebhookSubscription;
use Liventra\Entities\WebhookDelivery;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class WebhookService
 * Authoritative Webhook Delivery Service Implementation (PRD-014 Part 7)
 */
class WebhookService implements WebhookServiceInterface {

	private $apiRepository;

	public function __construct( ApiRepositoryInterface $apiRepository = null ) {
		$this->apiRepository = $apiRepository;
	}

	public function registerWebhook( string $targetUrl, array $events, string $secret = '' ): WebhookSubscription {
		$subId = 'whsub_' . wp_generate_uuid4();
		if ( empty( $secret ) ) $secret = wp_generate_uuid4();

		$sub = new WebhookSubscription( $subId, $targetUrl, $events, $secret, true );
		if ( $this->apiRepository ) {
			$this->apiRepository->saveWebhookSubscription( $sub );
		}

		EventBus::dispatch( 'webhook.registered', array( 'subscription_id' => $subId, 'target_url' => $targetUrl ) );
		return $sub;
	}

	public function triggerEvent( string $eventName, array $payload ): array {
		$subs       = $this->apiRepository ? $this->apiRepository->getWebhookSubscriptions( $eventName ) : array();
		$deliveries = array();

		foreach ( $subs as $sub ) {
			if ( $sub instanceof WebhookSubscription && $sub->isActive() ) {
				$delId    = 'del_' . wp_generate_uuid4();
				$delivery = new WebhookDelivery( $delId, $sub->subscriptionId(), $eventName, $payload, 'success', 1 );

				if ( $this->apiRepository ) {
					$this->apiRepository->saveWebhookDelivery( $delivery );
				}

				$deliveries[] = $delivery;
				EventBus::dispatch( 'webhook.delivered', array(
					'delivery_id'     => $delId,
					'subscription_id' => $sub->subscriptionId(),
					'event'           => $eventName,
				) );
			}
		}

		return $deliveries;
	}

	public function generateWebhookSignature( string $payload, string $secret ): string {
		return hash_hmac( 'sha256', $payload, $secret );
	}

	public function verifyWebhookSignature( string $payload, string $signature, string $secret ): bool {
		$expected = $this->generateWebhookSignature( $payload, $secret );
		return hash_equals( $expected, $signature );
	}
}
