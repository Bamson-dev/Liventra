<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\InAppNotificationServiceInterface;
use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class InAppNotificationService implements InAppNotificationServiceInterface {

	private $inAppStore = array();

	public function sendInApp( Notification $notification ): DeliveryReceipt {
		$this->inAppStore[ $notification->recipientId() ][] = $notification;
		return new DeliveryReceipt( 'rec_inapp_' . wp_generate_uuid4(), $notification->notificationId(), 'in_app', 'internal', 'delivered' );
	}

	public function getUnreadNotifications( int $userId ): array {
		return $this->inAppStore[ $userId ] ?? array();
	}
}
