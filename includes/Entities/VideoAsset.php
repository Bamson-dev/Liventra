<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class VideoAsset
 * Strongly typed Domain Entity representing a Video Asset (PRD-007 Part 2)
 */
class VideoAsset {

	private $uuid;
	private $webinarId;
	private $provider; // 'mp4' | 'hls' | 'bunny' | 'vimeo' | 'mux'
	private $source;
	private $duration;
	private $poster;
	private $captions;
	private $qualities;
	private $checksum;

	public function __construct(
		string $uuid,
		int $webinarId,
		string $provider,
		string $source,
		int $duration,
		?string $poster = null,
		array $captions = array(),
		array $qualities = array(),
		?string $checksum = null
	) {
		$this->uuid      = $uuid;
		$this->webinarId = $webinarId;
		$this->provider  = strtolower( $provider );
		$this->source    = $source;
		$this->duration  = max( 1, $duration );
		$this->poster    = $poster;
		$this->captions  = $captions;
		$this->qualities = ! empty( $qualities ) ? $qualities : array( 'auto', '1080p', '720p', '480p' );
		$this->checksum  = null !== $checksum ? $checksum : md5( $source . $duration );
	}

	public function uuid(): string { return $this->uuid; }
	public function webinarId(): int { return $this->webinarId; }
	public function provider(): string { return $this->provider; }
	public function source(): string { return $this->source; }
	public function duration(): int { return $this->duration; }
	public function poster(): ?string { return $this->poster; }
	public function captions(): array { return $this->captions; }
	public function qualities(): array { return $this->qualities; }
	public function checksum(): ?string { return $this->checksum; }

	public function toArray(): array {
		return array(
			'uuid'       => $this->uuid,
			'webinar_id' => $this->webinarId,
			'provider'   => $this->provider,
			'source'     => $this->source,
			'duration'   => $this->duration,
			'poster'     => $this->poster,
			'captions'   => $this->captions,
			'qualities'  => $this->qualities,
			'checksum'   => $this->checksum,
		);
	}
}
