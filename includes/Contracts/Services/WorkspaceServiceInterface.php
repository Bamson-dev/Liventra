<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\Workspace;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface WorkspaceServiceInterface {
	public function createWorkspace( string $orgId, string $name ): Workspace;
}
