<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class EngagementSnapshot
 * Domain Entity representing Webinar Engagement Analytics Snapshot (PRD-011 Part 2 & 6)
 */
class EngagementSnapshot {

	private $webinarId;
	private $totalRegistrations;
	private $liveAttendees;
	private $avgWatchTimeSeconds;
	private $completionPercentage;
	private $totalRevenue;
	private $ctrPercentage;

	public function __construct(
		int $webinarId,
		int $totalRegistrations = 0,
		int $liveAttendees = 0,
		float $avgWatchTimeSeconds = 0.0,
		float $completionPercentage = 0.0,
		float $totalRevenue = 0.0,
		float $ctrPercentage = 0.0
	) {
		$this->webinarId            = $webinarId;
		$this->totalRegistrations   = max( 0, $totalRegistrations );
		$this->liveAttendees        = max( 0, $liveAttendees );
		$this->avgWatchTimeSeconds  = max( 0.0, $avgWatchTimeSeconds );
		$this->completionPercentage = max( 0.0, $completionPercentage );
		$this->totalRevenue         = max( 0.0, $totalRevenue );
		$this->ctrPercentage        = max( 0.0, $ctrPercentage );
	}

	public function getWebinarId(): int { return $this->webinarId; }
	public function getTotalRegistrations(): int { return $this->totalRegistrations; }
	public function getLiveAttendees(): int { return $this->liveAttendees; }
	public function getAvgWatchTimeSeconds(): float { return $this->avgWatchTimeSeconds; }
	public function getCompletionPercentage(): float { return $this->completionPercentage; }
	public function getTotalRevenue(): float { return $this->totalRevenue; }
	public function getCtrPercentage(): float { return $this->ctrPercentage; }

	public function toArray(): array {
		return array(
			'webinar_id'            => $this->webinarId,
			'total_registrations'   => $this->totalRegistrations,
			'live_attendees'        => $this->liveAttendees,
			'avg_watch_time_sec'    => $this->avgWatchTimeSeconds,
			'completion_percentage' => $this->completionPercentage,
			'total_revenue'         => $this->totalRevenue,
			'ctr_percentage'        => $this->ctrPercentage,
		);
	}
}
