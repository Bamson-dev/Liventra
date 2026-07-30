<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Secret
 * Domain Entity representing Encrypted API Keys & Secrets at Rest (PRD-013 Part 2 & 6)
 */
class Secret {

	private $key;
	private $encryptedValue;
	private $version;
	private $updatedAt;

	public function __construct( string $key, string $encryptedValue, int $version = 1, ?\DateTimeImmutable $updatedAt = null ) {
		$this->key            = $key;
		$this->encryptedValue = $encryptedValue;
		$this->version        = max( 1, $version );
		$this->updatedAt      = null !== $updatedAt ? $updatedAt : new \DateTimeImmutable();
	}

	public function getKey(): string { return $this->key; }
	public function getEncryptedValue(): string { return $this->encryptedValue; }
	public function getVersion(): int { return $this->version; }
	public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}
