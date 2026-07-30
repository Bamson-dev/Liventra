<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\WhatsAppServiceInterface;
use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class WhatsAppService implements WhatsAppServiceInterface {
	public function sendWhatsApp( Notification $notification ): DeliveryReceipt {
		return new DeliveryReceipt( 'rec_wa_' . wp_generate_uuid4(), $notification->notificationId(), 'whatsapp', 'meta_cloud', 'delivered' );
	}
}
