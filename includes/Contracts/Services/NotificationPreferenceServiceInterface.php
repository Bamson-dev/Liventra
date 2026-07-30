<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\NotificationPreference;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface NotificationPreferenceServiceInterface {
	public function getPreferences( int $userId ): NotificationPreference;
	public function updatePreferences( int $userId, array $data ): NotificationPreference;
}
