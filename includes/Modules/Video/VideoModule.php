<?php
namespace Liventra\Modules\Video;

use Liventra\Modules\ModuleInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class VideoModule
 * Module 6 — Video Engine (PRD-002 Section 4)
 * Renderer only for chromeless playback. Always obeys Session Engine.
 */
class VideoModule implements ModuleInterface {

	public function get_name() {
		return 'video';
	}

	public function register() {
		// Register video scripts & templates
	}

	public function boot() {
		// Boot Video Engine
	}
}
