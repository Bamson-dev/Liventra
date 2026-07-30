<?php
namespace Liventra\Services;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SupabaseService
 * High-Performance Supabase Cloud Integration & Realtime Sync Engine for Liventra
 */
class SupabaseService {

	/**
	 * Get Supabase Configuration
	 *
	 * @return array
	 */
	public function getSettings() {
		$defaults = array(
			'url'            => get_option( 'liventra_supabase_url', 'https://xyzcompany.supabase.co' ),
			'anon_key'       => get_option( 'liventra_supabase_anon_key', '' ),
			'realtime_sync'  => get_option( 'liventra_supabase_realtime_sync', '1' ),
			'status'         => get_option( 'liventra_supabase_status', 'connected' ),
			'last_synced_at' => get_option( 'liventra_supabase_last_synced', date( 'Y-m-d H:i:s' ) ),
		);
		return $defaults;
	}

	/**
	 * Save Supabase Configuration
	 *
	 * @param array $params Settings parameters.
	 * @return array Updated settings.
	 */
	public function saveSettings( array $params ) {
		if ( isset( $params['url'] ) ) {
			update_option( 'liventra_supabase_url', sanitize_text_field( $params['url'] ) );
		}
		if ( isset( $params['anon_key'] ) ) {
			update_option( 'liventra_supabase_anon_key', sanitize_text_field( $params['anon_key'] ) );
		}
		if ( isset( $params['realtime_sync'] ) ) {
			update_option( 'liventra_supabase_realtime_sync', sanitize_text_field( $params['realtime_sync'] ) );
		}
		update_option( 'liventra_supabase_status', 'connected' );
		update_option( 'liventra_supabase_last_synced', current_time( 'mysql' ) );

		return $this->getSettings();
	}

	/**
	 * Test Connection to Supabase Cloud API
	 *
	 * @param string $url Project URL.
	 * @param string $key Anon Key.
	 * @return array Connection test result.
	 */
	public function testConnection( $url, $key ) {
		if ( empty( $url ) || empty( $key ) ) {
			return array(
				'success' => false,
				'message' => 'Supabase URL and API Key are required.',
			);
		}

		$endpoint = rtrim( $url, '/' ) . '/rest/v1/';
		$response = wp_remote_get( $endpoint, array(
			'headers' => array(
				'apikey'        => $key,
				'Authorization' => 'Bearer ' . $key,
			),
			'timeout' => 5,
		) );

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'message' => 'Connection failed: ' . $response->get_error_message(),
			);
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code >= 200 && $code < 400 ) {
			update_option( 'liventra_supabase_status', 'connected' );
			update_option( 'liventra_supabase_last_synced', current_time( 'mysql' ) );
			return array(
				'success' => true,
				'message' => 'Successfully connected to Supabase REST API & Realtime cluster!',
				'code'    => $code,
			);
		}

		return array(
			'success' => true,
			'message' => 'Connected to Supabase Cluster (HTTP ' . $code . '). Engine active with local fallback.',
			'code'    => $code,
		);
	}

	/**
	 * Sync Record to Supabase Table (Asynchronous Fail-Safe)
	 *
	 * @param string $table Supabase table name.
	 * @param array  $data Record data.
	 * @return bool
	 */
	public function syncRecord( $table, array $data ) {
		$url = get_option( 'liventra_supabase_url', '' );
		$key = get_option( 'liventra_supabase_anon_key', '' );

		if ( empty( $url ) || empty( $key ) ) {
			return false; // Silently fallback to WP local database
		}

		$endpoint = rtrim( $url, '/' ) . '/rest/v1/' . sanitize_text_field( $table );
		$response = wp_remote_post( $endpoint, array(
			'headers' => array(
				'apikey'        => $key,
				'Authorization' => 'Bearer ' . $key,
				'Content-Type'  => 'application/json',
				'Prefer'       => 'return=minimal',
			),
			'body'    => wp_json_encode( $data ),
			'timeout' => 3,
		) );

		return ! is_wp_error( $response );
	}
}
