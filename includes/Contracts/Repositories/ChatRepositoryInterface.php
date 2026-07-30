<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\ChatMessage;
use Liventra\Entities\ChatReaction;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface ChatRepositoryInterface
 * Persistence contract for Chat Engine (PRD-010 Part 3)
 */
interface ChatRepositoryInterface {

	/**
	 * Find message by UUID
	 *
	 * @param string $uuid Message UUID.
	 * @return ChatMessage|null
	 */
	public function findByUuid( string $uuid ): ?ChatMessage;

	/**
	 * Get all messages for a webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @return array Array of ChatMessage entities.
	 */
	public function getMessagesForWebinar( int $webinarId ): array;

	/**
	 * Save chat message
	 *
	 * @param array $data Message data.
	 * @return ChatMessage
	 */
	public function save( array $data ): ChatMessage;

	/**
	 * Save reaction
	 *
	 * @param ChatReaction $reaction Reaction entity.
	 * @return bool
	 */
	public function saveReaction( ChatReaction $reaction ): bool;

	/**
	 * Get pinned message for a webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @return ChatMessage|null
	 */
	public function getPinnedMessage( int $webinarId ): ?ChatMessage;
}
