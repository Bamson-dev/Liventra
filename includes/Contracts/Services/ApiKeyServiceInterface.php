<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\ApiKey;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface ApiKeyServiceInterface
 * Public Contract for API Key Management & Validation (PRD-014 Part 1 & 6)
 */
interface ApiKeyServiceInterface {

	/**
	 * Issue new API key
	 *
	 * @param int    $userId User ID.
	 * @param string $name Descriptive key name.
	 * @param array  $capabilities Capabilities array.
	 * @return ApiKey
	 */
	public function issueApiKey( int $userId, string $name, array $capabilities = array() ): ApiKey;

	/**
	 * Revoke API key
	 *
	 * @param string $keyId Key ID.
	 * @return bool
	 */
	public function revokeApiKey( string $keyId ): bool;

	/**
	 * Validate API key
	 *
	 * @param string $keyString Raw API key string.
	 * @return ApiKey|null
	 */
	public function validateApiKey( string $keyString ): ?ApiKey;
}
