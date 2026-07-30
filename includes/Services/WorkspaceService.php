<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\WorkspaceServiceInterface;
use Liventra\Contracts\Repositories\EnterpriseRepositoryInterface;
use Liventra\Entities\Workspace;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class WorkspaceService implements WorkspaceServiceInterface {

	private $repository;

	public function __construct( EnterpriseRepositoryInterface $repository = null ) {
		$this->repository = $repository;
	}

	public function createWorkspace( string $orgId, string $name ): Workspace {
		$wsId = 'ws_' . wp_generate_uuid4();
		$slug = strtolower( preg_replace( '/[^a-zA-Z0-9]/', '-', $name ) );
		$ws   = new Workspace( $wsId, $orgId, $name, $slug );

		if ( $this->repository ) {
			$this->repository->saveWorkspace( $ws );
		}

		EventBus::dispatch( 'workspace.created', array( 'workspace_id' => $wsId, 'org_id' => $orgId ) );
		return $ws;
	}
}
