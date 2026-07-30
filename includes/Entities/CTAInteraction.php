<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class CTAInteraction
 * Domain Entity representing CTA Attendee Interaction (PRD-009 Part 2 & 10)
 */
class CTAInteraction {

	const TYPE_IMPRESSION = 'impression';
	const TYPE_HOVER      = 'hover';
	const TYPE_CLICK      = 'click';
	const TYPE_DISMISS    = 'dismiss';
	const TYPE_CONVERSION = 'conversion';
	const TYPE_TIMEOUT    = 'timeout';

	private $interactionId;
	private $ctaUuid;
	private $attendeeId;
	private $type;
	private $payload;
	private $timestamp;

	public function __construct(
		int $interactionId,
		string $ctaUuid,
		int $attendeeId,
		string $type = self::TYPE_CLICK,
		array $payload = array(),
		?\DateTimeImmutable $timestamp = null
	) {
		$this->interactionId = $interactionId;
		$this->ctaUuid       = $ctaUuid;
		$this->attendeeId    = $attendeeId;
		$this->type          = $type;
		$this->payload       = $payload;
		$this->timestamp     = null !== $timestamp ? $timestamp : new \DateTimeImmutable();
	}

	public function getInteractionId(): int { return $this->interactionId; }
	public function getCtaUuid(): string { return $this->ctaUuid; }
	public function getAttendeeId(): int { return $this->attendeeId; }
	public function getType(): string { return $this->type; }
	public function getPayload(): array { return $this->payload; }
	public function getTimestamp(): \DateTimeImmutable { return $this->timestamp; }
}
