<?php
namespace Liventra\Extensions;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ExtensionDiagnostics
 * Diagnostics & Execution Metrics Tracker (Part 10)
 */
class ExtensionDiagnostics {

	private static $invocations = 0;
	private static $failures    = 0;
	private static $totalTimeMs  = 0.0;
	private static $handlerStats = array();

	public static function recordInvocation( string $handlerName, float $durationMs, bool $success = true ) {
		self::$invocations++;
		self::$totalTimeMs += $durationMs;

		if ( ! $success ) {
			self::$failures++;
		}

		if ( ! isset( self::$handlerStats[ $handlerName ] ) ) {
			self::$handlerStats[ $handlerName ] = array(
				'count'       => 0,
				'failures'    => 0,
				'total_ms'    => 0.0,
			);
		}

		self::$handlerStats[ $handlerName ]['count']++;
		self::$handlerStats[ $handlerName ]['total_ms'] += $durationMs;
		if ( ! $success ) {
			self::$handlerStats[ $handlerName ]['failures']++;
		}
	}

	public static function getMetrics(): array {
		return array(
			'total_invocations' => self::$invocations,
			'total_failures'    => self::$failures,
			'total_duration_ms' => self::$totalTimeMs,
			'avg_duration_ms'   => self::$invocations > 0 ? ( self::$totalTimeMs / self::$invocations ) : 0.0,
			'handler_stats'     => self::$handlerStats,
		);
	}

	public static function reset() {
		self::$invocations   = 0;
		self::$failures      = 0;
		self::$totalTimeMs    = 0.0;
		self::$handlerStats  = array();
	}
}
