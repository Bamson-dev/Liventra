<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class BenchmarkResult {

	private $benchmarkId;
	private $testName;
	private $executionTimeMs;
	private $memoryBytes;

	public function __construct( string $benchmarkId, string $testName, float $executionTimeMs, int $memoryBytes ) {
		$this->benchmarkId     = $benchmarkId;
		$this->testName        = $testName;
		$this->executionTimeMs = $executionTimeMs;
		$this->memoryBytes     = $memoryBytes;
	}

	public function benchmarkId(): string { return $this->benchmarkId; }
	public function testName(): string { return $this->testName; }
	public function executionTimeMs(): float { return $this->executionTimeMs; }
	public function memoryBytes(): int { return $this->memoryBytes; }
}
