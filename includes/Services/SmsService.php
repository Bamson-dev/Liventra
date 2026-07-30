<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\SmsServiceInterface;
use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class SmsService implements SmsServiceInterface {
	public function sendSms( Notification $notification ): DeliveryReceipt {
		return new DeliveryReceipt( 'rec_sms_' . wp_generate_uuid4(), $notification->notificationId(), 'sms', 'twilio', 'delivered' );
	}
}
