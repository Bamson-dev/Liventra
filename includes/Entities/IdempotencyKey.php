<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class IdempotencyKey
 * Domain Entity representing Request Idempotency Key (PRD-014 Part 2 & 8)
 */
class IdempotencyKey {

	private $key;
	private $requestHash;
	private $responsePayload;
	private $expiresAt;

	public function __construct( string $key, string $requestHash, array $responsePayload, int $expiresAt ) {
		$this->key             = $key;
		$this->requestHash     = $requestHash;
		$this->responsePayload = $responsePayload;
		$this->expiresAt       = $expiresAt;
	}

	public function key(): string { return $this->key; }
	public function requestHash(): string { return $this->requestHash; }
	public function responsePayload(): array { return $this->responsePayload; }
	public function isExpired(): bool { return time() > $this->expiresAt; }
}
