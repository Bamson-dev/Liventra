<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\Registration;
use Liventra\Entities\AttendeeIdentity;
use Liventra\Entities\JoinToken;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface RegistrationRepositoryInterface
 * Persistence contract for Registration Engine (PRD-008 Part 3)
 */
interface RegistrationRepositoryInterface {

	/**
	 * Find registration by ID
	 *
	 * @param int $registrationId Registration ID.
	 * @return Registration|null
	 */
	public function find( int $registrationId ): ?Registration;

	/**
	 * Find registration by email for a webinar (duplicate check)
	 *
	 * @param int    $webinarId Webinar ID.
	 * @param string $email Email address.
	 * @return Registration|null
	 */
	public function findByEmail( int $webinarId, string $email ): ?Registration;

	/**
	 * Create new registration record
	 *
	 * @param array $data Registration details.
	 * @return Registration
	 */
	public function create( array $data ): Registration;

	/**
	 * Create or find attendee identity
	 *
	 * @param array $data Attendee details.
	 * @return AttendeeIdentity
	 */
	public function saveAttendeeIdentity( array $data ): AttendeeIdentity;

	/**
	 * Save generated join token
	 *
	 * @param JoinToken $token Token object.
	 * @return bool
	 */
	public function saveToken( JoinToken $token ): bool;

	/**
	 * Find token by token string
	 *
	 * @param string $tokenString Cryptographic token.
	 * @return JoinToken|null
	 */
	public function findToken( string $tokenString ): ?JoinToken;
}
