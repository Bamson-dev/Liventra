<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Notification
 * Domain Entity representing a Notification Payload (PRD-015 Part 2)
 */
class Notification {

	private $notificationId;
	private $recipientId;
	private $channel; // 'email' | 'sms' | 'whatsapp' | 'push' | 'in_app' | 'slack' | 'discord'
	private $subject;
	private $body;
	private $status;
	private $scheduledAt;

	public function __construct(
		string $notificationId,
		int $recipientId,
		string $channel = 'email',
		string $subject = '',
		string $body = '',
		string $status = 'queued',
		?int $scheduledAt = null
	) {
		$this->notificationId = $notificationId;
		$this->recipientId    = $recipientId;
		$this->channel        = strtolower( $channel );
		$this->subject        = $subject;
		$this->body           = $body;
		$this->status         = strtolower( $status );
		$this->scheduledAt    = $scheduledAt;
	}

	public function notificationId(): string { return $this->notificationId; }
	public function recipientId(): int { return $this->recipientId; }
	public function channel(): string { return $this->channel; }
	public function subject(): string { return $this->subject; }
	public function body(): string { return $this->body; }
	public function status(): string { return $this->status; }
	public function scheduledAt(): ?int { return $this->scheduledAt; }
}
