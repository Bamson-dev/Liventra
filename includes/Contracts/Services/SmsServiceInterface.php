<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface SmsServiceInterface {
	public function sendSms( Notification $notification ): DeliveryReceipt;
}
