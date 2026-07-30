<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class ViewerCountModel
 * Domain Entity calculating realistic dynamic viewer count curves (PRD-005 Section 6)
 */
class ViewerCountModel {

	private $mode; // 'fixed' | 'growth' | 'plateau' | 'wave' | 'launch_spike' | 'scripted'
	private $baseCount;
	private $maxCount;
	private $scriptedPoints;

	public function __construct( array $config = array() ) {
		$this->mode           = isset( $config['mode'] ) ? $config['mode'] : 'growth';
		$this->baseCount      = isset( $config['base_count'] ) ? max( 1, (int) $config['base_count'] ) : 85;
		$this->maxCount       = isset( $config['max_count'] ) ? max( $this->baseCount, (int) $config['max_count'] ) : 340;
		$this->scriptedPoints = isset( $config['scripted_points'] ) && is_array( $config['scripted_points'] ) ? $config['scripted_points'] : array();
	}

	public function calculateCount( int $currentOffset, int $totalDurationSeconds ): int {
		if ( $currentOffset <= 0 ) {
			return (int) ( $this->baseCount * 0.4 ); // Waiting room baseline
		}

		$progress = min( 1.0, $currentOffset / max( 1, $totalDurationSeconds ) );

		switch ( $this->mode ) {
			case 'fixed':
				return $this->baseCount;

			case 'scripted':
				return $this->interpolateScripted( $currentOffset );

			case 'launch_spike':
				// Fast growth in first 20%, plateau till 80%, decline at end
				if ( $progress < 0.2 ) {
					$factor = $progress / 0.2;
					return (int) ( $this->baseCount + ( $this->maxCount - $this->baseCount ) * sin( $factor * M_PI_2 ) );
				} elseif ( $progress < 0.8 ) {
					return $this->maxCount;
				} else {
					$declineFactor = ( 1.0 - $progress ) / 0.2;
					return (int) ( $this->baseCount + ( $this->maxCount - $this->baseCount ) * $declineFactor );
				}

			case 'wave':
				// Sine wave fluctuation around average
				$wave = sin( $progress * M_PI * 4 ) * ( ( $this->maxCount - $this->baseCount ) * 0.25 );
				$linearGrowth = $this->baseCount + ( $this->maxCount - $this->baseCount ) * $progress;
				return (int) max( $this->baseCount, $linearGrowth + $wave );

			case 'growth':
			default:
				// Organic growth curve
				$growth = sin( $progress * M_PI_2 );
				return (int) ( $this->baseCount + ( $this->maxCount - $this->baseCount ) * $growth );
		}
	}

	private function interpolateScripted( int $offset ): int {
		if ( empty( $this->scriptedPoints ) ) {
			return $this->baseCount;
		}

		ksort( $this->scriptedPoints );
		$last_val = $this->baseCount;

		foreach ( $this->scriptedPoints as $point_offset => $count ) {
			if ( $offset >= (int) $point_offset ) {
				$last_val = (int) $count;
			} else {
				break;
			}
		}

		return $last_val;
	}
}
