<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Span;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface TracingServiceInterface {
	public function startSpan( string $name, string $parentSpanId = '' ): Span;
	public function finishSpan( Span $span ): bool;
}
