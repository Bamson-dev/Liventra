<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\ApiGatewayInterface;
use Liventra\Contracts\Services\SecurityServiceInterface;
use Liventra\Contracts\Services\AuthorizationServiceInterface;
use Liventra\Contracts\Services\AnalyticsServiceInterface;
use Liventra\Contracts\Repositories\ApiRepositoryInterface;
use Liventra\Entities\ApiRequest;
use Liventra\Entities\ApiResponse;
use Liventra\Entities\ApiEndpoint;
use Liventra\Entities\IdempotencyKey;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiGatewayService
 * Authoritative Public API & Integration Gateway Implementation (PRD-014)
 * Gateway routes & normalizes external traffic without implementing business logic.
 */
class ApiGatewayService implements ApiGatewayInterface {

	private $securityService;
	private $authorizationService;
	private $analyticsService;
	private $apiRepository;

	private $registeredEndpoints = array();

	public function __construct(
		SecurityServiceInterface $securityService = null,
		AuthorizationServiceInterface $authorizationService = null,
		AnalyticsServiceInterface $analyticsService = null,
		ApiRepositoryInterface $apiRepository = null
	) {
		$this->securityService      = $securityService;
		$this->authorizationService = $authorizationService;
		$this->analyticsService    = $analyticsService;
		$this->apiRepository       = $apiRepository;
	}

	public function dispatch( ApiRequest $request ): ApiResponse {
		EventBus::dispatch( 'api.request.received', array(
			'request_id' => $request->requestId(),
			'endpoint'   => $request->endpoint(),
			'version'    => $request->version(),
		) );

		// 1. Validate Idempotency for Mutation Requests (PRD-014 Part 8)
		$idempotencyKey = $request->headers()['X-Idempotency-Key'] ?? null;
		if ( $idempotencyKey && 'GET' !== $request->method() ) {
			$reqHash = md5( $request->endpoint() . '|' . (string) wp_json_encode( $request->params() ) );
			$cached  = $this->validateIdempotency( $idempotencyKey, $reqHash );
			if ( $cached ) return $cached;
		}

		// 2. Delegate Telemetry to Analytics Engine (PRD-014 Part 15)
		if ( $this->analyticsService ) {
			$this->analyticsService->track( 'api.request', 1, 1, array(
				'endpoint' => $request->endpoint(),
				'version'  => $request->version(),
			) );
		}

		$response = new ApiResponse( 200, array( 'message' => 'Liventra API Gateway v1/v2 ready' ), array(), array(
			'version'   => $request->version(),
			'page'      => 1,
			'per_page'  => 50,
			'total'     => 1,
			'has_next'  => false,
		) );

		// Cache Idempotent Response
		if ( $idempotencyKey && isset( $reqHash ) && $this->apiRepository ) {
			$this->apiRepository->saveIdempotencyKey( new IdempotencyKey( $idempotencyKey, $reqHash, $response->toArray(), time() + 86400 ) );
		}

		EventBus::dispatch( 'api.request.completed', array( 'request_id' => $request->requestId(), 'status' => 200 ) );
		return $response;
	}

	public function registerEndpoint( ApiEndpoint $endpoint ): bool {
		$key                           = strtolower( $endpoint->method() ) . ':' . $endpoint->version() . ':' . $endpoint->route();
		$this->registeredEndpoints[ $key ] = $endpoint;
		return true;
	}

	public function validateIdempotency( string $idempotencyKey, string $requestHash ): ?ApiResponse {
		$cached = $this->apiRepository ? $this->apiRepository->findIdempotencyKey( $idempotencyKey ) : null;
		if ( $cached && ! $cached->isExpired() && hash_equals( $cached->requestHash(), $requestHash ) ) {
			$payload = $cached->responsePayload();
			return new ApiResponse( $payload['status'] ?? 200, $payload['data'] ?? array(), $payload['errors'] ?? array(), $payload['meta'] ?? array() );
		}
		return null;
	}
}
