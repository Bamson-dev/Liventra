<?php
namespace Liventra\Contracts\Services;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface SdkServiceInterface
 * Public Contract for Developer SDK & Extension Hooks (PRD-018 Part 1 & 6)
 */
interface SdkServiceInterface {

	public function registerHook( string $hookName, callable $callback, int $priority = 10 ): bool;
	public function registerService( string $serviceName, object $serviceInstance ): bool;
	public function registerRestEndpoint( string $route, string $method, callable $callback ): bool;
}
