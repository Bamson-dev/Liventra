<?php
namespace Liventra\Database;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Migrator
 * Manages creation and upgrading of Liventra custom database tables (PRD-003 Compliant)
 */
class Migrator {

	/**
	 * Database schema version
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * Get SQL statements for all custom tables matching PRD-003 Specification
	 *
	 * @param string $prefix Database table prefix (e.g., 'wp_').
	 * @param string $charset_collate Database charset collate string.
	 * @return array Array of SQL CREATE TABLE statements.
	 */
	public static function get_schema_sql( $prefix = 'wp_', $charset_collate = 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci' ) {
		$table_webinars        = $prefix . 'liventra_webinars';
		$table_sessions        = $prefix . 'liventra_sessions';
		$table_attendees       = $prefix . 'liventra_attendees';
		$table_timeline_events = $prefix . 'liventra_timeline_events';
		$table_analytics       = $prefix . 'liventra_analytics_events';

		$sql = array();

		// 1. Table 1: webinars (PRD-003 Section 5)
		$sql[] = "CREATE TABLE {$table_webinars} (
			webinar_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			uuid char(36) NOT NULL,
			title varchar(255) NOT NULL,
			slug varchar(255) NOT NULL,
			description longtext NULL,
			status enum('draft','published','archived') NOT NULL DEFAULT 'draft',
			schedule_type enum('fixed','jit','instant') NOT NULL DEFAULT 'fixed',
			schedule_config json NOT NULL,
			video_source_type enum('mp4','hls','bunny','vimeo') NOT NULL DEFAULT 'mp4',
			video_source longtext NOT NULL,
			duration_seconds int(10) unsigned NOT NULL DEFAULT 0,
			timezone varchar(64) NOT NULL DEFAULT 'UTC',
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (webinar_id),
			UNIQUE KEY uuid (uuid),
			UNIQUE KEY slug (slug),
			KEY status (status),
			KEY schedule_type (schedule_type)
		) {$charset_collate};";

		// 2. Table 2: sessions (PRD-003 Section 6)
		$sql[] = "CREATE TABLE {$table_sessions} (
			session_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			webinar_id bigint(20) unsigned NOT NULL,
			session_uuid char(36) NOT NULL,
			scheduled_start datetime NOT NULL,
			scheduled_end datetime NOT NULL,
			actual_start datetime DEFAULT NULL,
			status enum('waiting','live','ended','cancelled') NOT NULL DEFAULT 'waiting',
			attendee_count int(10) unsigned NOT NULL DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (session_id),
			UNIQUE KEY session_uuid (session_uuid),
			KEY webinar_id (webinar_id),
			KEY status (status),
			KEY scheduled_start (scheduled_start)
		) {$charset_collate};";

		// 3. Table 3: attendees (PRD-003 Section 7)
		$sql[] = "CREATE TABLE {$table_attendees} (
			attendee_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			webinar_id bigint(20) unsigned NOT NULL,
			session_id bigint(20) unsigned NOT NULL,
			attendee_token char(64) NOT NULL,
			email varchar(255) NOT NULL,
			first_name varchar(100) NOT NULL DEFAULT '',
			last_name varchar(100) NOT NULL DEFAULT '',
			ip_address varbinary(16) DEFAULT NULL,
			user_agent text DEFAULT NULL,
			joined_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			left_at datetime DEFAULT NULL,
			watch_seconds int(11) NOT NULL DEFAULT 0,
			status enum('registered','waiting','watching','left','completed') NOT NULL DEFAULT 'registered',
			PRIMARY KEY  (attendee_id),
			UNIQUE KEY attendee_token (attendee_token),
			KEY email (email),
			KEY session_id (session_id),
			KEY webinar_id (webinar_id),
			KEY status (status)
		) {$charset_collate};";

		// 4. Table 4: timeline_events (PRD-003 Section 8)
		$sql[] = "CREATE TABLE {$table_timeline_events} (
			event_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			webinar_id bigint(20) unsigned NOT NULL,
			event_type enum('chat','cta','poll','notification','countdown','redirect','bonus','coupon') NOT NULL,
			trigger_second int(10) unsigned NOT NULL,
			payload json NOT NULL,
			enabled tinyint(1) NOT NULL DEFAULT 1,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (event_id),
			KEY webinar_id (webinar_id),
			KEY trigger_second (trigger_second),
			KEY event_type (event_type)
		) {$charset_collate};";

		// 5. Table 5: analytics_events (PRD-003 Section 9)
		$sql[] = "CREATE TABLE {$table_analytics} (
			analytics_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			attendee_id bigint(20) unsigned NOT NULL,
			webinar_id bigint(20) unsigned NOT NULL,
			session_id bigint(20) unsigned NOT NULL,
			event_type varchar(100) NOT NULL,
			event_data json NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (analytics_id),
			KEY attendee_id (attendee_id),
			KEY webinar_id (webinar_id),
			KEY session_id (session_id),
			KEY event_type (event_type)
		) {$charset_collate};";

		return $sql;
	}

	/**
	 * Run database migrations
	 */
	public static function run() {
		global $wpdb;

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$charset_collate = $wpdb->get_charset_collate();
		$schemas         = self::get_schema_sql( $wpdb->prefix, $charset_collate );

		foreach ( $schemas as $sql ) {
			dbDelta( $sql );
		}

		update_option( 'liventra_db_version', self::DB_VERSION );
	}
}
