<?php
namespace Liventra\Modules\CTA;

use Liventra\Modules\ModuleInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class CtaModule
 * Module 7 — CTA Engine (PRD-002 Section 4)
 * Offers, countdown timers, scarcity widgets responding to Timeline Engine.
 */
class CtaModule implements ModuleInterface {

	public function get_name() {
		return 'cta';
	}

	public function register() {}
	public function boot() {}
}
