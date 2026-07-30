<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ChatReaction
 * Domain Entity representing Emoji Reactions (PRD-010 Part 2 & 9)
 */
class ChatReaction {

	private $reactionId;
	private $chatUuid;
	private $attendeeId;
	private $emoji;
	private $timestamp;

	public function __construct(
		int $reactionId,
		string $chatUuid,
		int $attendeeId,
		string $emoji = '👍',
		?\DateTimeImmutable $timestamp = null
	) {
		$this->reactionId = $reactionId;
		$this->chatUuid   = $chatUuid;
		$this->attendeeId = $attendeeId;
		$this->emoji      = $emoji;
		$this->timestamp  = null !== $timestamp ? $timestamp : new \DateTimeImmutable();
	}

	public function getReactionId(): int { return $this->reactionId; }
	public function getChatUuid(): string { return $this->chatUuid; }
	public function getAttendeeId(): int { return $this->attendeeId; }
	public function getEmoji(): string { return $this->emoji; }
	public function getTimestamp(): \DateTimeImmutable { return $this->timestamp; }
}
