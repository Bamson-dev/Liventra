<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\LoggingServiceInterface;
use Liventra\Entities\LogEntry;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class LoggingService implements LoggingServiceInterface {
	public function log( string $level, string $message, array $context = array() ): LogEntry {
		return new LogEntry( 'log_' . wp_generate_uuid4(), 'cid_default', '', $level, $message, $context );
	}
}
