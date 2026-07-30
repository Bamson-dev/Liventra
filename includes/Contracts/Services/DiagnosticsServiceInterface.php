<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\DiagnosticReport;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface DiagnosticsServiceInterface {
	public function generateDiagnosticReport(): DiagnosticReport;
}
