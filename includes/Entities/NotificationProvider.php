<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class NotificationProvider {

	private $providerId;
	private $name;
	private $channel;
	private $priority;
	private $active;

	public function __construct( string $providerId, string $name, string $channel, int $priority = 10, bool $active = true ) {
		$this->providerId = $providerId;
		$this->name       = $name;
		$this->channel    = $channel;
		$this->priority   = $priority;
		$this->active     = $active;
	}

	public function providerId(): string { return $this->providerId; }
	public function name(): string { return $this->name; }
	public function channel(): string { return $this->channel; }
	public function priority(): int { return $this->priority; }
	public function isActive(): bool { return $this->active; }
}
