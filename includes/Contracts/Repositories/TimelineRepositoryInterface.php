<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\TimelineEvent;
use Liventra\Entities\TimelineVersion;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface TimelineRepositoryInterface
 * Persistence contract for Timeline Engine (PRD-006 Part 3)
 */
interface TimelineRepositoryInterface {

	/**
	 * Find event by primary key ID
	 *
	 * @param int $eventId Event ID.
	 * @return TimelineEvent|null
	 */
	public function find( int $eventId ): ?TimelineEvent;

	/**
	 * Find event by UUID
	 *
	 * @param string $uuid UUID string.
	 * @return TimelineEvent|null
	 */
	public function findByUuid( string $uuid ): ?TimelineEvent;

	/**
	 * Get all enabled events for a webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $version Version number (default latest).
	 * @return array Array of TimelineEvent entities.
	 */
	public function getEventsForWebinar( int $webinarId, int $version = 1 ): array;

	/**
	 * Save timeline event
	 *
	 * @param array $data Event data.
	 * @return TimelineEvent
	 */
	public function create( array $data ): TimelineEvent;

	/**
	 * Get latest published version for a webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @return TimelineVersion|null
	 */
	public function getLatestVersion( int $webinarId ): ?TimelineVersion;

	/**
	 * Save new timeline version
	 *
	 * @param array $data Version data.
	 * @return TimelineVersion
	 */
	public function createVersion( array $data ): TimelineVersion;
}
