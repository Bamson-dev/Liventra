<?php
namespace Liventra\Contracts\Services;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface OpenApiServiceInterface
 * Public Contract for OpenAPI 3.1 Specification Generator (PRD-014 Part 1 & 11)
 */
interface OpenApiServiceInterface {

	/**
	 * Generate OpenAPI 3.1 Specification Schema
	 *
	 * @param string $format Format output ('json' | 'yaml').
	 * @return string Schema output string.
	 */
	public function generateOpenApi( string $format = 'json' ): string;
}
