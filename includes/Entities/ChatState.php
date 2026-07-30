<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ChatState
 * Domain Entity representing Chat Lifecycle State (PRD-010 Part 2 & 7)
 */
class ChatState {

	const STATE_DRAFT     = 'draft';
	const STATE_SCHEDULED = 'scheduled';
	const STATE_VISIBLE   = 'visible';
	const STATE_PINNED    = 'pinned';
	const STATE_HIDDEN    = 'hidden';
	const STATE_ARCHIVED  = 'archived';

	private $messageUuid;
	private $status;

	public function __construct( string $messageUuid, string $status = self::STATE_VISIBLE ) {
		$this->messageUuid = $messageUuid;
		$this->status      = $status;
	}

	public function getMessageUuid(): string { return $this->messageUuid; }
	public function getStatus(): string { return $this->status; }
	public function isVisible(): bool { return self::STATE_VISIBLE === $this->status || self::STATE_PINNED === $this->status; }
}
