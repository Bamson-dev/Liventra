<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class WebhookSubscription
 * Domain Entity representing Outbound Webhook Registration (PRD-014 Part 2 & 7)
 */
class WebhookSubscription {

	private $subscriptionId;
	private $targetUrl;
	private $events;
	private $secret;
	private $active;

	public function __construct(
		string $subscriptionId,
		string $targetUrl,
		array $events = array(),
		string $secret = '',
		bool $active = true
	) {
		$this->subscriptionId = $subscriptionId;
		$this->targetUrl      = $targetUrl;
		$this->events         = $events;
		$this->secret         = $secret;
		$this->active         = $active;
	}

	public function subscriptionId(): string { return $this->subscriptionId; }
	public function targetUrl(): string { return $this->targetUrl; }
	public function events(): array { return $this->events; }
	public function secret(): string { return $this->secret; }
	public function isActive(): bool { return $this->active; }
}
