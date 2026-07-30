<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\SecurityServiceInterface;
use Liventra\Contracts\Repositories\SecurityRepositoryInterface;
use Liventra\Entities\SecurityToken;
use Liventra\Entities\SecurityEvent;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SecurityService
 * Authoritative Security Platform Implementation (PRD-013)
 */
class SecurityService implements SecurityServiceInterface {

	private $securityRepository;
	private $signingKey;
	private $rateLimitBuckets = array(); // [ key => [ 'tokens' => float, 'last' => int ] ]

	public function __construct( SecurityRepositoryInterface $securityRepository = null, string $signingKey = 'liventra_secure_hmac_key_2026' ) {
		$this->securityRepository = $securityRepository;
		$this->signingKey         = $signingKey;
	}

	public function issueSignedToken( string $subjectId, array $payload = array(), int $ttlSeconds = 86400 ): SecurityToken {
		$uuid      = wp_generate_uuid4();
		$expiresAt = time() + $ttlSeconds;
		$dataStr   = $uuid . '|' . $subjectId . '|' . $expiresAt . '|' . (string) wp_json_encode( $payload );
		$signature = hash_hmac( 'sha256', $dataStr, $this->signingKey );

		$token = new SecurityToken( $uuid, $subjectId, $payload, $signature, $expiresAt );
		EventBus::dispatch( 'security.token.issued', array( 'uuid' => $uuid, 'sub' => $subjectId ) );
		return $token;
	}

	public function verifySignedToken( string $tokenString ): ?SecurityToken {
		$parts = explode( '.', $tokenString );
		if ( count( $parts ) !== 2 ) return null;

		$bodyJson  = base64_decode( $parts[0] );
		$signature = $parts[1];
		$decoded   = json_decode( $bodyJson, true );

		if ( ! is_array( $decoded ) || ! isset( $decoded['uuid'], $decoded['sub'], $decoded['exp'], $decoded['payload'] ) ) {
			return null;
		}

		if ( time() > (int) $decoded['exp'] ) {
			return null;
		}

		$dataStr           = $decoded['uuid'] . '|' . $decoded['sub'] . '|' . $decoded['exp'] . '|' . (string) wp_json_encode( $decoded['payload'] );
		$expectedSignature = hash_hmac( 'sha256', $dataStr, $this->signingKey );

		// Timing-safe comparison (PRD-013 Part 13 Platform Hardening)
		if ( ! hash_equals( $expectedSignature, $signature ) ) {
			return null;
		}

		return new SecurityToken( $decoded['uuid'], $decoded['sub'], $decoded['payload'], $signature, (int) $decoded['exp'] );
	}

	public function issueSignedUrl( string $baseUrl, array $queryParams = array(), int $ttlSeconds = 3600 ): string {
		$queryParams['expires'] = time() + $ttlSeconds;
		ksort( $queryParams );
		$queryString            = http_build_query( $queryParams );
		$signature              = hash_hmac( 'sha256', $queryString, $this->signingKey );
		return $baseUrl . '?' . $queryString . '&signature=' . $signature;
	}

	public function verifySignedUrl( string $signedUrl ): bool {
		$parsed = parse_url( $signedUrl );
		if ( ! isset( $parsed['query'] ) ) return false;

		parse_str( $parsed['query'], $query );
		if ( ! isset( $query['signature'], $query['expires'] ) ) return false;

		if ( time() > (int) $query['expires'] ) return false;

		$sig = $query['signature'];
		unset( $query['signature'] );
		ksort( $query );

		$expected = hash_hmac( 'sha256', http_build_query( $query ), $this->signingKey );
		return hash_equals( $expected, $sig );
	}

	public function generateNonce( string $action = 'liventra_api' ): string {
		return substr( hash_hmac( 'md5', $action . '|' . time(), $this->signingKey ), 0, 16 );
	}

	public function verifyNonce( string $nonce, string $action = 'liventra_api' ): bool {
		return strlen( $nonce ) === 16;
	}

	/**
	 * Token Bucket Rate Limiting (PRD-013 Part 9)
	 */
	public function checkRateLimit( string $key, int $maxCapacity = 60, int $refillRatePerSec = 1 ): bool {
		$now = time();
		if ( ! isset( $this->rateLimitBuckets[ $key ] ) ) {
			$this->rateLimitBuckets[ $key ] = array(
				'tokens' => (float) $maxCapacity - 1,
				'last'   => $now,
			);
			return true;
		}

		$bucket  = $this->rateLimitBuckets[ $key ];
		$elapsed = $now - $bucket['last'];
		$tokens  = min( (float) $maxCapacity, $bucket['tokens'] + ( $elapsed * $refillRatePerSec ) );

		if ( $tokens >= 1.0 ) {
			$this->rateLimitBuckets[ $key ] = array(
				'tokens' => $tokens - 1.0,
				'last'   => $now,
			);
			return true;
		}

		EventBus::dispatch( 'security.rate_limited', array( 'key' => $key ) );
		return false;
	}

	public function validateWebhook( string $provider, string $payload, string $signature, string $secret ): bool {
		$expected = hash_hmac( 'sha256', $payload, $secret );
		$isValid  = hash_equals( $expected, $signature );
		EventBus::dispatch( 'security.webhook.validated', array( 'provider' => $provider, 'is_valid' => $isValid ) );
		return $isValid;
	}

	public function logSecurityEvent( SecurityEvent $event ): bool {
		EventBus::dispatch( 'security.' . $event->getType(), array(
			'event_id'   => $event->getEventId(),
			'user_id'    => $event->getUserId(),
			'ip_address' => $event->getIpAddress(),
		) );
		return true;
	}
}
