<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\SessionRepositoryInterface;
use Liventra\Entities\Session;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SessionRepository
 * Implementation of SessionRepositoryInterface (PRD-003 & PRD-004)
 */
class SessionRepository implements SessionRepositoryInterface {

	protected static function get_table() {
		global $wpdb;
		$prefix = isset( $wpdb->prefix ) ? $wpdb->prefix : 'wp_';
		return $prefix . 'liventra_sessions';
	}

	public function find( int $sessionId ): ?Session {
		global $wpdb;
		if ( ! $wpdb ) return null;

		$table = self::get_table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE session_id = %d", $sessionId ), ARRAY_A );

		return $row ? $this->hydrateEntity( $row ) : null;
	}

	public function findByUuid( string $sessionUuid ): ?Session {
		global $wpdb;
		if ( ! $wpdb ) return null;

		$table = self::get_table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE session_uuid = %s", $sessionUuid ), ARRAY_A );

		return $row ? $this->hydrateEntity( $row ) : null;
	}

	public function create( array $data ): Session {
		global $wpdb;
		$table = self::get_table();

		if ( empty( $data['session_uuid'] ) ) {
			$data['session_uuid'] = wp_generate_uuid4();
		}

		if ( $wpdb ) {
			$wpdb->insert( $table, $data );
			$data['session_id'] = $wpdb->insert_id;
		} else {
			$data['session_id'] = 1;
		}

		return $this->hydrateEntity( $data );
	}

	public function updateStatus( int $sessionId, string $status ): bool {
		global $wpdb;
		if ( ! $wpdb ) return true;
		$table = self::get_table();
		return false !== $wpdb->update( $table, array( 'status' => $status ), array( 'session_id' => $sessionId ) );
	}

	public function incrementAttendeeCount( int $sessionId ): bool {
		global $wpdb;
		if ( ! $wpdb ) return true;
		$table = self::get_table();
		return false !== $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET attendee_count = attendee_count + 1 WHERE session_id = %d", $sessionId ) );
	}

	private function hydrateEntity( array $row ): Session {
		$start = new \DateTimeImmutable( isset( $row['scheduled_start'] ) ? $row['scheduled_start'] : 'now' );
		$end   = new \DateTimeImmutable( isset( $row['scheduled_end'] ) ? $row['scheduled_end'] : 'now' );

		return new Session(
			(int) ( isset( $row['session_id'] ) ? $row['session_id'] : 0 ),
			(int) ( isset( $row['webinar_id'] ) ? $row['webinar_id'] : 0 ),
			(string) ( isset( $row['session_uuid'] ) ? $row['session_uuid'] : '' ),
			$start,
			$end,
			(string) ( isset( $row['status'] ) ? $row['status'] : 'waiting' ),
			(int) ( isset( $row['attendee_count'] ) ? $row['attendee_count'] : 0 )
		);
	}
}
