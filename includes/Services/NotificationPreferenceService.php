<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\NotificationPreferenceServiceInterface;
use Liventra\Entities\NotificationPreference;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class NotificationPreferenceService implements NotificationPreferenceServiceInterface {

	private $preferences = array();

	public function getPreferences( int $userId ): NotificationPreference {
		return $this->preferences[ $userId ] ?? new NotificationPreference( $userId );
	}

	public function updatePreferences( int $userId, array $data ): NotificationPreference {
		$pref = new NotificationPreference(
			$userId,
			(bool) ( $data['email'] ?? true ),
			(bool) ( $data['sms'] ?? true ),
			(bool) ( $data['whatsapp'] ?? true ),
			(bool) ( $data['push'] ?? true )
		);
		$this->preferences[ $userId ] = $pref;
		return $pref;
	}
}
