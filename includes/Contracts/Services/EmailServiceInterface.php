<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface EmailServiceInterface {
	public function sendEmail( Notification $notification ): DeliveryReceipt;
}
