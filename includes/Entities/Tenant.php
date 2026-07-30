<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class Tenant {

	private $tenantId;
	private $orgId;
	private $domain;
	private $subdomain;

	public function __construct( string $tenantId, string $orgId, string $domain, string $subdomain = '' ) {
		$this->tenantId  = $tenantId;
		$this->orgId     = $orgId;
		$this->domain    = $domain;
		$this->subdomain = $subdomain;
	}

	public function tenantId(): string { return $this->tenantId; }
	public function orgId(): string { return $this->orgId; }
	public function domain(): string { return $this->domain; }
	public function subdomain(): string { return $this->subdomain; }
}
