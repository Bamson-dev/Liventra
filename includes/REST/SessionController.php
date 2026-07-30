<?php
namespace Liventra\REST;

use Liventra\Container;
use Liventra\Contracts\Services\SessionServiceInterface;
use Liventra\Plugin;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Class SessionController
 * Handles Session Synchronization & Heartbeat REST Endpoints (PRD-004)
 * Consumes SessionServiceInterface via Container Dependency Injection.
 */
class SessionController extends ApiController {

	protected $rest_base = 'session';

	/**
	 * Session Service Contract
	 *
	 * @var SessionServiceInterface|null
	 */
	private $sessionService;

	/**
	 * Constructor with Container Dependency Resolution
	 *
	 * @param SessionServiceInterface|null $sessionService Optional injected service interface.
	 */
	public function __construct( SessionServiceInterface $sessionService = null ) {
		if ( null !== $sessionService ) {
			$this->sessionService = $sessionService;
		} else {
			// Resolve from DI Container
			$container = Container::getInstance();
			if ( ! isset( $container ) || ! method_exists( $container, 'get' ) ) {
				$this->sessionService = new \Liventra\Services\SessionService();
			} else {
				try {
					$this->sessionService = $container->get( SessionServiceInterface::class );
				} catch ( \Exception $e ) {
					$this->sessionService = new \Liventra\Services\SessionService();
				}
			}
		}
	}

	public function register_routes() {
		if ( ! function_exists( 'register_rest_route' ) ) {
			return;
		}

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/sync',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_session_sync' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Handle Session Sync Callback (PRD-004 Section 12)
	 */
	public function handle_session_sync( $request ) {
		$params = is_array( $request ) ? $request : $request->get_json_params();

		$start_time = isset( $params['scheduled_start'] ) ? (int) $params['scheduled_start'] : 0;
		$duration   = isset( $params['video_duration'] ) ? (int) $params['video_duration'] : 0;
		$token      = isset( $params['attendee_token'] ) ? (string) $params['attendee_token'] : 'anon';
		$client_sec = isset( $params['client_elapsed'] ) ? (int) $params['client_elapsed'] : 0;

		if ( ! $start_time || ! $duration ) {
			return $this->error_response( 'Missing required parameters (scheduled_start, video_duration)', 'invalid_params', 400 );
		}

		// Delegate state math strictly to SessionServiceInterface
		$sync_data = $this->sessionService->synchronizeAttendee( $token, $start_time, $duration, $client_sec );

		/** @var \Liventra\Modules\Timeline\TimelineModule $timeline_module */
		$timeline_module = Plugin::instance()->get_module( 'timeline' );
		$all_events      = isset( $params['timeline_events'] ) && is_array( $params['timeline_events'] ) ? $params['timeline_events'] : array();
		$last_synced     = isset( $params['last_synced_offset'] ) ? (int) $params['last_synced_offset'] : 0;

		$triggered_events = $timeline_module->get_triggered_events( $all_events, $sync_data['elapsed_seconds'], $last_synced );

		$response_payload = array_merge(
			$sync_data,
			array(
				'events' => $triggered_events,
			)
		);

		return $this->success_response( $response_payload );
	}
}
