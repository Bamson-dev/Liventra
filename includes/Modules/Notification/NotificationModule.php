<?php
namespace Liventra\Modules\Notification;

use Liventra\Modules\ModuleInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class NotificationModule
 * Module 9 — Notification Engine (PRD-002 Section 4)
 * Purchase popups, join notifications, viewer count updates.
 */
class NotificationModule implements ModuleInterface {

	public function get_name() {
		return 'notification';
	}

	public function register() {}
	public function boot() {}
}
