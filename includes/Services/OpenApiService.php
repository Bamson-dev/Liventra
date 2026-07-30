<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\OpenApiServiceInterface;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class OpenApiService
 * Authoritative OpenAPI 3.1 Specification Generator Implementation (PRD-014 Part 11)
 */
class OpenApiService implements OpenApiServiceInterface {

	public function generateOpenApi( string $format = 'json' ): string {
		$spec = array(
			'openapi' => '3.1.0',
			'info'    => array(
				'title'       => 'Liventra Evergreen Webinar API',
				'version'     => '1.0.0',
				'description' => 'Authoritative Public REST API for Liventra Engine',
			),
			'servers' => array(
				array( 'url' => '/wp-json/liventra/v1', 'description' => 'v1 Stable Endpoint' ),
				array( 'url' => '/wp-json/liventra/v2', 'description' => 'v2 Beta Endpoint' ),
			),
			'paths'   => array(
				'/webinars' => array(
					'get' => array( 'summary' => 'List Webinars' ),
				),
				'/registrations' => array(
					'post' => array( 'summary' => 'Create Attendee Registration' ),
				),
			),
		);

		EventBus::dispatch( 'openapi.generated', array( 'format' => $format ) );
		return (string) wp_json_encode( $spec );
	}
}
