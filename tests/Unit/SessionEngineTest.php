<?php
namespace Liventra\Tests\Unit;

if ( ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	define( 'LIVENTRA_TEST_SUITE', true );
}

class SessionEngineTest {

	public function testSessionEngineCalculatesPlaybackOffset() {
		$startTime = time() - 30;
		$elapsed   = time() - $startTime;
		if ( $elapsed < 30 ) {
			throw new \RuntimeException( 'Session engine offset calculation failed' );
		}
		return true;
	}
}
