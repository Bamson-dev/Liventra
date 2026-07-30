<?php
namespace Liventra\Contracts\Services;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface AdminStudioServiceInterface
 * Public Contract for Admin Studio & Webinar Builder (PRD-012 Part 1)
 */
interface AdminStudioServiceInterface {

	/**
	 * Create new webinar draft
	 *
	 * @param array $data Webinar configuration payload.
	 * @return array Created webinar data.
	 */
	public function createWebinar( array $data ): array;

	/**
	 * Duplicate existing webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @return array Duplicated webinar data.
	 */
	public function duplicateWebinar( int $webinarId ): array;

	/**
	 * Publish webinar configuration (creates immutable version)
	 *
	 * @param int $webinarId Webinar ID.
	 * @return array Publication result.
	 */
	public function publishWebinar( int $webinarId ): array;

	/**
	 * Archive webinar
	 *
	 * @param int $webinarId Webinar ID.
	 * @return bool
	 */
	public function archiveWebinar( int $webinarId ): bool;

	/**
	 * Launch isolated preview session (PRD-012 Part 11)
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $offsetSeconds Playback jump offset.
	 * @return array Preview session parameters.
	 */
	public function previewWebinar( int $webinarId, int $offsetSeconds = 0 ): array;

	/**
	 * Retrieve studio executive dashboard metrics
	 *
	 * @return array Dashboard metric dictionary.
	 */
	public function getDashboard(): array;

	/**
	 * Save visual timeline layout draft
	 *
	 * @param int   $webinarId Webinar ID.
	 * @param array $timelineEvents Timeline event sequence payload.
	 * @return bool
	 */
	public function saveTimeline( int $webinarId, array $timelineEvents ): bool;

	/**
	 * Validate webinar configuration before publishing (PRD-012 Part 10)
	 *
	 * @param int $webinarId Webinar ID.
	 * @return array Validation errors array (empty if clean).
	 */
	public function validateConfiguration( int $webinarId ): array;

	/**
	 * Create immutable version snapshot (PRD-012 Part 9)
	 *
	 * @param int $webinarId Webinar ID.
	 * @return int Version number.
	 */
	public function createVersion( int $webinarId ): int;

	/**
	 * Restore version snapshot
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $versionNumber Target version.
	 * @return bool
	 */
	public function restoreVersion( int $webinarId, int $versionNumber ): bool;
}
