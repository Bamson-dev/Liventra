<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Registration;
use Liventra\Entities\AttendeeIdentity;
use Liventra\Entities\JoinToken;
use Liventra\Entities\WaitingRoomState;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface RegistrationServiceInterface
 * Public Contract for Registration & Identity Engine (PRD-008 Part 1)
 */
interface RegistrationServiceInterface {

	/**
	 * Register an attendee for a webinar
	 *
	 * @param int   $webinarId Webinar ID.
	 * @param array $data Attendee details (email, first_name, last_name).
	 * @return Registration
	 */
	public function register( int $webinarId, array $data ): Registration;

	/**
	 * Validate registration status and token
	 *
	 * @param string $tokenString Cryptographic token string.
	 * @return bool
	 */
	public function validateRegistration( string $tokenString ): bool;

	/**
	 * Generate secure signed join link for an attendee
	 *
	 * @param Registration $registration Registration entity.
	 * @return string Secure URL with signed token.
	 */
	public function generateJoinLink( Registration $registration ): string;

	/**
	 * Resolve attendee identity from token or ID
	 *
	 * @param string $tokenString Cryptographic token.
	 * @return AttendeeIdentity|null
	 */
	public function resolveAttendee( string $tokenString ): ?AttendeeIdentity;

	/**
	 * Assign attendee to session via SessionEngine
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $attendeeId Attendee ID.
	 * @return array Session assignment array.
	 */
	public function assignSession( int $webinarId, int $attendeeId ): array;

	/**
	 * Authorize attendee join attempt (PRD-008 Part 10)
	 *
	 * @param string $tokenString Cryptographic token string.
	 * @return bool
	 */
	public function authorizeJoin( string $tokenString ): bool;

	/**
	 * Transition attendee into Waiting Room (PRD-008 Part 7)
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $attendeeId Attendee ID.
	 * @return WaitingRoomState
	 */
	public function enterWaitingRoom( int $webinarId, int $attendeeId ): WaitingRoomState;

	/**
	 * Admit attendee from waiting room to live session
	 *
	 * @param int $webinarId Webinar ID.
	 * @param int $attendeeId Attendee ID.
	 * @return bool
	 */
	public function admitAttendee( int $webinarId, int $attendeeId ): bool;

	/**
	 * Reconnect attendee identity and session state (PRD-008 Part 11)
	 *
	 * @param string $tokenString Cryptographic token string.
	 * @return array Reconnect payload.
	 */
	public function reconnect( string $tokenString ): array;

	/**
	 * Invalidate expired or revoked token
	 *
	 * @param string $tokenString Token string.
	 * @return bool
	 */
	public function invalidateToken( string $tokenString ): bool;
}
