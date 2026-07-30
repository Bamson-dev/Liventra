<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\LogEntry;
use Liventra\Entities\Span;
use Liventra\Entities\Metric;
use Liventra\Entities\HealthStatus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface ObservabilityServiceInterface
 * Public Contract for Central Observability & Operations Platform (PRD-016 Part 1)
 */
interface ObservabilityServiceInterface {

	/**
	 * Log structured telemetry entry
	 *
	 * @param string $level Log level ('info'|'warning'|'error'|'critical').
	 * @param string $message Log message.
	 * @param array  $context Context parameters.
	 * @return LogEntry
	 */
	public function log( string $level, string $message, array $context = array() ): LogEntry;

	/**
	 * Start distributed tracing span (PRD-016 Part 6)
	 *
	 * @param string $name Span operation name.
	 * @param string $parentSpanId Optional parent span ID.
	 * @return Span
	 */
	public function startSpan( string $name, string $parentSpanId = '' ): Span;

	/**
	 * Finish active tracing span
	 *
	 * @param Span $span Span entity.
	 * @return bool
	 */
	public function finishSpan( Span $span ): bool;

	/**
	 * Record operational metric
	 *
	 * @param string $name Metric name.
	 * @param float  $value Numerical value.
	 * @param string $type Metric type ('counter'|'gauge'|'histogram').
	 * @param array  $tags Key-value tags.
	 * @return Metric
	 */
	public function recordMetric( string $name, float $value, string $type = 'gauge', array $tags = array() ): Metric;

	/**
	 * Register subsystem health probe
	 *
	 * @param string   $subsystem Subsystem name.
	 * @param callable $callback Health evaluation callback.
	 * @return bool
	 */
	public function registerHealthCheck( string $subsystem, callable $callback ): bool;

	/**
	 * Execute all subsystem health checks
	 *
	 * @return array Array of HealthStatus entities.
	 */
	public function runHealthChecks(): array;

	/**
	 * Create unique Correlation ID for request lifecycle
	 *
	 * @return string Correlation ID string.
	 */
	public function createCorrelationId(): string;
}
