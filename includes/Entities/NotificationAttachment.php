<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class NotificationAttachment {

	private $attachmentId;
	private $filename;
	private $mimeType;
	private $content;

	public function __construct( string $attachmentId, string $filename, string $mimeType, string $content ) {
		$this->attachmentId = $attachmentId;
		$this->filename     = $filename;
		$this->mimeType     = $mimeType;
		$this->content      = $content;
	}

	public function attachmentId(): string { return $this->attachmentId; }
	public function filename(): string { return $this->filename; }
	public function mimeType(): string { return $this->mimeType; }
	public function content(): string { return $this->content; }
}
