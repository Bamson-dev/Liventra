<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class NotificationJob {

	private $jobId;
	private $notification;
	private $attempts;
	private $maxRetries;
	private $status;

	public function __construct(
		string $jobId,
		Notification $notification,
		int $attempts = 0,
		int $maxRetries = 3,
		string $status = 'pending'
	) {
		$this->jobId        = $jobId;
		$this->notification = $notification;
		$this->attempts     = $attempts;
		$this->maxRetries   = $maxRetries;
		$this->status       = $status;
	}

	public function jobId(): string { return $this->jobId; }
	public function notification(): Notification { return $this->notification; }
	public function attempts(): int { return $this->attempts; }
	public function maxRetries(): int { return $this->maxRetries; }
	public function status(): string { return $this->status; }
}
