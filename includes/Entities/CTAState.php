<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class CTAState
 * Domain Entity representing CTA Lifecycle State (PRD-009 Part 2 & 7)
 */
class CTAState {

	const STATE_DRAFT     = 'draft';
	const STATE_PUBLISHED = 'published';
	const STATE_VISIBLE   = 'visible';
	const STATE_CLICKED   = 'clicked';
	const STATE_CONVERTED = 'converted';
	const STATE_HIDDEN    = 'hidden';
	const STATE_ARCHIVED  = 'archived';

	private $ctaUuid;
	private $attendeeId;
	private $status;
	private $updatedAt;

	public function __construct(
		string $ctaUuid,
		int $attendeeId,
		string $status = self::STATE_VISIBLE,
		?\DateTimeImmutable $updatedAt = null
	) {
		$this->ctaUuid    = $ctaUuid;
		$this->attendeeId = $attendeeId;
		$this->status     = $status;
		$this->updatedAt  = null !== $updatedAt ? $updatedAt : new \DateTimeImmutable();
	}

	public function getCtaUuid(): string { return $this->ctaUuid; }
	public function getAttendeeId(): int { return $this->attendeeId; }
	public function getStatus(): string { return $this->status; }
	public function isVisible(): bool { return self::STATE_VISIBLE === $this->status; }
}
