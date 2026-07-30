<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\PluginPackage;
use Liventra\Entities\Plugin;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface PluginInstallerInterface {
	public function installPackage( PluginPackage $package ): Plugin;
}
