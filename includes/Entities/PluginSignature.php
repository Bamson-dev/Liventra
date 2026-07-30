<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class PluginSignature {

	private $publisher;
	private $signatureHash;
	private $verified;

	public function __construct( string $publisher, string $signatureHash, bool $verified = true ) {
		$this->publisher     = $publisher;
		$this->signatureHash = $signatureHash;
		$this->verified      = $verified;
	}

	public function publisher(): string { return $this->publisher; }
	public function signatureHash(): string { return $this->signatureHash; }
	public function isVerified(): bool { return $this->verified; }
}
