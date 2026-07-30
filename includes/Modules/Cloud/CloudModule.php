<?php
namespace Liventra\Modules\Cloud;

use Liventra\Modules\ModuleInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class CloudModule
 * Module 11 — Cloud Connector (PRD-002 Section 4)
 * Handles licensing, updates, cloud backup.
 * Cloud failures MUST NEVER interrupt running webinars.
 */
class CloudModule implements ModuleInterface {

	public function get_name() {
		return 'cloud';
	}

	public function register() {}
	public function boot() {}
}
