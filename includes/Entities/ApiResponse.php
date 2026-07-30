<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiResponse
 * Domain Entity representing Normalized API Response (PRD-014 Part 2 & 9 Pagination)
 */
class ApiResponse {

	private $status;
	private $data;
	private $errors;
	private $meta;

	public function __construct( int $status = 200, array $data = array(), array $errors = array(), array $meta = array() ) {
		$this->status = $status;
		$this->data   = $data;
		$this->errors = $errors;
		$this->meta   = $meta;
	}

	public function status(): int { return $this->status; }
	public function data(): array { return $this->data; }
	public function errors(): array { return $this->errors; }
	public function meta(): array { return $this->meta; }

	public function toArray(): array {
		return array(
			'status' => $this->status,
			'data'   => $this->data,
			'errors' => $this->errors,
			'meta'   => $this->meta,
		);
	}
}
