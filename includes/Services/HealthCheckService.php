<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\HealthCheckServiceInterface;
use Liventra\Entities\HealthStatus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class HealthCheckService implements HealthCheckServiceInterface {

	private $probes = array();

	public function registerHealthCheck( string $subsystem, callable $callback ): bool {
		$this->probes[ $subsystem ] = $callback;
		return true;
	}

	public function runHealthChecks(): array {
		$results = array();
		foreach ( $this->probes as $subsystem => $probe ) {
			$results[] = call_user_func( $probe );
		}
		if ( empty( $results ) ) {
			$results[] = new HealthStatus( 'system', 'healthy' );
		}
		return $results;
	}
}
