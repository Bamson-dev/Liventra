<?php
namespace Liventra\Middleware;

use Liventra\Contracts\Services\SecurityServiceInterface;
use Liventra\Contracts\Services\AuthorizationServiceInterface;
use Liventra\Contracts\Services\AuditServiceInterface;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SecurityMiddleware
 * Centralized REST API Security Protection Middleware (PRD-013 Part 14)
 */
class SecurityMiddleware {

	private $securityService;
	private $authorizationService;
	private $auditService;

	public function __construct(
		SecurityServiceInterface $securityService,
		AuthorizationServiceInterface $authorizationService,
		AuditServiceInterface $auditService = null
	) {
		$this->securityService      = $securityService;
		$this->authorizationService = $authorizationService;
		$this->auditService         = $auditService;
	}

	/**
	 * Process REST request security checks
	 *
	 * @param array $request REST request headers and parameters.
	 * @return bool True if authorized, false if rejected.
	 */
	public function handle( array $request ): bool {
		$ipAddress = $request['ip'] ?? '127.0.0.1';

		// 1. Rate Limiting Check
		if ( ! $this->securityService->checkRateLimit( $ipAddress ) ) {
			return false;
		}

		// 2. Nonce / Signature Check if present
		if ( isset( $request['nonce'] ) ) {
			if ( ! $this->securityService->verifyNonce( $request['nonce'] ) ) {
				return false;
			}
		}

		// 3. Authorization Check
		$userId     = (int) ( $request['user_id'] ?? 1 );
		$capability = (string) ( $request['capability'] ?? 'view_analytics' );
		if ( ! $this->authorizationService->authorize( $userId, $capability ) ) {
			return false;
		}

		// 4. Audit Log Entry
		if ( $this->auditService ) {
			$this->auditService->recordAudit( 'api_access', $userId, $request['route'] ?? '/api', array( 'ip' => $ipAddress ) );
		}

		return true;
	}
}
