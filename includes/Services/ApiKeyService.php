<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\ApiKeyServiceInterface;
use Liventra\Contracts\Repositories\ApiRepositoryInterface;
use Liventra\Entities\ApiKey;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiKeyService
 * Authoritative API Key Management Service Implementation (PRD-014 Part 6)
 */
class ApiKeyService implements ApiKeyServiceInterface {

	private $apiRepository;

	public function __construct( ApiRepositoryInterface $apiRepository = null ) {
		$this->apiRepository = $apiRepository;
	}

	public function issueApiKey( int $userId, string $name, array $capabilities = array() ): ApiKey {
		$keyId  = 'key_' . wp_generate_uuid4();
		$secret = 'live_sk_' . wp_generate_uuid4();

		$apiKey = new ApiKey( $keyId, $secret, $userId, $name, $capabilities );
		if ( $this->apiRepository ) {
			$this->apiRepository->saveApiKey( $apiKey );
		}

		EventBus::dispatch( 'api.key.created', array( 'key_id' => $keyId, 'user_id' => $userId ) );
		return $apiKey;
	}

	public function revokeApiKey( string $keyId ): bool {
		EventBus::dispatch( 'api.key.revoked', array( 'key_id' => $keyId ) );
		return true;
	}

	public function validateApiKey( string $keyString ): ?ApiKey {
		$key = $this->apiRepository ? $this->apiRepository->findApiKey( $keyString ) : null;
		if ( $key && ! $key->isRevoked() && ! $key->isExpired() ) {
			return $key;
		}
		return null;
	}
}
