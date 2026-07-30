<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\WebhookServiceInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class WebhookController
 * Thin REST Controller for Webhook Subscriptions (PRD-014 Part 16)
 */
class WebhookController {

	private $webhookService;

	public function __construct( WebhookServiceInterface $webhookService ) {
		$this->webhookService = $webhookService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/webhooks/subscriptions', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'register_subscription' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function register_subscription( $request ) {
		$params = $request->get_json_params() ?? array();
		$sub    = $this->webhookService->registerWebhook(
			(string) ( $params['target_url'] ?? '' ),
			(array) ( $params['events'] ?? array( '*' ) )
		);
		return rest_ensure_response( array( 'subscription_id' => $sub->subscriptionId() ) );
	}
}
