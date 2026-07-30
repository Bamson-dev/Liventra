<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\TracingServiceInterface;
use Liventra\Entities\Span;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class TracingService implements TracingServiceInterface {

	public function startSpan( string $name, string $parentSpanId = '' ): Span {
		return new Span( 'span_' . wp_generate_uuid4(), 'trace_' . wp_generate_uuid4(), $name, $parentSpanId );
	}

	public function finishSpan( Span $span ): bool {
		$span->finish();
		return true;
	}
}
