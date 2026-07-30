<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Secret;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface SecretManagerInterface
 * Public Contract for Encrypted Secret Management & Key Rotation (PRD-013 Part 1 & 6)
 */
interface SecretManagerInterface {

	/**
	 * Store secret encrypted at rest
	 *
	 * @param string $key Secret identifier key.
	 * @param string $plainValue Plaintext secret value.
	 * @return Secret
	 */
	public function storeSecret( string $key, string $plainValue ): Secret;

	/**
	 * Retrieve secret plaintext value (never logged)
	 *
	 * @param string $key Secret key.
	 * @return string|null Plaintext secret or null if missing.
	 */
	public function retrieveSecret( string $key ): ?string;

	/**
	 * Rotate secret key version
	 *
	 * @param string $key Secret key.
	 * @param string $newPlainValue New plaintext secret value.
	 * @return Secret
	 */
	public function rotateSecret( string $key, string $newPlainValue ): Secret;
}
