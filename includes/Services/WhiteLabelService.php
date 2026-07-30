<?php
namespace Liventra\Services;

use Liventra\Contracts\Services\WhiteLabelServiceInterface;
use Liventra\Contracts\Repositories\EnterpriseRepositoryInterface;
use Liventra\Entities\BrandingProfile;
use Liventra\EventBus;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

class WhiteLabelService implements WhiteLabelServiceInterface {

	private $repository;

	public function __construct( EnterpriseRepositoryInterface $repository = null ) {
		$this->repository = $repository;
	}

	public function configureBranding( string $orgId, array $data ): BrandingProfile {
		$profId   = 'brand_' . wp_generate_uuid4();
		$branding = new BrandingProfile(
			$profId,
			$orgId,
			(string) ( $data['custom_domain'] ?? '' ),
			(string) ( $data['logo_url'] ?? '' ),
			(string) ( $data['primary_color'] ?? '#6366f1' )
		);

		if ( $this->repository ) {
			$this->repository->saveBranding( $branding );
		}

		EventBus::dispatch( 'branding.updated', array( 'org_id' => $orgId ) );
		return $branding;
	}
}
