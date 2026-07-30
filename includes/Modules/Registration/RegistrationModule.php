<?php
namespace Liventra\Modules\Registration;

use Liventra\Modules\ModuleInterface;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class RegistrationModule
 * Module 3 — Registration Engine (PRD-002 Section 4)
 * Owns registration forms, attendee admission, token validation.
 * Never owns session timing.
 */
class RegistrationModule implements ModuleInterface {

	public function get_name() {
		return 'registration';
	}

	public function register() {
		EventBus::on( 'attendee.register', array( $this, 'handle_registration' ) );
	}

	public function boot() {
		// Registration boot
	}

	public function handle_registration( $payload ) {
		// Attendee registration logic
		return array(
			'success' => true,
			'token'   => wp_generate_password( 32, false ),
		);
	}
}
