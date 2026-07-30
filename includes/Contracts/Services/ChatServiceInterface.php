<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\ChatMessage;
use Liventra\Entities\ChatState;
use Liventra\Entities\ChatReaction;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface ChatServiceInterface
 * Public Contract for Live Chat & Realtime Engagement Engine (PRD-010 Part 1)
 */
interface ChatServiceInterface {

	/**
	 * Create a new chat message definition
	 *
	 * @param array $data Message details.
	 * @return ChatMessage
	 */
	public function createMessage( array $data ): ChatMessage;

	/**
	 * Publish a chat message to webinar stream
	 *
	 * @param ChatMessage $message Message entity.
	 * @return bool
	 */
	public function publishMessage( ChatMessage $message ): bool;

	/**
	 * Pin a message to the top of chat stream (PRD-010 Part 8)
	 *
	 * @param string $uuid Message UUID.
	 * @return bool
	 */
	public function pinMessage( string $uuid ): bool;

	/**
	 * Unpin current pinned message
	 *
	 * @param int $webinarId Webinar ID.
	 * @return bool
	 */
	public function unpinMessage( int $webinarId ): bool;

	/**
	 * Restore visible chat messages, pinned banner & reactions upon reconnect (PRD-010 Part 11)
	 *
	 * @param int   $webinarId Webinar ID.
	 * @param int   $currentOffset Current offset.
	 * @param array $context Attendee context.
	 * @return array Restored chat payload.
	 */
	public function restoreState( int $webinarId, int $currentOffset, array $context = array() ): array;

	/**
	 * Resolve message visibility for playback offset
	 *
	 * @param ChatMessage $message Message entity.
	 * @param int         $currentOffset Current offset.
	 * @return bool
	 */
	public function resolveVisibility( ChatMessage $message, int $currentOffset ): bool;

	/**
	 * Track emoji reaction on a message (PRD-010 Part 9)
	 *
	 * @param string $uuid Message UUID.
	 * @param int    $attendeeId Attendee ID.
	 * @param string $emoji Emoji string (👍, ❤️, 🔥, 😂, 👏, 😮).
	 * @return ChatReaction
	 */
	public function trackReaction( string $uuid, int $attendeeId, string $emoji ): ChatReaction;

	/**
	 * Moderate chat message (hide, delete, mute, block) (PRD-010 Part 10)
	 *
	 * @param string $uuid Message UUID.
	 * @param string $action Moderation action ('hide', 'delete', 'mute', 'block').
	 * @return bool
	 */
	public function moderate( string $uuid, string $action ): bool;

	/**
	 * Get all visible messages up to current offset
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $currentOffset Current offset.
	 * @return array
	 */
	public function getVisibleMessages( int $webinarId, int $currentOffset ): array;
}
