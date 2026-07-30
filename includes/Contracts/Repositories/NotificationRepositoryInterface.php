<?php
namespace Liventra\Contracts\Repositories;

use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;
use Liventra\Entities\NotificationTemplate;
use Liventra\Entities\NotificationPreference;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface NotificationRepositoryInterface
 * Persistence contract for Notification Platform (PRD-015 Part 3)
 */
interface NotificationRepositoryInterface {

	public function saveNotification( Notification $notification ): bool;
	public function findNotification( string $notificationId ): ?Notification;
	public function saveReceipt( DeliveryReceipt $receipt ): bool;
	public function saveTemplate( NotificationTemplate $template ): bool;
	public function findTemplate( string $templateId ): ?NotificationTemplate;
	public function savePreference( NotificationPreference $pref ): bool;
	public function findPreference( int $userId ): ?NotificationPreference;
}
