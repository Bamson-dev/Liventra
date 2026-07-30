<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\PushNotificationServiceInterface;
use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class PushNotificationService implements PushNotificationServiceInterface {
	public function sendPush( Notification $notification ): DeliveryReceipt {
		return new DeliveryReceipt( 'rec_push_' . wp_generate_uuid4(), $notification->notificationId(), 'push', 'fcm', 'delivered' );
	}
}
