<?php
namespace Liventra\Database\Repositories;

use Liventra\Contracts\Repositories\NotificationRepositoryInterface;
use Liventra\Entities\Notification;
use Liventra\Entities\DeliveryReceipt;
use Liventra\Entities\NotificationTemplate;
use Liventra\Entities\NotificationPreference;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class NotificationRepository
 * Persistence implementation for Notification Platform (PRD-003 & PRD-015)
 */
class NotificationRepository implements NotificationRepositoryInterface {

	private $inMemoryNotifications = array();
	private $inMemoryReceipts      = array();
	private $inMemoryTemplates     = array();
	private $inMemoryPreferences   = array();

	public function saveNotification( Notification $notification ): bool {
		$this->inMemoryNotifications[ $notification->notificationId() ] = $notification;
		return true;
	}

	public function findNotification( string $notificationId ): ?Notification {
		return isset( $this->inMemoryNotifications[ $notificationId ] ) ? $this->inMemoryNotifications[ $notificationId ] : null;
	}

	public function saveReceipt( DeliveryReceipt $receipt ): bool {
		$this->inMemoryReceipts[] = $receipt;
		return true;
	}

	public function saveTemplate( NotificationTemplate $template ): bool {
		$this->inMemoryTemplates[ $template->templateId() ] = $template;
		return true;
	}

	public function findTemplate( string $templateId ): ?NotificationTemplate {
		return isset( $this->inMemoryTemplates[ $templateId ] ) ? $this->inMemoryTemplates[ $templateId ] : null;
	}

	public function savePreference( NotificationPreference $pref ): bool {
		$this->inMemoryPreferences[ $pref->userId() ] = $pref;
		return true;
	}

	public function findPreference( int $userId ): ?NotificationPreference {
		return isset( $this->inMemoryPreferences[ $userId ] ) ? $this->inMemoryPreferences[ $userId ] : null;
	}
}
