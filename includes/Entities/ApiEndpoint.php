<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiEndpoint
 * Domain Entity representing Extension Registered API Endpoint (PRD-014 Part 2 & 13)
 */
class ApiEndpoint {

	private $route;
	private $method;
	private $callback;
	private $version;
	private $capability;

	public function __construct(
		string $route,
		string $method = 'GET',
		callable $callback = null,
		string $version = 'v1',
		string $capability = 'view_analytics'
	) {
		$this->route      = $route;
		$this->method     = strtoupper( $method );
		$this->callback   = $callback;
		$this->version    = strtolower( $version );
		$this->capability = $capability;
	}

	public function route(): string { return $this->route; }
	public function method(): string { return $this->method; }
	public function callback(): ?callable { return $this->callback; }
	public function version(): string { return $this->version; }
	public function capability(): string { return $this->capability; }
}
