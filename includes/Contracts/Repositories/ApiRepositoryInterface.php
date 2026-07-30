<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\ApiKey;
use Liventra\Entities\WebhookSubscription;
use Liventra\Entities\WebhookDelivery;
use Liventra\Entities\IdempotencyKey;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface ApiRepositoryInterface
 * Persistence contract for Public API & Integration Platform (PRD-014 Part 3)
 */
interface ApiRepositoryInterface {

	public function saveApiKey( ApiKey $key ): bool;
	public function findApiKey( string $keyString ): ?ApiKey;
	public function saveWebhookSubscription( WebhookSubscription $sub ): bool;
	public function getWebhookSubscriptions( string $eventName = '' ): array;
	public function saveWebhookDelivery( WebhookDelivery $delivery ): bool;
	public function saveIdempotencyKey( IdempotencyKey $key ): bool;
	public function findIdempotencyKey( string $keyString ): ?IdempotencyKey;
}
