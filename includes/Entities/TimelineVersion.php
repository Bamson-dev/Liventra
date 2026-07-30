<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class TimelineVersion
 * Domain Entity representing an Immutable Timeline Version (PRD-006 Part 6)
 */
class TimelineVersion {

	private $versionId;
	private $webinarId;
	private $versionNumber;
	private $status; // 'draft' | 'published' | 'archived'
	private $createdAt;

	public function __construct(
		int $versionId,
		int $webinarId,
		int $versionNumber,
		string $status = 'published',
		?\DateTimeImmutable $createdAt = null
	) {
		$this->versionId     = $versionId;
		$this->webinarId     = $webinarId;
		$this->versionNumber = $versionNumber;
		$this->status        = $status;
		$this->createdAt     = null !== $createdAt ? $createdAt : new \DateTimeImmutable();
	}

	public function getVersionId(): int { return $this->versionId; }
	public function getWebinarId(): int { return $this->webinarId; }
	public function getVersionNumber(): int { return $this->versionNumber; }
	public function getStatus(): string { return $this->status; }

	public function isPublished(): bool { return 'published' === $this->status; }
	public function isArchived(): bool { return 'archived' === $this->status; }
}
