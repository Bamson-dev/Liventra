<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class EnterprisePolicy {

	private $policyId;
	private $orgId;
	private $mfaRequired;
	private $ipRestrictions;
	private $passwordMinLength;

	public function __construct(
		string $policyId,
		string $orgId,
		bool $mfaRequired = true,
		array $ipRestrictions = array(),
		int $passwordMinLength = 12
	) {
		$this->policyId          = $policyId;
		$this->orgId             = $orgId;
		$this->mfaRequired       = $mfaRequired;
		$this->ipRestrictions    = $ipRestrictions;
		$this->passwordMinLength = $passwordMinLength;
	}

	public function policyId(): string { return $this->policyId; }
	public function orgId(): string { return $this->orgId; }
	public function mfaRequired(): bool { return $this->mfaRequired; }
	public function ipRestrictions(): array { return $this->ipRestrictions; }
	public function passwordMinLength(): int { return $this->passwordMinLength; }
}
