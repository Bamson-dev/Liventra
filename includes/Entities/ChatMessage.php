<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ChatMessage
 * Domain Entity representing a Live Chat Message (PRD-010 Part 2 & 5)
 */
class ChatMessage {

	// Supported message types (PRD-010 Part 5)
	const TYPE_ATTENDEE     = 'attendee';
	const TYPE_MODERATOR    = 'moderator';
	const TYPE_SYSTEM       = 'system';
	const TYPE_ANNOUNCEMENT = 'announcement';
	const TYPE_QUESTION     = 'question';
	const TYPE_ANSWER       = 'answer';

	private $uuid;
	private $webinarId;
	private $sender;
	private $avatar;
	private $role; // 'attendee' | 'host' | 'moderator' | 'system'
	private $message;
	private $triggerSecond;
	private $type;
	private $pinned;
	private $reactions;
	private $personalization;
	private $visibility; // true if enabled

	public function __construct(
		string $uuid,
		int $webinarId,
		string $sender,
		string $message,
		int $triggerSecond = 0,
		string $type = self::TYPE_ATTENDEE,
		string $role = 'attendee',
		?string $avatar = null,
		bool $pinned = false,
		array $reactions = array(),
		bool $personalization = true,
		bool $visibility = true
	) {
		$this->uuid            = $uuid;
		$this->webinarId       = $webinarId;
		$this->sender          = $sender;
		// Sanitize message content against XSS (PRD-010 Part 13 Security)
		$this->message         = function_exists( 'esc_html' ) ? esc_html( $message ) : htmlspecialchars( $message, ENT_QUOTES, 'UTF-8' );
		$this->triggerSecond   = max( 0, $triggerSecond );
		$this->type            = strtolower( $type );
		$this->role            = strtolower( $role );
		$this->avatar          = $avatar;
		$this->pinned          = $pinned;
		$this->reactions       = $reactions;
		$this->personalization = $personalization;
		$this->visibility      = $visibility;
	}

	public function uuid(): string { return $this->uuid; }
	public function webinarId(): int { return $this->webinarId; }
	public function sender(): string { return $this->sender; }
	public function message(): string { return $this->message; }
	public function triggerSecond(): int { return $this->triggerSecond; }
	public function type(): string { return $this->type; }
	public function role(): string { return $this->role; }
	public function avatar(): ?string { return $this->avatar; }
	public function isPinned(): bool { return $this->pinned; }
	public function reactions(): array { return $this->reactions; }
	public function hasPersonalization(): bool { return $this->personalization; }
	public function isVisible(): bool { return $this->visibility; }

	public function personalize( array $context ): self {
		$firstName = isset( $context['first_name'] ) ? (string) $context['first_name'] : 'Friend';
		$country   = isset( $context['country'] ) ? (string) $context['country'] : 'your area';

		$personalizedMsg = str_replace(
			array( '{first_name}', '{name}', '{country}' ),
			array( $firstName, $firstName, $country ),
			$this->message
		);

		return new self(
			$this->uuid,
			$this->webinarId,
			$this->sender,
			$personalizedMsg,
			$this->triggerSecond,
			$this->type,
			$this->role,
			$this->avatar,
			$this->pinned,
			$this->reactions,
			$this->personalization,
			$this->visibility
		);
	}

	public function toArray(): array {
		return array(
			'uuid'            => $this->uuid,
			'webinar_id'      => $this->webinarId,
			'sender'          => $this->sender,
			'message'         => $this->message,
			'trigger_second'  => $this->triggerSecond,
			'type'            => $this->type,
			'role'            => $this->role,
			'avatar'          => $this->avatar,
			'pinned'          => $this->pinned,
			'reactions'       => $this->reactions,
			'personalization' => $this->personalization,
			'visibility'      => $this->visibility,
		);
	}
}
