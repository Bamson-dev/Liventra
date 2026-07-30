<?php
namespace Liventra\REST;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ApiController
 * Abstract Base Controller for Liventra REST Endpoints
 */
abstract class ApiController {

	/**
	 * REST Namespace
	 *
	 * @var string
	 */
	protected $namespace = 'liventra/v1';

	/**
	 * Register REST Routes
	 */
	abstract public function register_routes();

	/**
	 * Format success response
	 *
	 * @param mixed $data Payload.
	 * @param int   $status HTTP status code.
	 * @return array Response structure.
	 */
	public function success_response( $data, $status = 200 ) {
		return array(
			'success' => true,
			'data'    => $data,
			'status'  => $status,
		);
	}

	/**
	 * Format error response
	 *
	 * @param string $message Error message.
	 * @param string $code Error code.
	 * @param int    $status HTTP status code.
	 * @return array Error payload.
	 */
	public function error_response( $message, $code = 'liventra_error', $status = 400 ) {
		return array(
			'success' => false,
			'code'    => $code,
			'message' => $message,
			'status'  => $status,
		);
	}
}
