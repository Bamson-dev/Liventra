<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class WebhookDelivery
 * Domain Entity representing Webhook Dispatch Delivery (PRD-014 Part 2 & 7)
 */
class WebhookDelivery {

	private $deliveryId;
	private $subscriptionId;
	private $event;
	private $payload;
	private $status;
	private $attempts;

	public function __construct(
		string $deliveryId,
		string $subscriptionId,
		string $event,
		array $payload,
		string $status = 'success',
		int $attempts = 1
	) {
		$this->deliveryId     = $deliveryId;
		$this->subscriptionId = $subscriptionId;
		$this->event          = $event;
		$this->payload        = $payload;
		$this->status         = $status;
		$this->attempts       = $attempts;
	}

	public function deliveryId(): string { return $this->deliveryId; }
	public function subscriptionId(): string { return $this->subscriptionId; }
	public function event(): string { return $this->event; }
	public function payload(): array { return $this->payload; }
	public function status(): string { return $this->status; }
	public function attempts(): int { return $this->attempts; }
}
