<?php
namespace Liventra\Modules\Analytics;

use Liventra\Modules\ModuleInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class AnalyticsModule
 * Module 10 — Analytics Engine (PRD-002 Section 4)
 * Watch duration, drop-offs, CTA clicks, heat maps. Deferred batch operations.
 */
class AnalyticsModule implements ModuleInterface {

	public function get_name() {
		return 'analytics';
	}

	public function register() {}
	public function boot() {}
}
