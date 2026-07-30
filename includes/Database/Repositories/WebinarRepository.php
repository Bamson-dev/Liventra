<?php
namespace Liventra\Database\Repositories;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class WebinarRepository
 * Data Access Repository for webinars Table (PRD-003 Section 5)
 */
class WebinarRepository {

	/**
	 * Get table name with prefix
	 *
	 * @return string
	 */
	protected static function get_table() {
		global $wpdb;
		$prefix = isset( $wpdb->prefix ) ? $wpdb->prefix : 'wp_';
		return $prefix . 'liventra_webinars';
	}

	/**
	 * Find webinar by ID
	 *
	 * @param int $webinar_id Primary key.
	 * @return array|null Record or null.
	 */
	public static function find( $webinar_id ) {
		global $wpdb;
		if ( ! $wpdb ) {
			return null;
		}

		$table = self::get_table();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE webinar_id = %d", $webinar_id ), ARRAY_A );
	}

	/**
	 * Find webinar by UUID
	 *
	 * @param string $uuid UUID char(36).
	 * @return array|null Record or null.
	 */
	public static function find_by_uuid( $uuid ) {
		global $wpdb;
		if ( ! $wpdb ) {
			return null;
		}

		$table = self::get_table();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE uuid = %s", $uuid ), ARRAY_A );
	}

	/**
	 * Insert new webinar
	 *
	 * @param array $data Webinar data array.
	 * @return int|false Inserted webinar_id or false.
	 */
	public static function create( array $data ) {
		global $wpdb;
		if ( ! $wpdb ) {
			return false;
		}

		$table = self::get_table();

		if ( empty( $data['uuid'] ) ) {
			$data['uuid'] = wp_generate_uuid4();
		}

		if ( is_array( $data['schedule_config'] ) ) {
			$data['schedule_config'] = wp_json_encode( $data['schedule_config'] );
		}

		$result = $wpdb->insert( $table, $data );
		return false !== $result ? $wpdb->insert_id : false;
	}
}
