<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\SecurityRepositoryInterface;
use Liventra\Entities\AuditRecord;
use Liventra\Entities\Secret;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SecurityRepository
 * Persistence implementation for Security Platform (PRD-003 & PRD-013)
 */
class SecurityRepository implements SecurityRepositoryInterface {

	private $inMemoryAuditLogs = array();
	private $inMemorySecrets   = array();

	public function saveAuditRecord( AuditRecord $record ): bool {
		$this->inMemoryAuditLogs[] = $record;
		return true;
	}

	public function getAuditRecords( int $limit = 100 ): array {
		return array_slice( $this->inMemoryAuditLogs, -$limit );
	}

	public function saveSecret( Secret $secret ): bool {
		$this->inMemorySecrets[ $secret->getKey() ] = $secret;
		return true;
	}

	public function findSecret( string $key ): ?Secret {
		return isset( $this->inMemorySecrets[ $key ] ) ? $this->inMemorySecrets[ $key ] : null;
	}
}
