<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\NotificationTemplateServiceInterface;
use Liventra\Entities\NotificationTemplate;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class NotificationTemplateService implements NotificationTemplateServiceInterface {

	private $templates = array();

	public function createTemplate( array $data ): NotificationTemplate {
		$id       = $data['template_id'] ?? 'tmpl_' . wp_generate_uuid4();
		$template = new NotificationTemplate(
			$id,
			(string) ( $data['name'] ?? 'Custom Template' ),
			(string) ( $data['subject'] ?? 'Hello {{first_name}}' ),
			(string) ( $data['body'] ?? 'Your webinar {{webinar_name}} starts soon.' ),
			(string) ( $data['channel'] ?? 'email' )
		);

		$this->templates[ $id ] = $template;
		return $template;
	}

	public function render( string $templateId, array $variables = array() ): array {
		if ( isset( $this->templates[ $templateId ] ) ) {
			return $this->templates[ $templateId ]->render( $variables );
		}
		return array( 'subject' => 'Subject', 'body' => 'Body' );
	}
}
