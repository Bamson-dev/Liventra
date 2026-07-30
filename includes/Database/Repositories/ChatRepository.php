<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\ChatRepositoryInterface;
use Liventra\Entities\ChatMessage;
use Liventra\Entities\ChatReaction;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ChatRepository
 * Persistence implementation for Live Chat Engine (PRD-003 & PRD-010)
 */
class ChatRepository implements ChatRepositoryInterface {

	private $inMemoryMessages  = array();
	private $inMemoryReactions = array();
	private $pinnedMessageUuid = null;

	public function findByUuid( string $uuid ): ?ChatMessage {
		return isset( $this->inMemoryMessages[ $uuid ] ) ? $this->inMemoryMessages[ $uuid ] : null;
	}

	public function getMessagesForWebinar( int $webinarId ): array {
		$msgs = array();
		foreach ( $this->inMemoryMessages as $msg ) {
			if ( $msg->webinarId() === $webinarId && $msg->isVisible() ) {
				$msgs[] = $msg;
			}
		}
		return $msgs;
	}

	public function save( array $data ): ChatMessage {
		$uuid = $data['uuid'] ?? wp_generate_uuid4();

		$msg = new ChatMessage(
			$uuid,
			(int) ( $data['webinar_id'] ?? 1 ),
			(string) ( $data['sender'] ?? 'Anonymous' ),
			(string) ( $data['message'] ?? '' ),
			(int) ( $data['trigger_second'] ?? 0 ),
			(string) ( $data['type'] ?? 'attendee' ),
			(string) ( $data['role'] ?? 'attendee' ),
			$data['avatar'] ?? null,
			(bool) ( $data['pinned'] ?? false ),
			$data['reactions'] ?? array(),
			(bool) ( $data['personalization'] ?? true ),
			(bool) ( $data['visibility'] ?? true )
		);

		if ( $msg->isPinned() ) {
			$this->pinnedMessageUuid = $uuid;
		}

		$this->inMemoryMessages[ $uuid ] = $msg;
		return $msg;
	}

	public function saveReaction( ChatReaction $reaction ): bool {
		$this->inMemoryReactions[] = $reaction;
		return true;
	}

	public function getPinnedMessage( int $webinarId ): ?ChatMessage {
		if ( $this->pinnedMessageUuid && isset( $this->inMemoryMessages[ $this->pinnedMessageUuid ] ) ) {
			return $this->inMemoryMessages[ $this->pinnedMessageUuid ];
		}
		return null;
	}
}
