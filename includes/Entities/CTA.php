<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class CTA
 * Strongly Typed Domain Entity representing a Call-to-Action Offer (PRD-009 Part 2)
 */
class CTA {

	private $uuid;
	private $webinarId;
	private $title;
	private $description;
	private $buttonText;
	private $destinationUrl;
	private $type; // 'button', 'banner', 'modal', 'sticky_footer', 'sidebar', 'floating_bubble', 'fullscreen_overlay', 'inline_block', 'coupon', 'multi_step'
	private $priority;
	private $visibility;
	private $persistence; // true if survives reconnect
	private $countdownDuration; // in seconds
	private $personalization; // boolean flag
	private $triggerSecond;
	private $enabled;

	public function __construct(
		string $uuid,
		int $webinarId,
		string $title,
		string $buttonText,
		string $destinationUrl,
		string $type = 'button',
		string $description = '',
		int $triggerSecond = 0,
		bool $persistence = true,
		int $countdownDuration = 300,
		bool $personalization = true,
		int $priority = 90,
		bool $enabled = true
	) {
		$this->uuid              = $uuid;
		$this->webinarId         = $webinarId;
		$this->title             = $title;
		$this->description       = $description;
		$this->buttonText        = $buttonText;
		$this->destinationUrl    = $destinationUrl;
		$this->type              = strtolower( $type );
		$this->triggerSecond     = max( 0, $triggerSecond );
		$this->persistence       = $persistence;
		$this->countdownDuration = max( 0, $countdownDuration );
		$this->personalization   = $personalization;
		$this->priority          = $priority;
		$this->enabled            = $enabled;
	}

	public function uuid(): string { return $this->uuid; }
	public function webinarId(): int { return $this->webinarId; }
	public function title(): string { return $this->title; }
	public function description(): string { return $this->description; }
	public function buttonText(): string { return $this->buttonText; }
	public function destinationUrl(): string { return $this->destinationUrl; }
	public function type(): string { return $this->type; }
	public function priority(): int { return $this->priority; }
	public function isPersistent(): bool { return $this->persistence; }
	public function countdownDuration(): int { return $this->countdownDuration; }
	public function hasPersonalization(): bool { return $this->personalization; }
	public function triggerSecond(): int { return $this->triggerSecond; }
	public function isEnabled(): bool { return $this->enabled; }

	public function personalize( array $context ): self {
		$firstName = isset( $context['first_name'] ) ? (string) $context['first_name'] : 'Friend';
		$personalizedTitle = str_replace( array( '{first_name}', '{name}' ), $firstName, $this->title );
		$personalizedDesc  = str_replace( array( '{first_name}', '{name}' ), $firstName, $this->description );

		return new self(
			$this->uuid,
			$this->webinarId,
			$personalizedTitle,
			$this->buttonText,
			$this->destinationUrl,
			$this->type,
			$personalizedDesc,
			$this->triggerSecond,
			$this->persistence,
			$this->countdownDuration,
			$this->personalization,
			$this->priority,
			$this->enabled
		);
	}

	public function toArray(): array {
		return array(
			'uuid'               => $this->uuid,
			'webinar_id'         => $this->webinarId,
			'title'              => $this->title,
			'description'        => $this->description,
			'button_text'        => $this->buttonText,
			'destination_url'    => $this->destinationUrl,
			'type'               => $this->type,
			'trigger_second'     => $this->triggerSecond,
			'persistence'        => $this->persistence,
			'countdown_duration' => $this->countdownDuration,
			'personalization'    => $this->personalization,
			'priority'           => $this->priority,
			'enabled'            => $this->enabled,
		);
	}
}
