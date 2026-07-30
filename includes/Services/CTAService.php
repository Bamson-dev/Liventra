<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\CTAServiceInterface;
use Liventra\Contracts\Services\TimelineServiceInterface;
use Liventra\Contracts\Services\RegistrationServiceInterface;
use Liventra\Contracts\Repositories\CTARepositoryInterface;
use Liventra\Entities\CTA;
use Liventra\Entities\CTAState;
use Liventra\Entities\CTAInteraction;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class CTAService
 * Authoritative Conversion & Offer Engine Implementation (PRD-009)
 */
class CTAService implements CTAServiceInterface {

	private $ctaRepository;
	private $timelineService;
	private $registrationService;

	public function __construct(
		CTARepositoryInterface $ctaRepository = null,
		TimelineServiceInterface $timelineService = null,
		RegistrationServiceInterface $registrationService = null
	) {
		$this->ctaRepository       = $ctaRepository;
		$this->timelineService     = $timelineService;
		$this->registrationService = $registrationService;
	}

	public function createCTA( array $data ): CTA {
		$cta = $this->ctaRepository ? $this->ctaRepository->save( $data ) : new CTA(
			wp_generate_uuid4(),
			(int) ( $data['webinar_id'] ?? 1 ),
			(string) ( $data['title'] ?? 'Special Offer' ),
			(string) ( $data['button_text'] ?? 'Buy Now' ),
			(string) ( $data['destination_url'] ?? 'https://example.com' )
		);

		EventBus::dispatch( 'cta.created', $cta->toArray() );
		return $cta;
	}

	public function updateCTA( string $uuid, array $data ): CTA {
		$data['uuid'] = $uuid;
		$cta          = $this->ctaRepository ? $this->ctaRepository->save( $data ) : new CTA(
			$uuid,
			(int) ( $data['webinar_id'] ?? 1 ),
			(string) ( $data['title'] ?? 'Updated Offer' ),
			(string) ( $data['button_text'] ?? 'Enroll' ),
			(string) ( $data['destination_url'] ?? 'https://example.com' )
		);

		EventBus::dispatch( 'cta.updated', $cta->toArray() );
		return $cta;
	}

	public function archiveCTA( string $uuid ): bool {
		EventBus::dispatch( 'cta.archived', array( 'uuid' => $uuid ) );
		return true;
	}

	/**
	 * Evaluate CTA eligibility server-side (PRD-009 Part 6 & 12 Security)
	 */
	public function resolveEligibility( CTA $cta, int $currentOffset, array $context = array() ): bool {
		if ( ! $cta->isEnabled() ) return false;

		// 1. Check Playback Offset Condition
		if ( $currentOffset < $cta->triggerSecond() ) {
			return false; // Not eligible yet
		}

		// 2. Check Country Condition if specified
		if ( isset( $context['country'] ) && isset( $context['required_country'] ) ) {
			if ( strtolower( $context['country'] ) !== strtolower( $context['required_country'] ) ) {
				return false;
			}
		}

		return true;
	}

	public function showCTA( string $uuid, int $attendeeId ): CTAState {
		$state = new CTAState( $uuid, $attendeeId, CTAState::STATE_VISIBLE );
		EventBus::dispatch( 'cta.visible', array( 'uuid' => $uuid, 'attendee_id' => $attendeeId ) );
		$this->trackInteraction( $uuid, $attendeeId, CTAInteraction::TYPE_IMPRESSION );
		return $state;
	}

	public function hideCTA( string $uuid, int $attendeeId ): bool {
		EventBus::dispatch( 'cta.hidden', array( 'uuid' => $uuid, 'attendee_id' => $attendeeId ) );
		return true;
	}

	/**
	 * Restore persistent CTA states upon reconnect (PRD-009 Part 7)
	 */
	public function restoreState( int $webinarId, int $currentOffset, array $context = array() ): array {
		$all = $this->ctaRepository ? $this->ctaRepository->getCTAsForWebinar( $webinarId ) : array();

		$active = array();
		foreach ( $all as $cta ) {
			if ( $cta instanceof CTA && $cta->isPersistent() ) {
				if ( $this->resolveEligibility( $cta, $currentOffset, $context ) ) {
					$personalized = $cta->hasPersonalization() ? $cta->personalize( $context ) : $cta;
					$active[]     = $personalized->toArray();
				}
			}
		}

		EventBus::dispatch( 'cta.restored', array( 'webinar_id' => $webinarId, 'count' => count( $active ) ) );
		return $active;
	}

	/**
	 * Track attendee interaction (PRD-009 Part 10)
	 */
	public function trackInteraction( string $uuid, int $attendeeId, string $type ): CTAInteraction {
		$interaction = new CTAInteraction( rand( 1, 99999 ), $uuid, $attendeeId, $type );

		if ( $this->ctaRepository ) {
			$this->ctaRepository->recordInteraction( $interaction );
		}

		EventBus::dispatch( 'cta.' . strtolower( $type ), array(
			'uuid'        => $uuid,
			'attendee_id' => $attendeeId,
			'type'        => $type,
		) );

		return $interaction;
	}

	/**
	 * Track purchase conversion event (PRD-009 Part 11 Attribution)
	 */
	public function trackConversion( string $uuid, int $attendeeId, array $conversionData = array() ): CTAInteraction {
		$interaction = new CTAInteraction( rand( 1, 99999 ), $uuid, $attendeeId, CTAInteraction::TYPE_CONVERSION, $conversionData );

		if ( $this->ctaRepository ) {
			$this->ctaRepository->recordInteraction( $interaction );
		}

		EventBus::dispatch( 'cta.converted', array_merge( array(
			'uuid'        => $uuid,
			'attendee_id' => $attendeeId,
		), $conversionData ) );

		return $interaction;
	}

	public function getActiveCTAs( int $webinarId ): array {
		return $this->ctaRepository ? $this->ctaRepository->getCTAsForWebinar( $webinarId ) : array();
	}
}
