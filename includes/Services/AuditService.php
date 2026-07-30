<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\AuditServiceInterface;
use Liventra\Contracts\Repositories\SecurityRepositoryInterface;
use Liventra\Entities\AuditRecord;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class AuditService
 * Authoritative Append-Only Audit Logging Service Implementation (PRD-013 Part 11)
 */
class AuditService implements AuditServiceInterface {

	private $securityRepository;

	public function __construct( SecurityRepositoryInterface $securityRepository = null ) {
		$this->securityRepository = $securityRepository;
	}

	public function recordAudit( string $action, int $actorId, string $target, array $details = array() ): AuditRecord {
		$record = new AuditRecord( rand( 1, 99999 ), $action, $actorId, $target, $details );

		if ( $this->securityRepository ) {
			$this->securityRepository->saveAuditRecord( $record );
		}

		EventBus::dispatch( 'security.audit.created', array(
			'action'   => $action,
			'actor_id' => $actorId,
			'target'   => $target,
		) );

		return $record;
	}

	public function getAuditLogs( int $limit = 100 ): array {
		return $this->securityRepository ? $this->securityRepository->getAuditRecords( $limit ) : array();
	}
}
