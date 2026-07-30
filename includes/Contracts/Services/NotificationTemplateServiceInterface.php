<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\NotificationTemplate;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface NotificationTemplateServiceInterface {
	public function createTemplate( array $data ): NotificationTemplate;
	public function render( string $templateId, array $variables = array() ): array;
}
