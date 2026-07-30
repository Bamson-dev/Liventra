<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\CTA;
use Liventra\Entities\CTAState;
use Liventra\Entities\CTAInteraction;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface CTAServiceInterface
 * Public Contract for Conversion & Offer Engine (PRD-009 Part 1)
 */
interface CTAServiceInterface {

	/**
	 * Create a new CTA definition
	 *
	 * @param array $data CTA details.
	 * @return CTA
	 */
	public function createCTA( array $data ): CTA;

	/**
	 * Update existing CTA definition
	 *
	 * @param string $uuid CTA UUID.
	 * @param array  $data Updated details.
	 * @return CTA
	 */
	public function updateCTA( string $uuid, array $data ): CTA;

	/**
	 * Archive a CTA definition
	 *
	 * @param string $uuid CTA UUID.
	 * @return bool
	 */
	public function archiveCTA( string $uuid ): bool;

	/**
	 * Evaluate CTA eligibility for attendee and session offset (PRD-009 Part 6)
	 *
	 * @param CTA   $cta CTA entity.
	 * @param int   $currentOffset Playback timestamp.
	 * @param array $context Attendee context (first_name, country, device).
	 * @return bool
	 */
	public function resolveEligibility( CTA $cta, int $currentOffset, array $context = array() ): bool;

	/**
	 * Show CTA and trigger impression event
	 *
	 * @param string $uuid CTA UUID.
	 * @param int    $attendeeId Attendee ID.
	 * @return CTAState
	 */
	public function showCTA( string $uuid, int $attendeeId ): CTAState;

	/**
	 * Hide active CTA
	 *
	 * @param string $uuid CTA UUID.
	 * @param int    $attendeeId Attendee ID.
	 * @return bool
	 */
	public function hideCTA( string $uuid, int $attendeeId ): bool;

	/**
	 * Restore persistent CTA states upon reconnect (PRD-009 Part 7)
	 *
	 * @param int   $webinarId Webinar ID.
	 * @param int   $currentOffset Current offset.
	 * @param array $context Attendee context.
	 * @return array Array of active CTA entities/arrays.
	 */
	public function restoreState( int $webinarId, int $currentOffset, array $context = array() ): array;

	/**
	 * Track attendee interaction (impression, hover, click, dismiss, timeout) (PRD-009 Part 10)
	 *
	 * @param string $uuid CTA UUID.
	 * @param int    $attendeeId Attendee ID.
	 * @param string $type Interaction type.
	 * @return CTAInteraction
	 */
	public function trackInteraction( string $uuid, int $attendeeId, string $type ): CTAInteraction;

	/**
	 * Track purchase conversion event (PRD-009 Part 11)
	 *
	 * @param string $uuid CTA UUID.
	 * @param int    $attendeeId Attendee ID.
	 * @param array  $conversionData Transaction payload.
	 * @return CTAInteraction
	 */
	public function trackConversion( string $uuid, int $attendeeId, array $conversionData = array() ): CTAInteraction;

	/**
	 * Get all active CTAs for a webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @return array
	 */
	public function getActiveCTAs( int $webinarId ): array;
}
