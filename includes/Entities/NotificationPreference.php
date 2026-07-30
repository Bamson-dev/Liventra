<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class NotificationPreference {

	private $userId;
	private $emailEnabled;
	private $smsEnabled;
	private $whatsappEnabled;
	private $pushEnabled;

	public function __construct(
		int $userId,
		bool $emailEnabled = true,
		bool $smsEnabled = true,
		bool $whatsappEnabled = true,
		bool $pushEnabled = true
	) {
		$this->userId          = $userId;
		$this->emailEnabled    = $emailEnabled;
		$this->smsEnabled      = $smsEnabled;
		$this->whatsappEnabled = $whatsappEnabled;
		$this->pushEnabled     = $pushEnabled;
	}

	public function userId(): int { return $this->userId; }
	public function emailEnabled(): bool { return $this->emailEnabled; }
	public function smsEnabled(): bool { return $this->smsEnabled; }
	public function whatsappEnabled(): bool { return $this->whatsappEnabled; }
	public function pushEnabled(): bool { return $this->pushEnabled; }
}
