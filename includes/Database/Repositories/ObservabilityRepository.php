<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\ObservabilityRepositoryInterface;
use Liventra\Entities\LogEntry;
use Liventra\Entities\Span;
use Liventra\Entities\Metric;
use Liventra\Entities\HealthStatus;
use Liventra\Entities\DiagnosticReport;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ObservabilityRepository
 * Persistence implementation for Operations Platform (PRD-003 & PRD-016)
 */
class ObservabilityRepository implements ObservabilityRepositoryInterface {

	private $inMemoryLogs       = array();
	private $inMemorySpans      = array();
	private $inMemoryMetrics    = array();
	private $inMemoryHealth     = array();
	private $inMemoryReports    = array();

	public function saveLog( LogEntry $log ): bool {
		$this->inMemoryLogs[] = $log;
		return true;
	}

	public function saveSpan( Span $span ): bool {
		$this->inMemorySpans[ $span->spanId() ] = $span;
		return true;
	}

	public function saveMetric( Metric $metric ): bool {
		$this->inMemoryMetrics[] = $metric;
		return true;
	}

	public function saveHealthStatus( HealthStatus $health ): bool {
		$this->inMemoryHealth[ $health->subsystem() ] = $health;
		return true;
	}

	public function saveDiagnosticReport( DiagnosticReport $report ): bool {
		$this->inMemoryReports[ $report->reportId() ] = $report;
		return true;
	}
}
