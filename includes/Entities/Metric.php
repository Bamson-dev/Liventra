<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class Metric {

	private $metricId;
	private $name;
	private $type; // 'counter' | 'gauge' | 'histogram'
	private $value;
	private $tags;
	private $timestamp;

	public function __construct( string $metricId, string $name, string $type, float $value, array $tags = array() ) {
		$this->metricId  = $metricId;
		$this->name      = $name;
		$this->type      = $type;
		$this->value     = $value;
		$this->tags      = $tags;
		$this->timestamp = time();
	}

	public function metricId(): string { return $this->metricId; }
	public function name(): string { return $this->name; }
	public function type(): string { return $this->type; }
	public function value(): float { return $this->value; }
	public function tags(): array { return $this->tags; }
}
