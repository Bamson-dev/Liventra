<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\LogEntry;
use Liventra\Entities\Span;
use Liventra\Entities\Metric;
use Liventra\Entities\HealthStatus;
use Liventra\Entities\DiagnosticReport;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface ObservabilityRepositoryInterface
 * Persistence contract for Observability Platform (PRD-016 Part 3)
 */
interface ObservabilityRepositoryInterface {

	public function saveLog( LogEntry $log ): bool;
	public function saveSpan( Span $span ): bool;
	public function saveMetric( Metric $metric ): bool;
	public function saveHealthStatus( HealthStatus $health ): bool;
	public function saveDiagnosticReport( DiagnosticReport $report ): bool;
}
