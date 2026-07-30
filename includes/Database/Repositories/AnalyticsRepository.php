<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\AnalyticsRepositoryInterface;
use Liventra\Entities\AnalyticsEvent;
use Liventra\Entities\Metric;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class AnalyticsRepository
 * Persistence implementation for Analytics Engine (PRD-003 & PRD-011)
 */
class AnalyticsRepository implements AnalyticsRepositoryInterface {

	private $inMemoryEvents  = array();
	private $inMemoryMetrics = array();

	public function findByUuid( string $uuid ): ?AnalyticsEvent {
		return isset( $this->inMemoryEvents[ $uuid ] ) ? $this->inMemoryEvents[ $uuid ] : null;
	}

	public function save( array $data ): AnalyticsEvent {
		$uuid = $data['uuid'] ?? wp_generate_uuid4();

		$event = new AnalyticsEvent(
			$uuid,
			(int) ( $data['webinar_id'] ?? 1 ),
			(int) ( $data['attendee_id'] ?? 1 ),
			(string) ( $data['event_type'] ?? 'pageview' ),
			$data['payload'] ?? array(),
			(int) ( $data['session_id'] ?? 0 ),
			(string) ( $data['source'] ?? 'server' )
		);

		$this->inMemoryEvents[ $uuid ] = $event;
		return $event;
	}

	public function getEventsForWebinar( int $webinarId ): array {
		$events = array();
		foreach ( $this->inMemoryEvents as $evt ) {
			if ( $evt->webinarId() === $webinarId ) {
				$events[] = $evt;
			}
		}
		return $events;
	}

	public function saveMetric( Metric $metric ): bool {
		$this->inMemoryMetrics[] = $metric;
		return true;
	}
}
