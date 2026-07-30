<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class UsageQuota {

	private $orgId;
	private $maxWebinars;
	private $maxAttendees;
	private $maxStorageGb;
	private $currentWebinars;

	public function __construct(
		string $orgId,
		int $maxWebinars = 100,
		int $maxAttendees = 10000,
		int $maxStorageGb = 500,
		int $currentWebinars = 5
	) {
		$this->orgId           = $orgId;
		$this->maxWebinars     = $maxWebinars;
		$this->maxAttendees    = $maxAttendees;
		$this->maxStorageGb    = $maxStorageGb;
		$this->currentWebinars = $currentWebinars;
	}

	public function orgId(): string { return $this->orgId; }
	public function maxWebinars(): int { return $this->maxWebinars; }
	public function maxAttendees(): int { return $this->maxAttendees; }
	public function maxStorageGb(): int { return $this->maxStorageGb; }
	public function currentWebinars(): int { return $this->currentWebinars; }
	public function isExceeded(): bool { return $this->currentWebinars >= $this->maxWebinars; }
}
