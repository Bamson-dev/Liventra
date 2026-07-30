<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SecurityToken
 * Domain Entity representing a Cryptographically Signed Token (PRD-013 Part 2)
 */
class SecurityToken {

	private $uuid;
	private $subjectId;
	private $payload;
	private $signature;
	private $expiresAt;

	public function __construct(
		string $uuid,
		string $subjectId,
		array $payload,
		string $signature,
		int $expiresAt
	) {
		$this->uuid      = $uuid;
		$this->subjectId = $subjectId;
		$this->payload   = $payload;
		$this->signature = $signature;
		$this->expiresAt = $expiresAt;
	}

	public function uuid(): string { return $this->uuid; }
	public function subjectId(): string { return $this->subjectId; }
	public function payload(): array { return $this->payload; }
	public function signature(): string { return $this->signature; }
	public function expiresAt(): int { return $this->expiresAt; }
	public function isExpired(): bool { return time() > $this->expiresAt; }

	public function toString(): string {
		$body = base64_encode( (string) wp_json_encode( array(
			'uuid'       => $this->uuid,
			'sub'        => $this->subjectId,
			'exp'        => $this->expiresAt,
			'payload'    => $this->payload,
		) ) );
		return $body . '.' . $this->signature;
	}
}
