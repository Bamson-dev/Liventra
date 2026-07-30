<?php
namespace Liventra\Entities;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class Workspace {

	private $workspaceId;
	private $orgId;
	private $name;
	private $slug;

	public function __construct( string $workspaceId, string $orgId, string $name, string $slug ) {
		$this->workspaceId = $workspaceId;
		$this->orgId       = $orgId;
		$this->name        = $name;
		$this->slug        = $slug;
	}

	public function workspaceId(): string { return $this->workspaceId; }
	public function orgId(): string { return $this->orgId; }
	public function name(): string { return $this->name; }
	public function slug(): string { return $this->slug; }
}
