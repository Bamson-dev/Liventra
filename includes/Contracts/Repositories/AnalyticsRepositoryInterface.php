<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\AnalyticsEvent;
use Liventra\Entities\Metric;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface AnalyticsRepositoryInterface
 * Persistence contract for Analytics Engine (PRD-011 Part 3)
 */
interface AnalyticsRepositoryInterface {

	/**
	 * Find analytics event by UUID
	 *
	 * @param string $uuid Event UUID.
	 * @return AnalyticsEvent|null
	 */
	public function findByUuid( string $uuid ): ?AnalyticsEvent;

	/**
	 * Save analytics event
	 *
	 * @param array $data Event data.
	 * @return AnalyticsEvent
	 */
	public function save( array $data ): AnalyticsEvent;

	/**
	 * Get events for a webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @return array Array of AnalyticsEvent entities.
	 */
	public function getEventsForWebinar( int $webinarId ): array;

	/**
	 * Save custom metric
	 *
	 * @param Metric $metric Metric entity.
	 * @return bool
	 */
	public function saveMetric( Metric $metric ): bool;
}
