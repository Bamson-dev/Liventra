<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\SecretManagerInterface;
use Liventra\Contracts\Repositories\SecurityRepositoryInterface;
use Liventra\Entities\Secret;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SecretManager
 * Authoritative Secret Management & Key Rotation Implementation (PRD-013 Part 6)
 * Secrets are encrypted at rest and NEVER logged.
 */
class SecretManager implements SecretManagerInterface {

	private $securityRepository;
	private $masterKey;

	public function __construct( SecurityRepositoryInterface $securityRepository = null, string $masterKey = 'liventra_master_vault_key' ) {
		$this->securityRepository = $securityRepository;
		$this->masterKey          = $masterKey;
	}

	public function storeSecret( string $key, string $plainValue ): Secret {
		$encrypted = base64_encode( $plainValue ^ str_repeat( $this->masterKey, ceil( strlen( $plainValue ) / strlen( $this->masterKey ) ) ) );
		$secret    = new Secret( $key, $encrypted, 1 );

		if ( $this->securityRepository ) {
			$this->securityRepository->saveSecret( $secret );
		}

		return $secret;
	}

	public function retrieveSecret( string $key ): ?string {
		$secret = $this->securityRepository ? $this->securityRepository->findSecret( $key ) : null;
		if ( ! $secret ) return null;

		$decoded = base64_decode( $secret->getEncryptedValue() );
		return $decoded ^ str_repeat( $this->masterKey, ceil( strlen( $decoded ) / strlen( $this->masterKey ) ) );
	}

	public function rotateSecret( string $key, string $newPlainValue ): Secret {
		$existing  = $this->securityRepository ? $this->securityRepository->findSecret( $key ) : null;
		$nextVer   = $existing ? $existing->getVersion() + 1 : 1;
		$encrypted = base64_encode( $newPlainValue ^ str_repeat( $this->masterKey, ceil( strlen( $newPlainValue ) / strlen( $this->masterKey ) ) ) );
		$secret    = new Secret( $key, $encrypted, $nextVer );

		if ( $this->securityRepository ) {
			$this->securityRepository->saveSecret( $secret );
		}

		EventBus::dispatch( 'security.secret.rotated', array( 'key' => $key, 'version' => $nextVer ) );
		return $secret;
	}
}
