<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class Offer
 * Domain Entity representing a Pricing Offer attached to a CTA (PRD-009 Part 2)
 */
class Offer {

	private $offerId;
	private $ctaUuid;
	private $price;
	private $discountedPrice;
	private $currency;
	private $scarcitySeats;

	public function __construct(
		int $offerId,
		string $ctaUuid,
		float $price,
		float $discountedPrice,
		string $currency = 'USD',
		int $scarcitySeats = 0
	) {
		$this->offerId         = $offerId;
		$this->ctaUuid         = $ctaUuid;
		$this->price           = max( 0.0, $price );
		$this->discountedPrice = max( 0.0, $discountedPrice );
		$this->currency        = strtoupper( $currency );
		$this->scarcitySeats   = max( 0, $scarcitySeats );
	}

	public function getOfferId(): int { return $this->offerId; }
	public function getCtaUuid(): string { return $this->ctaUuid; }
	public function getPrice(): float { return $this->price; }
	public function getDiscountedPrice(): float { return $this->discountedPrice; }
	public function getCurrency(): string { return $this->currency; }
	public function getScarcitySeats(): int { return $this->scarcitySeats; }
}
