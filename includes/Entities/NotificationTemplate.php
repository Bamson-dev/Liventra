<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class NotificationTemplate {

	private $templateId;
	private $name;
	private $subjectTemplate;
	private $bodyTemplate;
	private $channel;

	public function __construct(
		string $templateId,
		string $name,
		string $subjectTemplate,
		string $bodyTemplate,
		string $channel = 'email'
	) {
		$this->templateId      = $templateId;
		$this->name            = $name;
		$this->subjectTemplate = $subjectTemplate;
		$this->bodyTemplate    = $bodyTemplate;
		$this->channel         = strtolower( $channel );
	}

	public function templateId(): string { return $this->templateId; }
	public function name(): string { return $this->name; }
	public function subjectTemplate(): string { return $this->subjectTemplate; }
	public function bodyTemplate(): string { return $this->bodyTemplate; }
	public function channel(): string { return $this->channel; }

	public function render( array $variables ): array {
		$subject = $this->subjectTemplate;
		$body    = $this->bodyTemplate;

		foreach ( $variables as $k => $v ) {
			$sub = (string) $v;
			$subject = str_replace( array( '{{' . $k . '}}', '{' . $k . '}' ), $sub, $subject );
			$body    = str_replace( array( '{{' . $k . '}}', '{' . $k . '}' ), $sub, $body );
		}

		return array(
			'subject' => $subject,
			'body'    => $body,
		);
	}
}
