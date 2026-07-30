<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\AnalyticsEvent;
use Liventra\Entities\Metric;
use Liventra\Entities\Funnel;
use Liventra\Entities\EngagementSnapshot;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface AnalyticsServiceInterface
 * Public Contract for Analytics & Event Intelligence Engine (PRD-011 Part 1)
 */
interface AnalyticsServiceInterface {

	/**
	 * Track a single analytics event
	 *
	 * @param string $eventType Event type string.
	 * @param int    $webinarId Webinar ID.
	 * @param int    $attendeeId Attendee ID.
	 * @param array  $payload Custom event payload.
	 * @return AnalyticsEvent
	 */
	public function track( string $eventType, int $webinarId, int $attendeeId, array $payload = array() ): AnalyticsEvent;

	/**
	 * Track a batch of analytics events
	 *
	 * @param array $events Array of raw event arrays or AnalyticsEvent entities.
	 * @return bool
	 */
	public function trackBatch( array $events ): bool;

	/**
	 * Identify attendee attribution context
	 *
	 * @param int   $attendeeId Attendee ID.
	 * @param array $context Context attributes (UTMs, IP, device, referrer).
	 * @return bool
	 */
	public function identifyAttendee( int $attendeeId, array $context ): bool;

	/**
	 * Record custom metric
	 *
	 * @param Metric $metric Metric entity.
	 * @return bool
	 */
	public function recordMetric( Metric $metric ): bool;

	/**
	 * Record purchase conversion event with revenue attribution
	 *
	 * @param int   $webinarId Webinar ID.
	 * @param int   $attendeeId Attendee ID.
	 * @param float $revenue Revenue amount.
	 * @param array $payload Additional conversion data.
	 * @return AnalyticsEvent
	 */
	public function recordConversion( int $webinarId, int $attendeeId, float $revenue, array $payload = array() ): AnalyticsEvent;

	/**
	 * Build complete chronological journey timeline for an attendee
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $attendeeId Attendee ID.
	 * @return array Chronological array of AnalyticsEvent entities.
	 */
	public function buildTimeline( int $webinarId, int $attendeeId ): array;

	/**
	 * Aggregate metrics for a webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @return EngagementSnapshot
	 */
	public function aggregate( int $webinarId ): EngagementSnapshot;

	/**
	 * Export analytics dataset (PRD-011 Part 12)
	 *
	 * @param int    $webinarId Webinar ID.
	 * @param string $format Format string ('csv' | 'json').
	 * @return string Exported data string.
	 */
	public function export( int $webinarId, string $format = 'csv' ): string;

	/**
	 * Get aggregated dashboard summary metrics (PRD-011 Part 11)
	 *
	 * @param int $webinarId Webinar ID.
	 * @return array Summary metrics dictionary.
	 */
	public function getDashboardMetrics( int $webinarId ): array;
}
