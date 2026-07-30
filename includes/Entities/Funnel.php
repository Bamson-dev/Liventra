<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Funnel
 * Domain Entity representing Attendee Conversion Funnel (PRD-011 Part 2 & 7)
 */
class Funnel {

	private $webinarId;
	private $steps; // ['landing_page', 'registration', 'waiting_room', 'video_started', 'cta_viewed', 'cta_clicked', 'purchase']
	private $counts;
	private $conversionRates;

	public function __construct( int $webinarId, array $counts = array() ) {
		$this->webinarId = $webinarId;
		$this->steps     = array(
			'landing_page', 'registration', 'waiting_room', 'video_started', 'cta_viewed', 'cta_clicked', 'purchase'
		);

		$this->counts = array(
			'landing_page'  => $counts['landing_page'] ?? 100,
			'registration'  => $counts['registration'] ?? 65,
			'waiting_room'  => $counts['waiting_room'] ?? 50,
			'video_started' => $counts['video_started'] ?? 45,
			'cta_viewed'    => $counts['cta_viewed'] ?? 30,
			'cta_clicked'   => $counts['cta_clicked'] ?? 12,
			'purchase'      => $counts['purchase'] ?? 4,
		);

		$this->conversionRates = $this->calculateRates();
	}

	public function getWebinarId(): int { return $this->webinarId; }
	public function getSteps(): array { return $this->steps; }
	public function getCounts(): array { return $this->counts; }
	public function getConversionRates(): array { return $this->conversionRates; }

	private function calculateRates(): array {
		$rates = array();
		$landing = max( 1, $this->counts['landing_page'] );
		foreach ( $this->counts as $step => $count ) {
			$rates[ $step ] = round( ( $count / $landing ) * 100, 2 );
		}
		return $rates;
	}
}
