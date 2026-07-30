<?php
namespace Liventra\Database\Repositories;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class AttendeeRepository
 * Data Access Repository for attendees Table (PRD-003 Section 7)
 */
class AttendeeRepository {

	protected static function get_table() {
		global $wpdb;
		$prefix = isset( $wpdb->prefix ) ? $wpdb->prefix : 'wp_';
		return $prefix . 'liventra_attendees';
	}

	public static function find_by_token( $attendee_token ) {
		global $wpdb;
		if ( ! $wpdb ) return null;
		$table = self::get_table();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE attendee_token = %s", $attendee_token ), ARRAY_A );
	}
}
