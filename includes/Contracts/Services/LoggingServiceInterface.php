<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\LogEntry;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface LoggingServiceInterface {
	public function log( string $level, string $message, array $context = array() ): LogEntry;
}
