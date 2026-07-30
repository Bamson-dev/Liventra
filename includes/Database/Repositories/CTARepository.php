<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\CTARepositoryInterface;
use Liventra\Entities\CTA;
use Liventra\Entities\CTAInteraction;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class CTARepository
 * Persistence implementation for CTA Engine (PRD-003 & PRD-009)
 */
class CTARepository implements CTARepositoryInterface {

	private $inMemoryCTAs         = array();
	private $inMemoryInteractions = array();

	public function findByUuid( string $uuid ): ?CTA {
		return isset( $this->inMemoryCTAs[ $uuid ] ) ? $this->inMemoryCTAs[ $uuid ] : null;
	}

	public function getCTAsForWebinar( int $webinarId ): array {
		$ctas = array();
		foreach ( $this->inMemoryCTAs as $cta ) {
			if ( $cta->webinarId() === $webinarId && $cta->isEnabled() ) {
				$ctas[] = $cta;
			}
		}
		return $ctas;
	}

	public function save( array $data ): CTA {
		$uuid = $data['uuid'] ?? wp_generate_uuid4();

		$cta = new CTA(
			$uuid,
			(int) ( $data['webinar_id'] ?? 1 ),
			(string) ( $data['title'] ?? 'Special Offer' ),
			(string) ( $data['button_text'] ?? 'Enroll Now' ),
			(string) ( $data['destination_url'] ?? 'https://example.com/checkout' ),
			(string) ( $data['type'] ?? 'button' ),
			(string) ( $data['description'] ?? '' ),
			(int) ( $data['trigger_second'] ?? 0 ),
			(bool) ( $data['persistence'] ?? true ),
			(int) ( $data['countdown_duration'] ?? 300 ),
			(bool) ( $data['personalization'] ?? true ),
			(int) ( $data['priority'] ?? 90 ),
			(bool) ( $data['enabled'] ?? true )
		);

		$this->inMemoryCTAs[ $uuid ] = $cta;
		return $cta;
	}

	public function recordInteraction( CTAInteraction $interaction ): bool {
		$this->inMemoryInteractions[] = $interaction;
		return true;
	}
}
