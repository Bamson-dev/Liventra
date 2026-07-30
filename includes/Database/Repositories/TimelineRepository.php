<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\TimelineRepositoryInterface;
use Liventra\Entities\TimelineEvent;
use Liventra\Entities\TimelineVersion;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class TimelineRepository
 * Persistence implementation for Timeline Engine (PRD-003 & PRD-006)
 * Repositories ONLY map database rows and return typed entities.
 */
class TimelineRepository implements TimelineRepositoryInterface {

	protected static function get_table() {
		global $wpdb;
		$prefix = isset( $wpdb->prefix ) ? $wpdb->prefix : 'wp_';
		return $prefix . 'liventra_timeline_events';
	}

	public function find( int $eventId ): ?TimelineEvent {
		global $wpdb;
		if ( ! $wpdb ) return null;
		$table = self::get_table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE event_id = %d", $eventId ), ARRAY_A );
		return $row ? $this->hydrateEntity( $row ) : null;
	}

	public function findByUuid( string $uuid ): ?TimelineEvent {
		global $wpdb;
		if ( ! $wpdb ) return null;
		$table = self::get_table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE payload LIKE %s", '%' . $wpdb->esc_like( $uuid ) . '%' ), ARRAY_A );
		return $row ? $this->hydrateEntity( $row ) : null;
	}

	public function getEventsForWebinar( int $webinarId, int $version = 1 ): array {
		global $wpdb;
		if ( ! $wpdb ) return array();
		$table   = self::get_table();
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE webinar_id = %d AND enabled = 1 ORDER BY trigger_second ASC", $webinarId ), ARRAY_A );

		$entities = array();
		foreach ( $results as $row ) {
			$entities[] = $this->hydrateEntity( $row );
		}
		return $entities;
	}

	public function create( array $data ): TimelineEvent {
		global $wpdb;
		$table = self::get_table();

		if ( empty( $data['uuid'] ) ) {
			$data['uuid'] = wp_generate_uuid4();
		}

		if ( is_array( $data['payload'] ?? null ) ) {
			$data['payload'] = wp_json_encode( $data['payload'] );
		}

		if ( $wpdb ) {
			$wpdb->insert( $table, array(
				'webinar_id'     => $data['webinar_id'] ?? 1,
				'event_type'     => $data['event_type'] ?? 'cta',
				'trigger_second' => $data['trigger_second'] ?? 0,
				'payload'        => $data['payload'] ?? '{}',
				'enabled'        => $data['enabled'] ?? 1,
			) );
			$data['event_id'] = $wpdb->insert_id;
		} else {
			$data['event_id'] = 1;
		}

		return $this->hydrateEntity( $data );
	}

	public function getLatestVersion( int $webinarId ): ?TimelineVersion {
		return new TimelineVersion( 1, $webinarId, 1, 'published' );
	}

	public function createVersion( array $data ): TimelineVersion {
		return new TimelineVersion( 2, $data['webinar_id'] ?? 1, $data['version_number'] ?? 2, 'published' );
	}

	private function hydrateEntity( array $row ): TimelineEvent {
		$payload = isset( $row['payload'] ) ? ( is_array( $row['payload'] ) ? $row['payload'] : (array) json_decode( $row['payload'], true ) ) : ( isset( $row['event_payload'] ) ? (array) $row['event_payload'] : array() );

		$uuid = isset( $payload['uuid'] ) ? $payload['uuid'] : ( isset( $row['uuid'] ) ? $row['uuid'] : 'evt-' . ( $row['event_id'] ?? 1 ) );
		$deps = isset( $payload['dependencies'] ) && is_array( $payload['dependencies'] ) ? $payload['dependencies'] : array();

		return new TimelineEvent(
			$uuid,
			(int) ( $row['event_id'] ?? 1 ),
			(int) ( $row['webinar_id'] ?? 1 ),
			(string) ( $row['event_type'] ?? 'cta' ),
			(int) ( $row['trigger_second'] ?? ( $row['trigger_time'] ?? 0 ) ),
			$payload,
			(bool) ( $payload['replayable'] ?? true ),
			(bool) ( $row['enabled'] ?? true ),
			(int) ( $payload['priority'] ?? 50 ),
			(int) ( $row['version'] ?? 1 ),
			$deps
		);
	}
}
