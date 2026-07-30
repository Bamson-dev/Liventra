<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class BrandingProfile {

	private $profileId;
	private $orgId;
	private $customDomain;
	private $logoUrl;
	private $primaryColor;

	public function __construct(
		string $profileId,
		string $orgId,
		string $customDomain = '',
		string $logoUrl = '',
		string $primaryColor = '#6366f1'
	) {
		$this->profileId    = $profileId;
		$this->orgId        = $orgId;
		$this->customDomain = $customDomain;
		$this->logoUrl      = $logoUrl;
		$this->primaryColor = $primaryColor;
	}

	public function profileId(): string { return $this->profileId; }
	public function orgId(): string { return $this->orgId; }
	public function customDomain(): string { return $this->customDomain; }
	public function logoUrl(): string { return $this->logoUrl; }
	public function primaryColor(): string { return $this->primaryColor; }
}
