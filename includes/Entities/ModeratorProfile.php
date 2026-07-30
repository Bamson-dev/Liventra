<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ModeratorProfile
 * Domain Entity representing a Webinar Host or Moderator (PRD-010 Part 2 & 10)
 */
class ModeratorProfile {

	private $moderatorId;
	private $name;
	private $avatar;
	private $role;

	public function __construct( int $moderatorId, string $name, ?string $avatar = null, string $role = 'moderator' ) {
		$this->moderatorId = $moderatorId;
		$this->name        = $name;
		$this->avatar      = $avatar;
		$this->role        = strtolower( $role );
	}

	public function getModeratorId(): int { return $this->moderatorId; }
	public function getName(): string { return $this->name; }
	public function getAvatar(): ?string { return $this->avatar; }
	public function getRole(): string { return $this->role; }
}
