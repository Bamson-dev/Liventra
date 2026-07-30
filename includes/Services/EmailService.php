<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\EmailServiceInterface;
use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class EmailService implements EmailServiceInterface {
	public function sendEmail( Notification $notification ): DeliveryReceipt {
		return new DeliveryReceipt( 'rec_email_' . wp_generate_uuid4(), $notification->notificationId(), 'email', 'ses', 'delivered' );
	}
}
