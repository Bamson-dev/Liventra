<?php
namespace Liventra\REST;

use Liventra\Contracts\Services\NotificationServiceInterface;
use Liventra\Entities\Notification;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class NotificationController
 * Thin REST API Controller for Notification Platform (PRD-015 Part 14)
 */
class NotificationController {

	private $notificationService;

	public function __construct( NotificationServiceInterface $notificationService ) {
		$this->notificationService = $notificationService;
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) return;

		register_rest_route( 'liventra/v1', '/notifications/send', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'send_notification' ),
			'permission_callback'=> '__return_true',
		) );
	}

	public function send_notification( $request ) {
		$params  = $request->get_json_params() ?? array();
		$notif   = new Notification(
			'notif_' . wp_generate_uuid4(),
			(int) ( $params['recipient_id'] ?? 1 ),
			(string) ( $params['channel'] ?? 'email' ),
			(string) ( $params['subject'] ?? 'Notification' ),
			(string) ( $params['body'] ?? '' )
		);
		$receipt = $this->notificationService->send( $notif );

		return rest_ensure_response( array(
			'notification_id' => $notif->notificationId(),
			'receipt_id'      => $receipt->receiptId(),
			'status'          => $receipt->status(),
		) );
	}
}
