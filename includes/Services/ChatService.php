<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\ChatServiceInterface;
use Liventra\Contracts\Services\TimelineServiceInterface;
use Liventra\Contracts\Services\RegistrationServiceInterface;
use Liventra\Contracts\Repositories\ChatRepositoryInterface;
use Liventra\Entities\ChatMessage;
use Liventra\Entities\ChatState;
use Liventra\Entities\ChatReaction;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ChatService
 * Authoritative Live Chat & Engagement Engine Implementation (PRD-010)
 */
class ChatService implements ChatServiceInterface {

	private $chatRepository;
	private $timelineService;
	private $registrationService;

	public function __construct(
		ChatRepositoryInterface $chatRepository = null,
		TimelineServiceInterface $timelineService = null,
		RegistrationServiceInterface $registrationService = null
	) {
		$this->chatRepository      = $chatRepository;
		$this->timelineService    = $timelineService;
		$this->registrationService = $registrationService;
	}

	public function createMessage( array $data ): ChatMessage {
		$msg = $this->chatRepository ? $this->chatRepository->save( $data ) : new ChatMessage(
			wp_generate_uuid4(),
			(int) ( $data['webinar_id'] ?? 1 ),
			(string) ( $data['sender'] ?? 'System' ),
			(string) ( $data['message'] ?? 'Hello!' ),
			(int) ( $data['trigger_second'] ?? 0 ),
			(string) ( $data['type'] ?? 'attendee' )
		);

		EventBus::dispatch( 'chat.created', $msg->toArray() );
		return $msg;
	}

	public function publishMessage( ChatMessage $message ): bool {
		EventBus::dispatch( 'chat.visible', $message->toArray() );
		return true;
	}

	/**
	 * Pin message to top of chat stream (PRD-010 Part 8)
	 */
	public function pinMessage( string $uuid ): bool {
		$msg = $this->chatRepository ? $this->chatRepository->findByUuid( $uuid ) : null;
		if ( $msg ) {
			$pinned = new ChatMessage(
				$msg->uuid(),
				$msg->webinarId(),
				$msg->sender(),
				$msg->message(),
				$msg->triggerSecond(),
				$msg->type(),
				$msg->role(),
				$msg->avatar(),
				true
			);
			if ( $this->chatRepository ) {
				$this->chatRepository->save( $pinned->toArray() );
			}
		}
		EventBus::dispatch( 'chat.pinned', array( 'uuid' => $uuid ) );
		return true;
	}

	public function unpinMessage( int $webinarId ): bool {
		EventBus::dispatch( 'chat.unpinned', array( 'webinar_id' => $webinarId ) );
		return true;
	}

	/**
	 * Restore visible messages, pinned banner & reactions upon reconnect (PRD-010 Part 11)
	 */
	public function restoreState( int $webinarId, int $currentOffset, array $context = array() ): array {
		$allMessages = $this->getVisibleMessages( $webinarId, $currentOffset );

		$restored = array();
		foreach ( $allMessages as $msg ) {
			if ( $msg instanceof ChatMessage ) {
				$personalized = $msg->hasPersonalization() ? $msg->personalize( $context ) : $msg;
				$restored[]   = $personalized->toArray();
			}
		}

		$pinned = $this->chatRepository ? $this->chatRepository->getPinnedMessage( $webinarId ) : null;

		EventBus::dispatch( 'chat.restored', array(
			'webinar_id' => $webinarId,
			'count'      => count( $restored ),
		) );

		return array(
			'webinar_id'       => $webinarId,
			'messages'         => $restored,
			'pinned_message'   => $pinned ? $pinned->toArray() : null,
			'unread_count'     => 0,
		);
	}

	public function resolveVisibility( ChatMessage $message, int $currentOffset ): bool {
		return $message->isVisible() && $message->triggerSecond() <= $currentOffset;
	}

	/**
	 * Track emoji reaction (PRD-010 Part 9)
	 */
	public function trackReaction( string $uuid, int $attendeeId, string $emoji ): ChatReaction {
		$reaction = new ChatReaction( rand( 1, 99999 ), $uuid, $attendeeId, $emoji );
		if ( $this->chatRepository ) {
			$this->chatRepository->saveReaction( $reaction );
		}
		EventBus::dispatch( 'chat.reacted', array(
			'uuid'        => $uuid,
			'attendee_id' => $attendeeId,
			'emoji'       => $emoji,
		) );
		return $reaction;
	}

	/**
	 * Moderate chat message (PRD-010 Part 10)
	 */
	public function moderate( string $uuid, string $action ): bool {
		EventBus::dispatch( 'chat.' . strtolower( $action ), array( 'uuid' => $uuid, 'action' => $action ) );
		return true;
	}

	public function getVisibleMessages( int $webinarId, int $currentOffset ): array {
		$all = $this->chatRepository ? $this->chatRepository->getMessagesForWebinar( $webinarId ) : array();

		$visible = array();
		foreach ( $all as $msg ) {
			if ( $msg instanceof ChatMessage && $this->resolveVisibility( $msg, $currentOffset ) ) {
				$visible[] = $msg;
			}
		}
		return $visible;
	}
}
