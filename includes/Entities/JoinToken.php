<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class JoinToken
 * Cryptographically Signed Join Token Domain Entity (PRD-008 Part 2 & 6)
 */
class JoinToken {

	private $uuid;
	private $attendeeId;
	private $webinarId;
	private $issuedAt;
	private $expiresAt;
	private $signature;
	private $scope;
	private $tokenString;

	public function __construct(
		string $uuid,
		int $attendeeId,
		int $webinarId,
		\DateTimeImmutable $issuedAt,
		\DateTimeImmutable $expiresAt,
		string $signature,
		string $scope = 'attendee',
		?string $tokenString = null
	) {
		$this->uuid        = $uuid;
		$this->attendeeId  = $attendeeId;
		$this->webinarId   = $webinarId;
		$this->issuedAt    = $issuedAt;
		$this->expiresAt   = $expiresAt;
		$this->signature   = $signature;
		$this->scope       = $scope;
		$this->tokenString = null !== $tokenString ? $tokenString : $this->buildTokenString();
	}

	public function getUuid(): string { return $this->uuid; }
	public function getAttendeeId(): int { return $this->attendeeId; }
	public function getWebinarId(): int { return $this->webinarId; }
	public function getIssuedAt(): \DateTimeImmutable { return $this->issuedAt; }
	public function getExpiresAt(): \DateTimeImmutable { return $this->expiresAt; }
	public function getSignature(): string { return $this->signature; }
	public function getScope(): string { return $this->scope; }
	public function getTokenString(): string { return $this->tokenString; }

	public function isExpired( ?\DateTimeImmutable $now = null ): bool {
		$check = null !== $now ? $now : new \DateTimeImmutable();
		return $check > $this->expiresAt;
	}

	public function verifySignature( string $secretKey ): bool {
		$expected = hash_hmac( 'sha256', $this->uuid . ':' . $this->attendeeId . ':' . $this->webinarId . ':' . $this->expiresAt->getTimestamp(), $secretKey );
		return hash_equals( $expected, $this->signature );
	}

	private function buildTokenString(): string {
		return $this->uuid . '.' . base64_encode( $this->attendeeId . ':' . $this->webinarId . ':' . $this->signature );
	}
}
