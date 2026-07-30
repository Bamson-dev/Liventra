<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\CTA;
use Liventra\Entities\CTAInteraction;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface CTARepositoryInterface
 * Persistence contract for CTA Engine (PRD-009 Part 3)
 */
interface CTARepositoryInterface {

	/**
	 * Find CTA by UUID
	 *
	 * @param string $uuid CTA UUID.
	 * @return CTA|null
	 */
	public function findByUuid( string $uuid ): ?CTA;

	/**
	 * Get all CTAs for a webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @return array Array of CTA entities.
	 */
	public function getCTAsForWebinar( int $webinarId ): array;

	/**
	 * Create or update CTA definition
	 *
	 * @param array $data CTA details.
	 * @return CTA
	 */
	public function save( array $data ): CTA;

	/**
	 * Record interaction entry
	 *
	 * @param CTAInteraction $interaction Interaction object.
	 * @return bool
	 */
	public function recordInteraction( CTAInteraction $interaction ): bool;
}
