<?php
namespace Liventra\Contracts\Services;

use Liventra\Entities\BrandingProfile;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

interface WhiteLabelServiceInterface {
	public function configureBranding( string $orgId, array $data ): BrandingProfile;
}
