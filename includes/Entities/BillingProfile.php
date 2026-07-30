<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class BillingProfile {

	private $orgId;
	private $tier; // 'starter' | 'growth' | 'enterprise'
	private $billingEmail;
	private $status;

	public function __construct( string $orgId, string $tier = 'enterprise', string $billingEmail = 'billing@acme.com', string $status = 'active' ) {
		$this->orgId        = $orgId;
		$this->tier         = strtolower( $tier );
		$this->billingEmail = $billingEmail;
		$this->status       = strtolower( $status );
	}

	public function orgId(): string { return $this->orgId; }
	public function tier(): string { return $this->tier; }
	public function billingEmail(): string { return $this->billingEmail; }
	public function status(): string { return $this->status; }
}
