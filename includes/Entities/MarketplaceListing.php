<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class MarketplaceListing {

	private $listingId;
	private $title;
	private $author;
	private $description;
	private $rating;
	private $downloads;

	public function __construct(
		string $listingId,
		string $title,
		string $author,
		string $description = '',
		float $rating = 5.0,
		int $downloads = 100
	) {
		$this->listingId   = $listingId;
		$this->title       = $title;
		$this->author      = $author;
		$this->description = $description;
		$this->rating      = $rating;
		$this->downloads   = $downloads;
	}

	public function listingId(): string { return $this->listingId; }
	public function title(): string { return $this->title; }
	public function author(): string { return $this->author; }
	public function description(): string { return $this->description; }
	public function rating(): float { return $this->rating; }
	public function downloads(): int { return $this->downloads; }
}
