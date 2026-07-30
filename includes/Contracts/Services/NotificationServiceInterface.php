<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface NotificationServiceInterface
 * Public Contract for Notification Orchestration Platform (PRD-015 Part 1)
 */
interface NotificationServiceInterface {

	/**
	 * Send immediate notification
	 *
	 * @param Notification $notification Notification entity.
	 * @return DeliveryReceipt
	 */
	public function send( Notification $notification ): DeliveryReceipt;

	/**
	 * Enqueue notification for async processing
	 *
	 * @param Notification $notification Notification entity.
	 * @return bool
	 */
	public function queue( Notification $notification ): bool;

	/**
	 * Schedule notification for future delivery
	 *
	 * @param Notification $notification Notification entity.
	 * @param int          $timestamp Scheduled UTC timestamp.
	 * @return bool
	 */
	public function schedule( Notification $notification, int $timestamp ): bool;

	/**
	 * Cancel scheduled notification
	 *
	 * @param string $notificationId Notification ID.
	 * @return bool
	 */
	public function cancel( string $notificationId ): bool;

	/**
	 * Retry failed notification delivery (PRD-015 Part 10)
	 *
	 * @param string $notificationId Notification ID.
	 * @return bool
	 */
	public function retry( string $notificationId ): bool;

	/**
	 * Render notification template with variable placeholders (PRD-015 Part 6)
	 *
	 * @param string $templateId Template ID.
	 * @param array  $variables Placeholders dictionary (first_name, webinar_name).
	 * @return array Rendered subject & body.
	 */
	public function renderTemplate( string $templateId, array $variables = array() ): array;
}
