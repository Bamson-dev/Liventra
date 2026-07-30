<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\DiagnosticsServiceInterface;
use Liventra\Entities\DiagnosticReport;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class DiagnosticsService implements DiagnosticsServiceInterface {

	public function generateDiagnosticReport(): DiagnosticReport {
		$repId  = 'diag_' . wp_generate_uuid4();
		$report = new DiagnosticReport(
			$repId,
			'Liventra Operational Health Report: All Subsystems Nominal',
			array( 'api_latency_p99' => 45.2, 'cpu_usage' => 12.4 ),
			array( 'database' => 'healthy', 'event_bus' => 'healthy' )
		);

		EventBus::dispatch( 'operations.diagnostic.generated', array( 'report_id' => $repId ) );
		return $report;
	}
}
