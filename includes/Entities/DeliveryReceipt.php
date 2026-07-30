<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class DeliveryReceipt {

	private $receiptId;
	private $notificationId;
	private $channel;
	private $provider;
	private $status;
	private $timestamp;

	public function __construct(
		string $receiptId,
		string $notificationId,
		string $channel,
		string $provider = 'smtp',
		string $status = 'delivered',
		?int $timestamp = null
	) {
		$this->receiptId      = $receiptId;
		$this->notificationId = $notificationId;
		$this->channel        = $channel;
		$this->provider       = $provider;
		$this->status         = $status;
		$this->timestamp      = null !== $timestamp ? $timestamp : time();
	}

	public function receiptId(): string { return $this->receiptId; }
	public function notificationId(): string { return $this->notificationId; }
	public function channel(): string { return $this->channel; }
	public function provider(): string { return $this->provider; }
	public function status(): string { return $this->status; }
	public function timestamp(): int { return $this->timestamp; }
}
