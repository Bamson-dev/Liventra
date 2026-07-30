<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiRequest
 * Domain Entity representing an Incoming API Request (PRD-014 Part 2)
 */
class ApiRequest {

	private $requestId;
	private $endpoint;
	private $method;
	private $params;
	private $headers;
	private $version;
	private $timestamp;

	public function __construct(
		string $requestId,
		string $endpoint,
		string $method = 'GET',
		array $params = array(),
		array $headers = array(),
		string $version = 'v1'
	) {
		$this->requestId = $requestId;
		$this->endpoint  = $endpoint;
		$this->method    = strtoupper( $method );
		$this->params    = $params;
		$this->headers   = $headers;
		$this->version   = $version;
		$this->timestamp = time();
	}

	public function requestId(): string { return $this->requestId; }
	public function endpoint(): string { return $this->endpoint; }
	public function method(): string { return $this->method; }
	public function params(): array { return $this->params; }
	public function headers(): array { return $this->headers; }
	public function version(): string { return $this->version; }
}
