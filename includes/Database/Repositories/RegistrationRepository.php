<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\RegistrationRepositoryInterface;
use Liventra\Entities\Registration;
use Liventra\Entities\AttendeeIdentity;
use Liventra\Entities\JoinToken;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class RegistrationRepository
 * Persistence implementation for Registration Engine (PRD-003 & PRD-008)
 */
class RegistrationRepository implements RegistrationRepositoryInterface {

	protected static function get_attendees_table() {
		global $wpdb;
		$prefix = isset( $wpdb->prefix ) ? $wpdb->prefix : 'wp_';
		return $prefix . 'liventra_attendees';
	}

	private $inMemoryTokens        = array();
	private $inMemoryRegistrations = array();

	public function find( int $registrationId ): ?Registration {
		if ( isset( $this->inMemoryRegistrations[ $registrationId ] ) ) {
			return $this->inMemoryRegistrations[ $registrationId ];
		}
		return null;
	}

	public function findByEmail( int $webinarId, string $email ): ?Registration {
		$clean = strtolower( trim( $email ) );
		foreach ( $this->inMemoryRegistrations as $reg ) {
			if ( $reg->getWebinarId() === $webinarId && $reg->getEmail() === $clean ) {
				return $reg;
			}
		}
		return null;
	}

	public function create( array $data ): Registration {
		$regId      = count( $this->inMemoryRegistrations ) + 1;
		$webinarId  = (int) ( $data['webinar_id'] ?? 1 );
		$attendeeId = (int) ( $data['attendee_id'] ?? $regId );
		$email      = (string) ( $data['email'] ?? 'attendee@example.com' );
		$firstName  = (string) ( $data['first_name'] ?? '' );
		$lastName   = (string) ( $data['last_name'] ?? '' );
		$status     = (string) ( $data['status'] ?? Registration::STATUS_CONFIRMED );

		$reg = new Registration( $regId, $webinarId, $attendeeId, $email, $firstName, $lastName, $status );
		$this->inMemoryRegistrations[ $regId ] = $reg;
		return $reg;
	}

	public function saveAttendeeIdentity( array $data ): AttendeeIdentity {
		$attId     = (int) ( $data['attendee_id'] ?? rand( 10, 9999 ) );
		$email     = (string) ( $data['email'] ?? '' );
		$firstName = (string) ( $data['first_name'] ?? '' );
		$lastName  = (string) ( $data['last_name'] ?? '' );
		return new AttendeeIdentity( $attId, $email, $firstName, $lastName );
	}

	public function saveToken( JoinToken $token ): bool {
		$this->inMemoryTokens[ $token->getTokenString() ] = $token;
		return true;
	}

	public function findToken( string $tokenString ): ?JoinToken {
		return isset( $this->inMemoryTokens[ $tokenString ] ) ? $this->inMemoryTokens[ $tokenString ] : null;
	}
}
