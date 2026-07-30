<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\SecurityToken;
use Liventra\Entities\SecurityEvent;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface SecurityServiceInterface
 * Public Contract for Security Platform & Signed Resources (PRD-013 Part 1)
 */
interface SecurityServiceInterface {

	/**
	 * Issue cryptographically signed token (HMAC-SHA256)
	 *
	 * @param string $subjectId Subject / Attendee / User ID.
	 * @param array  $payload Token payload data.
	 * @param int    $ttlSeconds Time to live in seconds.
	 * @return SecurityToken
	 */
	public function issueSignedToken( string $subjectId, array $payload = array(), int $ttlSeconds = 86400 ): SecurityToken;

	/**
	 * Verify cryptographically signed token with timing-safe comparison
	 *
	 * @param string $tokenString Token string representation.
	 * @return SecurityToken|null
	 */
	public function verifySignedToken( string $tokenString ): ?SecurityToken;

	/**
	 * Issue signed resource URL (webinars, videos, downloads)
	 *
	 * @param string $baseUrl Base URL string.
	 * @param array  $queryParams Query arguments to sign.
	 * @param int    $ttlSeconds Time to live in seconds.
	 * @return string Signed URL string.
	 */
	public function issueSignedUrl( string $baseUrl, array $queryParams = array(), int $ttlSeconds = 3600 ): string;

	/**
	 * Verify signed resource URL
	 *
	 * @param string $signedUrl Signed URL string.
	 * @return bool
	 */
	public function verifySignedUrl( string $signedUrl ): bool;

	/**
	 * Generate CSRF / REST nonce
	 *
	 * @param string $action Action context.
	 * @return string Nonce string.
	 */
	public function generateNonce( string $action = 'liventra_api' ): string;

	/**
	 * Verify CSRF / REST nonce
	 *
	 * @param string $nonce Nonce string.
	 * @param string $action Action context.
	 * @return bool
	 */
	public function verifyNonce( string $nonce, string $action = 'liventra_api' ): bool;

	/**
	 * Check rate limit policy (token bucket algorithm) (PRD-013 Part 9)
	 *
	 * @param string $key Rate limit bucket key (e.g. IP or User ID).
	 * @param int    $maxCapacity Token bucket capacity.
	 * @param int    $refillRatePerSec Refill rate per second.
	 * @return bool True if allowed, false if rate limited.
	 */
	public function checkRateLimit( string $key, int $maxCapacity = 60, int $refillRatePerSec = 1 ): bool;

	/**
	 * Validate incoming webhook signature (Stripe, LemonSqueezy, custom) (PRD-013 Part 10)
	 *
	 * @param string $provider Provider key.
	 * @param string $payload Raw payload body.
	 * @param string $signature Signature header value.
	 * @param string $secret Verification secret.
	 * @return bool
	 */
	public function validateWebhook( string $provider, string $payload, string $signature, string $secret ): bool;

	/**
	 * Log security event
	 *
	 * @param SecurityEvent $event Security event entity.
	 * @return bool
	 */
	public function logSecurityEvent( SecurityEvent $event ): bool;
}
