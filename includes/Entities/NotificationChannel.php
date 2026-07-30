<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class NotificationChannel {

	private $channelId;
	private $name;
	private $enabled;

	public function __construct( string $channelId, string $name, bool $enabled = true ) {
		$this->channelId = $channelId;
		$this->name      = $name;
		$this->enabled   = $enabled;
	}

	public function channelId(): string { return $this->channelId; }
	public function name(): string { return $this->name; }
	public function isEnabled(): bool { return $this->enabled; }
}
