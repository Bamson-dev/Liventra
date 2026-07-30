<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\AuditRecord;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface AuditServiceInterface
 * Public Contract for Immutable Append-Only Audit Logging (PRD-013 Part 1 & 11)
 */
interface AuditServiceInterface {

	/**
	 * Append immutable security audit log record
	 *
	 * @param string $action Audit action type (e.g. 'publish', 'archive', 'delete').
	 * @param int    $actorId Actor / User ID.
	 * @param string $target Target resource identifier.
	 * @param array  $details Additional context payload.
	 * @return AuditRecord
	 */
	public function recordAudit( string $action, int $actorId, string $target, array $details = array() ): AuditRecord;

	/**
	 * Retrieve audit logs
	 *
	 * @param int $limit Max items.
	 * @return array Array of AuditRecord entities.
	 */
	public function getAuditLogs( int $limit = 100 ): array;
}
