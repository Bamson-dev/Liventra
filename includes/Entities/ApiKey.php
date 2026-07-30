<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiKey
 * Domain Entity representing an API Key (PRD-014 Part 2 & 6)
 */
class ApiKey {

	private $keyId;
	private $secretKey;
	private $userId;
	private $name;
	private $capabilities;
	private $expiresAt;
	private $revoked;

	public function __construct(
		string $keyId,
		string $secretKey,
		int $userId,
		string $name,
		array $capabilities = array(),
		?int $expiresAt = null,
		bool $revoked = false
	) {
		$this->keyId        = $keyId;
		$this->secretKey    = $secretKey;
		$this->userId       = $userId;
		$this->name         = $name;
		$this->capabilities = $capabilities;
		$this->expiresAt    = $expiresAt;
		$this->revoked      = $revoked;
	}

	public function keyId(): string { return $this->keyId; }
	public function secretKey(): string { return $this->secretKey; }
	public function userId(): int { return $this->userId; }
	public function name(): string { return $this->name; }
	public function capabilities(): array { return $this->capabilities; }
	public function isRevoked(): bool { return $this->revoked; }
	public function isExpired(): bool { return null !== $this->expiresAt && time() > $this->expiresAt; }
}
