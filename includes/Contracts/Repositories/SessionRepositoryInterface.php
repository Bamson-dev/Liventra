<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\Session;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface SessionRepositoryInterface
 * Contract for Session persistence operations (PRD-003 & PRD-004)
 */
interface SessionRepositoryInterface {

	/**
	 * Find session by primary key ID
	 *
	 * @param int $sessionId Session ID.
	 * @return Session|null
	 */
	public function find( int $sessionId ): ?Session;

	/**
	 * Find session by public UUID
	 *
	 * @param string $sessionUuid UUID string.
	 * @return Session|null
	 */
	public function findByUuid( string $sessionUuid ): ?Session;

	/**
	 * Create new session record
	 *
	 * @param array $data Session attributes.
	 * @return Session
	 */
	public function create( array $data ): Session;

	/**
	 * Update session state status
	 *
	 * @param int    $sessionId Session ID.
	 * @param string $status New status ('waiting', 'live', 'ended', 'cancelled').
	 * @return bool
	 */
	public function updateStatus( int $sessionId, string $status ): bool;

	/**
	 * Increment active attendee count
	 *
	 * @param int $sessionId Session ID.
	 * @return bool
	 */
	public function incrementAttendeeCount( int $sessionId ): bool;
}
