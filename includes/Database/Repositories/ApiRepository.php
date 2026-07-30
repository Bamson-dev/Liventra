<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\ApiRepositoryInterface;
use Liventra\Entities\ApiKey;
use Liventra\Entities\WebhookSubscription;
use Liventra\Entities\WebhookDelivery;
use Liventra\Entities\IdempotencyKey;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiRepository
 * Persistence implementation for Integration Platform (PRD-003 & PRD-014)
 */
class ApiRepository implements ApiRepositoryInterface {

	private $inMemoryApiKeys        = array();
	private $inMemorySubscribers     = array();
	private $inMemoryDeliveries      = array();
	private $inMemoryIdempotencyKeys = array();

	public function saveApiKey( ApiKey $key ): bool {
		$this->inMemoryApiKeys[ $key->secretKey() ] = $key;
		return true;
	}

	public function findApiKey( string $keyString ): ?ApiKey {
		return isset( $this->inMemoryApiKeys[ $keyString ] ) ? $this->inMemoryApiKeys[ $keyString ] : null;
	}

	public function saveWebhookSubscription( WebhookSubscription $sub ): bool {
		$this->inMemorySubscribers[ $sub->subscriptionId() ] = $sub;
		return true;
	}

	public function getWebhookSubscriptions( string $eventName = '' ): array {
		if ( empty( $eventName ) ) return array_values( $this->inMemorySubscribers );

		$filtered = array();
		foreach ( $this->inMemorySubscribers as $sub ) {
			if ( in_array( $eventName, $sub->events(), true ) || in_array( '*', $sub->events(), true ) ) {
				$filtered[] = $sub;
			}
		}
		return $filtered;
	}

	public function saveWebhookDelivery( WebhookDelivery $delivery ): bool {
		$this->inMemoryDeliveries[] = $delivery;
		return true;
	}

	public function saveIdempotencyKey( IdempotencyKey $key ): bool {
		$this->inMemoryIdempotencyKeys[ $key->key() ] = $key;
		return true;
	}

	public function findIdempotencyKey( string $keyString ): ?IdempotencyKey {
		return isset( $this->inMemoryIdempotencyKeys[ $keyString ] ) ? $this->inMemoryIdempotencyKeys[ $keyString ] : null;
	}
}
