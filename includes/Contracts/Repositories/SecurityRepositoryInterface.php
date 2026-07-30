<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\AuditRecord;
use Liventra\Entities\SecurityEvent;
use Liventra\Entities\Secret;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface SecurityRepositoryInterface
 * Persistence contract for Security Platform (PRD-013 Part 3)
 */
interface SecurityRepositoryInterface {

	/**
	 * Save audit record
	 *
	 * @param AuditRecord $record Audit record entity.
	 * @return bool
	 */
	public function saveAuditRecord( AuditRecord $record ): bool;

	/**
	 * Get audit logs
	 *
	 * @param int $limit Limit count.
	 * @return array
	 */
	public function getAuditRecords( int $limit = 100 ): array;

	/**
	 * Save secret metadata
	 *
	 * @param Secret $secret Secret entity.
	 * @return bool
	 */
	public function saveSecret( Secret $secret ): bool;

	/**
	 * Find secret by key
	 *
	 * @param string $key Secret key.
	 * @return Secret|null
	 */
	public function findSecret( string $key ): ?Secret;
}
