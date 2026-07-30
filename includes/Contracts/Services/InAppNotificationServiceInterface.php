<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface InAppNotificationServiceInterface {
	public function sendInApp( Notification $notification ): DeliveryReceipt;
	public function getUnreadNotifications( int $userId ): array;
}
