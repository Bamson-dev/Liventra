<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class AuditExport {

	private $exportId;
	private $orgId;
	private $recordsCount;
	private $downloadUrl;

	public function __construct( string $exportId, string $orgId, int $recordsCount, string $downloadUrl ) {
		$this->exportId     = $exportId;
		$this->orgId        = $orgId;
		$this->recordsCount = $recordsCount;
		$this->downloadUrl  = $downloadUrl;
	}

	public function exportId(): string { return $this->exportId; }
	public function orgId(): string { return $this->orgId; }
	public function recordsCount(): int { return $this->recordsCount; }
	public function downloadUrl(): string { return $this->downloadUrl; }
}
