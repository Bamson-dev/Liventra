<?php
namespace Liventra\Modules\Chat;

use Liventra\Modules\ModuleInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ChatModule
 * Module 8 — Chat Engine (PRD-002 Section 4)
 * Real attendee messages, simulated messages, moderation. Never controls playback.
 */
class ChatModule implements ModuleInterface {

	public function get_name() {
		return 'chat';
	}

	public function register() {}
	public function boot() {}
}
