<?php
namespace Liventra\Contracts\Services;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface HealthCheckServiceInterface {
	public function registerHealthCheck( string $subsystem, callable $callback ): bool;
	public function runHealthChecks(): array;
}
