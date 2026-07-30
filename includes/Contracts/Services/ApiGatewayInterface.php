<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\ApiRequest;
use Liventra\Entities\ApiResponse;
use Liventra\Entities\ApiEndpoint;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface ApiGatewayInterface
 * Public Contract for API Gateway & Versioned REST Dispatcher (PRD-014 Part 1)
 */
interface ApiGatewayInterface {

	/**
	 * Dispatch incoming API request through authentication, rate limiting & version routing
	 *
	 * @param ApiRequest $request Request object.
	 * @return ApiResponse
	 */
	public function dispatch( ApiRequest $request ): ApiResponse;

	/**
	 * Register extension custom API endpoint (PRD-014 Part 13)
	 *
	 * @param ApiEndpoint $endpoint Endpoint entity.
	 * @return bool
	 */
	public function registerEndpoint( ApiEndpoint $endpoint ): bool;

	/**
	 * Validate request idempotency key (PRD-014 Part 8)
	 *
	 * @param string $idempotencyKey Key string.
	 * @param string $requestHash Request payload hash.
	 * @return ApiResponse|null Cached response if duplicate, null if fresh.
	 */
	public function validateIdempotency( string $idempotencyKey, string $requestHash ): ?ApiResponse;
}
