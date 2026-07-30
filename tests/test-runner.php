<?php
/**
 * Standalone Test Suite Runner for Liventra Core Architecture & Database Setup
 * Tests PSR-4 Autoloading, Plugin Container, EventBus, Migrator SQL Schemas, Session Time Anchor Math, and Timeline JIT Spooling.
 */

define( 'LIVENTRA_TEST_SUITE', true );

require_once __DIR__ . '/../includes/Autoloader.php';

\Liventra\Autoloader::register( __DIR__ . '/../includes/' );

class TestRunner {

	protected $passed = 0;
	protected $failed = 0;
	protected $tests  = array();

	public function assert( $condition, $message ) {
		if ( $condition ) {
			$this->passed++;
			echo "  ✅ PASS: {$message}\n";
		} else {
			$this->failed++;
			echo "  ❌ FAIL: {$message}\n";
		}
	}

	public function run() {
		echo "====================================================\n";
		echo "🚀 RUNNING LIVENTRA AUTOMATED ARCHITECTURE TEST SUITE\n";
		echo "====================================================\n\n";

		$this->test_autoloader();
		$this->test_plugin_container();
		$this->test_event_bus();
		$this->test_database_migrator_schema();
		$this->test_session_engine_time_math();
		$this->test_timeline_engine_jit_filtering();
		$this->test_session_rest_controller();

		echo "\n====================================================\n";
		echo "📊 TEST RESULTS: {$this->passed} Passed, {$this->failed} Failed\n";
		echo "====================================================\n";

		return 0 === $this->failed;
	}

	protected function test_autoloader() {
		echo "--- 1. Testing PSR-4 Autoloader ---\n";
		$exists = class_exists( '\\Liventra\\Plugin' );
		$this->assert( $exists, 'Autoloader successfully loaded \\Liventra\\Plugin class' );
	}

	protected function test_plugin_container() {
		echo "\n--- 2. Testing Plugin Singleton & Module Registry ---\n";
		$plugin = \Liventra\Plugin::instance();
		$this->assert( null !== $plugin, 'Plugin instance initialized successfully' );

		$modules = $plugin->get_modules();
		$this->assert( count( $modules ) === 10, 'Plugin registered all 10 PRD-002 core modules' );

		$expected_modules = array(
			'webinar', 'registration', 'session', 'timeline',
			'video', 'cta', 'chat', 'notification', 'analytics', 'cloud'
		);

		foreach ( $expected_modules as $mod_name ) {
			$mod = $plugin->get_module( $mod_name );
			$this->assert( null !== $mod, "Module '{$mod_name}' is correctly registered in container" );
		}
	}

	protected function test_event_bus() {
		echo "\n--- 3. Testing EventBus Pub/Sub Communication ---\n";
		\Liventra\EventBus::reset();

		$received = false;
		$data     = null;

		\Liventra\EventBus::on( 'test.event', function( $payload ) use ( &$received, &$data ) {
			$received = true;
			$data     = $payload;
		} );

		\Liventra\EventBus::dispatch( 'test.event', array( 'foo' => 'bar' ) );

		$this->assert( true === $received, 'EventBus successfully dispatched event to subscriber' );
		$this->assert( isset( $data['foo'] ) && 'bar' === $data['foo'], 'Subscriber received correct event payload' );
	}

	protected function test_database_migrator_schema() {
		echo "\n--- 4. Testing PRD-003 Database Migrator Schemas ---\n";
		$schemas = \Liventra\Database\Migrator::get_schema_sql( 'wp_test_' );

		$this->assert( count( $schemas ) === 5, 'Migrator generates 5 custom table schemas matching PRD-003' );

		$schema_str = implode( "\n", $schemas );
		$this->assert( strpos( $schema_str, 'wp_test_liventra_webinars' ) !== false, 'Schema contains wp_liventra_webinars' );
		$this->assert( strpos( $schema_str, 'webinar_id bigint(20) unsigned' ) !== false, 'webinars table has webinar_id PK' );
		$this->assert( strpos( $schema_str, 'video_source_type' ) !== false, 'webinars table has video_source_type' );

		$this->assert( strpos( $schema_str, 'wp_test_liventra_sessions' ) !== false, 'Schema contains wp_liventra_sessions' );
		$this->assert( strpos( $schema_str, 'session_id bigint(20) unsigned' ) !== false, 'sessions table has session_id PK' );

		$this->assert( strpos( $schema_str, 'wp_test_liventra_attendees' ) !== false, 'Schema contains wp_liventra_attendees' );
		$this->assert( strpos( $schema_str, 'attendee_id bigint(20) unsigned' ) !== false, 'attendees table has attendee_id PK' );

		$this->assert( strpos( $schema_str, 'wp_test_liventra_timeline_events' ) !== false, 'Schema contains wp_liventra_timeline_events' );
		$this->assert( strpos( $schema_str, 'trigger_second' ) !== false, 'timeline_events table has trigger_second' );

		$this->assert( strpos( $schema_str, 'wp_test_liventra_analytics_events' ) !== false, 'Schema contains wp_liventra_analytics_events' );
		$this->assert( strpos( $schema_str, 'analytics_id bigint(20) unsigned' ) !== false, 'analytics_events table has analytics_id PK' );
	}

	protected function test_session_engine_time_math() {
		echo "\n--- 5. Testing PRD-004 Session Engine (Container, Entity, Service & Math) ---\n";
		
		// Test Container Binding
		$container = \Liventra\Container::getInstance();
		$container->bind( \Liventra\Contracts\Services\SessionServiceInterface::class, \Liventra\Services\SessionService::class );

		$service = $container->get( \Liventra\Contracts\Services\SessionServiceInterface::class );
		$this->assert( $service instanceof \Liventra\Contracts\Services\SessionServiceInterface, 'Container resolved SessionServiceInterface implementation' );

		// Test Session Domain Entity
		$start_dt = new \DateTimeImmutable( '@1000' );
		$end_dt   = new \DateTimeImmutable( '@4600' );
		$session  = new \Liventra\Entities\Session( 1, 10, 'uuid-123', $start_dt, $end_dt, 'waiting' );

		$now_wait = new \DateTimeImmutable( '@500' );
		$this->assert( true === $session->isWaiting( $now_wait ), 'Session Entity identifies waiting state' );
		$this->assert( 500 === $session->remainingWaitingSeconds( $now_wait ), 'Session Entity calculates remaining waiting seconds' );

		$now_live = new \DateTimeImmutable( '@1500' );
		$this->assert( true === $session->isLive( $now_live, 3600 ), 'Session Entity identifies live state' );
		$this->assert( 500 === $session->elapsedSeconds( $now_live ), 'Session Entity calculates elapsed seconds' );

		$now_ended = new \DateTimeImmutable( '@5000' );
		$this->assert( true === $session->hasEnded( $now_ended, 3600 ), 'Session Entity identifies ended state' );

		// Test Session Service State Machine & Late Join Offset Math
		$start_time = 1000;
		$duration   = 3600;

		$wait_res = $service->evaluateSessionState( $start_time, $duration, 500 );
		$this->assert( 'waiting_room' === $wait_res['state'], 'SessionService returns waiting_room when now < start' );

		$live_res = $service->evaluateSessionState( $start_time, $duration, 1500 );
		$this->assert( 'live' === $live_res['state'], 'SessionService returns live when start <= now < start+duration' );
		$this->assert( 500 === $live_res['elapsed_seconds'], 'Late join calculates exact server offset (500s)' );

		// Test Drift Sync (Client at 400s vs Server at 500s -> Drift = 100s > 2.5s -> requires seek)
		$sync_res = $service->synchronizeAttendee( 'token123', $start_time, $duration, 400, 1500 );
		$this->assert( 100 === $sync_res['drift_seconds'], 'Drift seconds correctly calculated' );
		$this->assert( true === $sync_res['requires_seek'], 'Drift > 2.5s requires seek' );

		// Test PRD-005 Live Simulation Service
		echo "\n--- 5.1 Testing PRD-005 Live Simulation Engine ---\n";
		$container->bind( \Liventra\Contracts\Services\LiveSimulationServiceInterface::class, \Liventra\Services\LiveSimulationService::class );
		$simService = $container->get( \Liventra\Contracts\Services\LiveSimulationServiceInterface::class );
		$this->assert( $simService instanceof \Liventra\Contracts\Services\LiveSimulationServiceInterface, 'Container resolved LiveSimulationServiceInterface' );

		// Test Viewer Count Curves
		$count_mid = $simService->resolveViewerCount( array( 'mode' => 'growth', 'base_count' => 100, 'max_count' => 500 ), 1800, 3600 );
		$this->assert( $count_mid > 100 && $count_mid <= 500, 'ViewerCountModel computes growth curve' );

		// Test Poll Lifecycle Phases
		$poll_voting = $simService->resolvePollState( array( 'question' => 'Goal?', 'voting_duration_seconds' => 60, 'results_duration_seconds' => 30 ), 100, 120 );
		$this->assert( 'voting' === $poll_voting['phase'], 'Poll lifecycle identifies voting phase' );

		$poll_closed = $simService->resolvePollState( array( 'question' => 'Goal?', 'voting_duration_seconds' => 60, 'results_duration_seconds' => 30 ), 100, 300 );
		$this->assert( 'closed' === $poll_closed['phase'], 'Poll lifecycle identifies closed phase' );

		// Test PRD-006 Timeline Engine
		echo "\n--- 5.2 Testing PRD-006 Timeline Engine (Service, Entities, Dependency Graph & Registry) ---\n";
		$container->bind( \Liventra\Contracts\Repositories\TimelineRepositoryInterface::class, \Liventra\Database\Repositories\TimelineRepository::class );
		$container->bind( \Liventra\Contracts\Services\TimelineServiceInterface::class, \Liventra\Services\TimelineService::class );

		$timelineService = $container->get( \Liventra\Contracts\Services\TimelineServiceInterface::class );
		$this->assert( $timelineService instanceof \Liventra\Contracts\Services\TimelineServiceInterface, 'Container resolved TimelineServiceInterface' );

		// Test TimelineEvent Domain Entity
		$evt1 = new \Liventra\Entities\TimelineEvent( 'uuid-1', 1, 10, 'cta', 30, array( 'title' => 'Buy' ), true, true, 90 );
		$this->assert( 'uuid-1' === $evt1->uuid(), 'TimelineEvent encapsulates UUID' );
		$this->assert( 90 === $evt1->priorityWeight(), 'TimelineEvent calculates priority weight' );
		$this->assert( true === $evt1->isEligible( 45, 0 ), 'TimelineEvent evaluates JIT eligibility' );
		$this->assert( false === $evt1->isEligible( 15, 0 ), 'TimelineEvent rejects future offsets' );

		// Test EventRegistry Subsystem Handler Dispatch & Isolation
		\Liventra\Registries\EventRegistry::reset();
		$handlerFired = false;
		\Liventra\Registries\EventRegistry::register( 'cta', function( $evt ) use ( &$handlerFired ) {
			$handlerFired = true;
		} );
		$execRes = \Liventra\Registries\EventRegistry::dispatch( $evt1 );
		$this->assert( true === $handlerFired, 'EventRegistry dispatches registered handler' );
		$this->assert( true === $execRes->isSuccess(), 'EventExecution records successful execution' );

		// Test Dependency Validation & Circular Dependency Rejection
		$evtA = new \Liventra\Entities\TimelineEvent( 'uuid-A', 10, 1, 'poll_close', 60, array(), true, true, 50, 1, array( 'uuid-B' ) );
		$evtB = new \Liventra\Entities\TimelineEvent( 'uuid-B', 11, 1, 'poll_results', 70, array(), true, true, 50, 1, array( 'uuid-A' ) );

		$circularCaught = false;
		try {
			$timelineService->validateTimeline( array( $evtA, $evtB ) );
		} catch ( \InvalidArgumentException $e ) {
			$circularCaught = true;
		}
		$this->assert( true === $circularCaught, 'TimelineService detects and rejects circular dependencies' );

		// Test Reconnect Catch-Up State Restoration
		$restoreRes = $timelineService->restoreState( 1, 100, 42 );
		$this->assert( isset( $restoreRes['restored_persistent'] ), 'RestoreState produces catch-up payload for reconnected attendees' );

		// Test Immutable Timeline Publishing
		$pubVersion = $timelineService->publishTimeline( 1 );
		$this->assert( $pubVersion instanceof \Liventra\Entities\TimelineVersion, 'PublishTimeline generates immutable TimelineVersion' );

		// Test PRD-006 Phase 10 Pluggable Extension Framework
		echo "\n--- 5.3 Testing Pluggable Extension Framework & EventRegistry ---\n";
		$this->assert( \Liventra\Events\EventTypeRegistry::isValid( \Liventra\Events\EventTypeRegistry::CTA_SHOW ), 'EventTypeRegistry validates standard cta.show event' );
		$this->assert( \Liventra\Events\EventTypeRegistry::isValid( \Liventra\Events\EventTypeRegistry::POLL_OPEN ), 'EventTypeRegistry validates standard poll.open event' );

		// Test ExtensionManager Dependency Validation & Conflict Detection
		\Liventra\Extensions\ExtensionManager::reset();
		$registered = \Liventra\Extensions\ExtensionManager::registerExtension( new \stdClass(), array( 'name' => 'CoreCRM', 'min_php_version' => '7.4' ) );
		$this->assert( true === $registered, 'ExtensionManager validates and registers valid extension' );

		$conflictCaught = false;
		try {
			\Liventra\Extensions\ExtensionManager::registerExtension( new \stdClass(), array( 'name' => 'PluginB', 'conflicts' => array( 'CoreCRM' ) ) );
		} catch ( \RuntimeException $e ) {
			$conflictCaught = true;
		}
		$this->assert( true === $conflictCaught, 'ExtensionManager detects and blocks conflicting modules' );

		// Test Pluggable EventHandlerInterface Registration & Priority Execution
		$mockHandler = new class implements \Liventra\Contracts\Events\EventHandlerInterface {
			public function supports( string $t ): bool { return 'cta.show' === $t; }
			public function priority(): int { return 100; }
			public function handle( \Liventra\Entities\TimelineEvent $e ): \Liventra\Events\EventResult {
				return \Liventra\Events\EventResult::success( $e->uuid() );
			}
			public function metadata(): \Liventra\Events\HandlerMetadata {
				return new \Liventra\Events\HandlerMetadata( 'MockCTAHandler', array( 'cta.show' ), '1.0.0', 'Tester', 100 );
			}
		};

		\Liventra\Registries\EventRegistry::registerHandler( $mockHandler );

		$testEvt = new \Liventra\Entities\TimelineEvent( 'uuid-mock-1', 99, 1, 'cta.show', 10 );
		$execRes = \Liventra\Registries\EventRegistry::dispatch( $testEvt );
		$this->assert( true === $execRes->isSuccess(), 'EventRegistry dispatches pluggable EventHandlerInterface' );

		// Test Failure Isolation (Failing third-party handler does not break system)
		$failingHandler = new class implements \Liventra\Contracts\Events\EventHandlerInterface {
			public function supports( string $t ): bool { return 'crash.test' === $t; }
			public function priority(): int { return 50; }
			public function handle( \Liventra\Entities\TimelineEvent $e ): \Liventra\Events\EventResult {
				throw new \RuntimeException( 'Third party handler crash!' );
			}
			public function metadata(): \Liventra\Events\HandlerMetadata {
				return new \Liventra\Events\HandlerMetadata( 'FailingHandler', array( 'crash.test' ), '1.0.0', 'ThirdParty', 50 );
			}
		};
		\Liventra\Registries\EventRegistry::registerHandler( $failingHandler );

		$crashEvt = new \Liventra\Entities\TimelineEvent( 'uuid-crash-1', 100, 1, 'crash.test', 10 );
		$crashRes = \Liventra\Registries\EventRegistry::dispatch( $crashEvt );
		$this->assert( false === $crashRes->isSuccess(), 'EventRegistry isolates third-party handler failure without throwing uncaught exception' );

		$metrics = \Liventra\Extensions\ExtensionDiagnostics::getMetrics();
		$this->assert( $metrics['total_invocations'] > 0, 'ExtensionDiagnostics tracks execution metrics' );

		// Test PRD-007 Video Engine (Service, Entities, Providers, Resolver & Synchronization)
		echo "\n--- 5.4 Testing PRD-007 Video Engine ---\n";
		$container->bind( \Liventra\Contracts\Services\VideoServiceInterface::class, \Liventra\Services\VideoService::class );
		$videoService = $container->get( \Liventra\Contracts\Services\VideoServiceInterface::class );
		$this->assert( $videoService instanceof \Liventra\Contracts\Services\VideoServiceInterface, 'Container resolved VideoServiceInterface' );

		// Test VideoAsset Domain Entity
		$asset = new \Liventra\Entities\VideoAsset( 'asset-uuid-1', 1, 'mp4', 'https://example.com/video.mp4', 3600 );
		$this->assert( 'asset-uuid-1' === $asset->uuid(), 'VideoAsset encapsulates UUID' );
		$this->assert( 3600 === $asset->duration(), 'VideoAsset encapsulates duration' );

		// Test Provider Resolver & Provider Adapters
		$resolver = new \Liventra\Resolvers\ProviderResolver();
		$mp4Prov  = $resolver->resolve( 'https://example.com/video.mp4' );
		$this->assert( 'mp4' === $mp4Prov->getProviderName(), 'ProviderResolver identifies MP4 source' );

		$hlsProv = $resolver->resolve( 'https://example.com/stream.m3u8' );
		$this->assert( 'hls' === $hlsProv->getProviderName(), 'ProviderResolver identifies HLS source' );

		$bunnyProv = $resolver->resolve( 'https://b-cdn.net/video.mp4' );
		$this->assert( 'bunny' === $bunnyProv->getProviderName(), 'ProviderResolver identifies Bunny Stream source' );

		$muxProv = $resolver->resolve( 'https://stream.mux.com/123.m3u8' );
		$this->assert( 'mux' === $muxProv->getProviderName(), 'ProviderResolver identifies Mux source' );

		// Test Signed Media URLs (PRD-007 Part 12 Security)
		$signedUrl = $mp4Prov->getSignedUrl( $asset );
		$this->assert( false !== strpos( $signedUrl, 'token=' ), 'VideoProvider generates signed URL with security token' );

		// Test Authoritative Synchronization Thresholds (PRD-007 Part 5)
		$syncIgnore = $videoService->synchronize( 100.2, 100.0 );
		$this->assert( 'ignore' === $syncIgnore['action'], 'Sync ignores drift <= 500ms' );

		$syncSoft = $videoService->synchronize( 101.2, 100.0 );
		$this->assert( 'soft_correction' === $syncSoft['action'], 'Sync applies soft correction for drift > 500ms' );

		$syncForce = $videoService->synchronize( 110.0, 100.0 );
		$this->assert( 'force_seek' === $syncForce['action'], 'Sync forces seek for drift > 2.5s' );
		$this->assert( true === $syncForce['requires_seek'], 'Force seek flag correctly set for drift > 2.5s' );

		// Test PRD-008 Registration Engine
		echo "\n--- 5.5 Testing PRD-008 Registration & Identity Engine ---\n";
		$container->bind( \Liventra\Contracts\Repositories\RegistrationRepositoryInterface::class, \Liventra\Database\Repositories\RegistrationRepository::class );
		$container->bind( \Liventra\Contracts\Services\RegistrationServiceInterface::class, \Liventra\Services\RegistrationService::class );

		$regService = $container->get( \Liventra\Contracts\Services\RegistrationServiceInterface::class );
		$this->assert( $regService instanceof \Liventra\Contracts\Services\RegistrationServiceInterface, 'Container resolved RegistrationServiceInterface' );

		// Test Attendee Registration & Duplicate Handling
		$reg1 = $regService->register( 1, array( 'email' => 'joshua@example.com', 'first_name' => 'Joshua', 'last_name' => 'Okpara' ) );
		$this->assert( 'joshua@example.com' === $reg1->getEmail(), 'RegistrationService registers attendee email' );
		$this->assert( true === $reg1->isConfirmed(), 'New registration defaults to confirmed state' );

		$regDuplicate = $regService->register( 1, array( 'email' => 'joshua@example.com', 'first_name' => 'Joshua' ) );
		$this->assert( $reg1->getRegistrationId() === $regDuplicate->getRegistrationId(), 'RegistrationService reuses existing duplicate registration' );

		// Test Secure Signed Join Link Generation (PRD-008 Part 6 HMAC)
		$joinLink = $regService->generateJoinLink( $reg1 );
		$this->assert( false !== strpos( $joinLink, '?token=' ), 'GenerateJoinLink generates signed join link with token' );

		// Test Authorization & Token Verification
		$tokenStr = parse_url( $joinLink, PHP_URL_QUERY );
		parse_str( $tokenStr, $queryVars );
		$tokenValue = $queryVars['token'] ?? '';
		$this->assert( true === $regService->authorizeJoin( $tokenValue ), 'AuthorizeJoin verifies cryptographic join token' );

		// Test Waiting Room Flow (PRD-008 Part 7)
		$waitingState = $regService->enterWaitingRoom( 1, $reg1->getAttendeeId() );
		$this->assert( $waitingState instanceof \Liventra\Entities\WaitingRoomState, 'EnterWaitingRoom returns WaitingRoomState entity' );
		$this->assert( 'waiting' === $waitingState->getStatus(), 'WaitingRoomState status is waiting' );

		// Test Reconnect Identity Restoration (PRD-008 Part 11)
		$reconnectData = $regService->reconnect( $tokenValue );
		$this->assert( 'reconnected' === $reconnectData['status'], 'Reconnect restores attendee session identity' );

		// Test PRD-009 CTA Engine
		echo "\n--- 5.6 Testing PRD-009 CTA & Offer Engine ---\n";
		$container->bind( \Liventra\Contracts\Repositories\CTARepositoryInterface::class, \Liventra\Database\Repositories\CTARepository::class );
		$container->bind( \Liventra\Contracts\Services\CTAServiceInterface::class, \Liventra\Services\CTAService::class );

		$ctaService = $container->get( \Liventra\Contracts\Services\CTAServiceInterface::class );
		$this->assert( $ctaService instanceof \Liventra\Contracts\Services\CTAServiceInterface, 'Container resolved CTAServiceInterface' );

		// Test CTA Domain Entity & Dynamic Personalization (PRD-009 Part 8)
		$ctaRaw = $ctaService->createCTA( array(
			'webinar_id'     => 1,
			'title'          => 'Hi {first_name}, Special Discount!',
			'button_text'    => 'Claim Offer',
			'destination_url'=> 'https://example.com/checkout',
			'trigger_second' => 300,
			'persistence'    => true,
		) );
		$this->assert( 300 === $ctaRaw->triggerSecond(), 'CTA encapsulates triggerSecond' );

		$personalizedCta = $ctaRaw->personalize( array( 'first_name' => 'Matthew' ) );
		$this->assert( false !== strpos( $personalizedCta->title(), 'Matthew' ), 'CTA personalizes title with attendee first_name' );

		// Test Server-Side Eligibility Evaluation (PRD-009 Part 6)
		$ineligible = $ctaService->resolveEligibility( $ctaRaw, 100 );
		$this->assert( false === $ineligible, 'CTA is ineligible before triggerSecond offset' );

		$eligible = $ctaService->resolveEligibility( $ctaRaw, 350 );
		$this->assert( true === $eligible, 'CTA becomes eligible at or after triggerSecond offset' );

		// Test Reconnect Persistent State Restoration (PRD-009 Part 7)
		$restoredCTAs = $ctaService->restoreState( 1, 350, array( 'first_name' => 'Matthew' ) );
		$this->assert( count( $restoredCTAs ) > 0, 'RestoreState retrieves active persistent CTAs for reconnected attendee' );

		// Test Interaction Tracking & Conversion Attribution (PRD-009 Part 10 & 11)
		$interaction = $ctaService->trackInteraction( $ctaRaw->uuid(), 42, \Liventra\Entities\CTAInteraction::TYPE_CLICK );
		$this->assert( 'click' === $interaction->getType(), 'TrackInteraction logs click interaction' );

		$conversion = $ctaService->trackConversion( $ctaRaw->uuid(), 42, array( 'amount' => 297, 'currency' => 'USD' ) );
		$this->assert( 'conversion' === $conversion->getType(), 'TrackConversion records transaction attribution' );

		// Test PRD-010 Live Chat Engine
		echo "\n--- 5.7 Testing PRD-010 Live Chat Engine ---\n";
		$container->bind( \Liventra\Contracts\Repositories\ChatRepositoryInterface::class, \Liventra\Database\Repositories\ChatRepository::class );
		$container->bind( \Liventra\Contracts\Services\ChatServiceInterface::class, \Liventra\Services\ChatService::class );

		$chatService = $container->get( \Liventra\Contracts\Services\ChatServiceInterface::class );
		$this->assert( $chatService instanceof \Liventra\Contracts\Services\ChatServiceInterface, 'Container resolved ChatServiceInterface' );

		// Test ChatMessage Domain Entity & XSS Sanitization (PRD-010 Part 13 Security)
		$xssMsg = new \Liventra\Entities\ChatMessage( 'uuid-xss-1', 1, 'Hacker', '<script>alert("XSS")</script>Hello {first_name}' );
		$this->assert( false === strpos( $xssMsg->message(), '<script>' ), 'ChatMessage entity sanitizes XSS script tags' );

		// Test Message Personalization (PRD-010 Part 6)
		$personalizedMsg = $xssMsg->personalize( array( 'first_name' => 'Sarah' ) );
		$this->assert( false !== strpos( $personalizedMsg->message(), 'Sarah' ), 'ChatMessage personalizes {first_name} placeholder' );

		// Test Pinned Message Lifecycle (PRD-010 Part 8)
		$chatService->createMessage( array( 'uuid' => 'uuid-pin-1', 'webinar_id' => 1, 'sender' => 'Host', 'message' => 'Important Announcement' ) );
		$pinRes = $chatService->pinMessage( 'uuid-pin-1' );
		$this->assert( true === $pinRes, 'PinMessage pins target announcement' );

		// Test Emoji Reaction Tracking (PRD-010 Part 9)
		$reaction = $chatService->trackReaction( 'uuid-pin-1', 42, '🔥' );
		$this->assert( '🔥' === $reaction->getEmoji(), 'TrackReaction records emoji reaction' );

		// Test Moderation (PRD-010 Part 10)
		$modRes = $chatService->moderate( 'uuid-pin-1', 'hide' );
		$this->assert( true === $modRes, 'Moderate executes hide action' );

		// Test Reconnect State Restoration (PRD-010 Part 11)
		$chatRestore = $chatService->restoreState( 1, 100, array( 'first_name' => 'Sarah' ) );
		$this->assert( isset( $chatRestore['messages'] ), 'RestoreState restores visible chat messages' );

		// Test PRD-011 Analytics & Event Intelligence Engine
		echo "\n--- 5.8 Testing PRD-011 Analytics & Event Intelligence Engine ---\n";
		$container->bind( \Liventra\Contracts\Repositories\AnalyticsRepositoryInterface::class, \Liventra\Database\Repositories\AnalyticsRepository::class );
		$container->bind( \Liventra\Contracts\Services\AnalyticsServiceInterface::class, \Liventra\Services\AnalyticsService::class );

		$analyticsService = $container->get( \Liventra\Contracts\Services\AnalyticsServiceInterface::class );
		$this->assert( $analyticsService instanceof \Liventra\Contracts\Services\AnalyticsServiceInterface, 'Container resolved AnalyticsServiceInterface' );

		// Test Event Collection & Normalization (PRD-011 Part 5)
		$evt1 = $analyticsService->track( 'video.started', 1, 42, array( 'duration' => 3600 ) );
		$this->assert( 'video.started' === $evt1->eventType(), 'AnalyticsService tracks normalized analytics event' );

		// Test Revenue Conversion Attribution (PRD-011 Part 10)
		$convEvt = $analyticsService->recordConversion( 1, 42, 197.0, array( 'campaign' => 'facebook_ad' ) );
		$this->assert( 197.0 === $convEvt->payload()['revenue'], 'RecordConversion tracks revenue attribution' );

		// Test Attendee Journey Timeline Builder
		$timeline = $analyticsService->buildTimeline( 1, 42 );
		$this->assert( count( $timeline ) >= 2, 'BuildTimeline retrieves chronological attendee journey' );

		// Test Dashboard Metrics Aggregation (PRD-011 Part 11)
		$dashMetrics = $analyticsService->getDashboardMetrics( 1 );
		$this->assert( isset( $dashMetrics['summary'] ), 'GetDashboardMetrics returns summary engagement snapshot' );
		$this->assert( isset( $dashMetrics['funnel'] ), 'GetDashboardMetrics includes funnel conversion step counts' );

		// Test Dataset Export (PRD-011 Part 12 CSV & JSON)
		$csvExport = $analyticsService->export( 1, 'csv' );
		$this->assert( false !== strpos( $csvExport, 'UUID,WebinarID' ), 'Export generates valid CSV format dataset' );

		$jsonExport = $analyticsService->export( 1, 'json' );
		$this->assert( false !== strpos( $jsonExport, 'video.started' ), 'Export generates valid JSON format dataset' );

		// Test PRD-012 Admin Studio & Webinar Builder Engine
		echo "\n--- 5.9 Testing PRD-012 Admin Studio & Webinar Builder Engine ---\n";
		$container->bind( \Liventra\Contracts\Services\AdminStudioServiceInterface::class, \Liventra\Services\AdminStudioService::class );

		$studioService = $container->get( \Liventra\Contracts\Services\AdminStudioServiceInterface::class );
		$this->assert( $studioService instanceof \Liventra\Contracts\Services\AdminStudioServiceInterface, 'Container resolved AdminStudioServiceInterface' );

		// Test Webinar Creation & Duplication (PRD-012 Part 3)
		$webinar1 = $studioService->createWebinar( array( 'title' => 'Masterclass Webinar 2026', 'video_asset_id' => 99 ) );
		$this->assert( 'Masterclass Webinar 2026' === $webinar1['title'], 'CreateWebinar generates webinar draft' );

		$duplicated = $studioService->duplicateWebinar( $webinar1['webinar_id'] );
		$this->assert( false !== strpos( $duplicated['title'], '(Copy)' ), 'DuplicateWebinar clones existing webinar configuration' );

		// Test Pre-Publish Validation Pipeline (PRD-012 Part 10)
		$valErrorsClean = $studioService->validateConfiguration( $webinar1['webinar_id'] );
		$this->assert( empty( $valErrorsClean ), 'ValidateConfiguration passes valid webinar setup' );

		$webinarEmpty = $studioService->createWebinar( array( 'title' => 'Empty Setup' ) );
		$valErrorsMissing = $studioService->validateConfiguration( $webinarEmpty['webinar_id'] );
		$this->assert( ! empty( $valErrorsMissing ), 'ValidateConfiguration detects missing video asset binding' );

		// Test Immutable Versioning & Publishing (PRD-012 Part 9)
		$pubRes = $studioService->publishWebinar( $webinar1['webinar_id'] );
		$this->assert( true === $pubRes['success'], 'PublishWebinar creates published immutable version' );
		$this->assert( 1 === $pubRes['version'], 'PublishWebinar assigns version number 1' );

		// Test Isolated Preview Mode (PRD-012 Part 11)
		$previewRes = $studioService->previewWebinar( $webinar1['webinar_id'], 600 );
		$this->assert( 'preview_isolated' === $previewRes['mode'], 'PreviewWebinar launches isolated preview session' );
		$this->assert( 600 === $previewRes['offset_seconds'], 'PreviewWebinar respects offset jump seconds' );

		// Test Studio Executive Dashboard Aggregation (PRD-012 Part 2)
		$studioDash = $studioService->getDashboard();
		$this->assert( isset( $studioDash['total_webinars'] ), 'GetDashboard aggregates total webinars' );
		$this->assert( isset( $studioDash['active_attendees'] ), 'GetDashboard aggregates active live attendees from AnalyticsEngine' );

		// Test PRD-013 Security, Authorization & Platform Hardening Platform
		echo "\n--- 5.10 Testing PRD-013 Security, Authorization & Platform Hardening ---\n";
		$container->bind( \Liventra\Contracts\Repositories\SecurityRepositoryInterface::class, \Liventra\Database\Repositories\SecurityRepository::class );
		$container->bind( \Liventra\Contracts\Services\SecurityServiceInterface::class, \Liventra\Services\SecurityService::class );
		$container->bind( \Liventra\Contracts\Services\AuthorizationServiceInterface::class, \Liventra\Services\AuthorizationService::class );
		$container->bind( \Liventra\Contracts\Services\AuditServiceInterface::class, \Liventra\Services\AuditService::class );
		$container->bind( \Liventra\Contracts\Services\SecretManagerInterface::class, \Liventra\Services\SecretManager::class );

		$securityService = $container->get( \Liventra\Contracts\Services\SecurityServiceInterface::class );
		$authService     = $container->get( \Liventra\Contracts\Services\AuthorizationServiceInterface::class );
		$auditService    = $container->get( \Liventra\Contracts\Services\AuditServiceInterface::class );
		$secretManager   = $container->get( \Liventra\Contracts\Services\SecretManagerInterface::class );

		$this->assert( $securityService instanceof \Liventra\Contracts\Services\SecurityServiceInterface, 'Container resolved SecurityServiceInterface' );
		$this->assert( $authService instanceof \Liventra\Contracts\Services\AuthorizationServiceInterface, 'Container resolved AuthorizationServiceInterface' );
		$this->assert( $auditService instanceof \Liventra\Contracts\Services\AuditServiceInterface, 'Container resolved AuditServiceInterface' );
		$this->assert( $secretManager instanceof \Liventra\Contracts\Services\SecretManagerInterface, 'Container resolved SecretManagerInterface' );

		// Test Cryptographic Token Issuance & Verification (PRD-013 Part 4 & 7)
		$signedTokenObj = $securityService->issueSignedToken( 'user_88', array( 'scope' => 'webinar_access' ) );
		$tokenStr       = $signedTokenObj->toString();
		$verifiedToken  = $securityService->verifySignedToken( $tokenStr );
		$this->assert( null !== $verifiedToken, 'VerifySignedToken validates HMAC-SHA256 signature' );
		$this->assert( 'user_88' === $verifiedToken->subjectId(), 'Verified token restores subject ID' );

		// Test Signed Resource URLs
		$signedUrl = $securityService->issueSignedUrl( 'https://example.com/video.mp4', array( 'webinar_id' => 1 ) );
		$this->assert( true === $securityService->verifySignedUrl( $signedUrl ), 'VerifySignedUrl validates query parameter signature' );

		// Test Token Bucket Rate Limiting (PRD-013 Part 9)
		$rateLimitKey = 'ip_192.168.1.10';
		$rl1 = $securityService->checkRateLimit( $rateLimitKey, 2, 1 );
		$rl2 = $securityService->checkRateLimit( $rateLimitKey, 2, 1 );
		$rl3 = $securityService->checkRateLimit( $rateLimitKey, 2, 1 );
		$this->assert( true === $rl1 && true === $rl2, 'RateLimit permits requests within capacity' );
		$this->assert( false === $rl3, 'RateLimit blocks requests exceeding bucket capacity' );

		// Test RBAC Role Capability Resolution (PRD-013 Part 5)
		$this->assert( true === $authService->hasCapability( 'super_admin', 'delete_webinar' ), 'Super Admin possesses delete_webinar capability' );
		$this->assert( false === $authService->hasCapability( 'viewer', 'delete_webinar' ), 'Viewer lacks delete_webinar capability' );

		// Test Immutable Append-Only Audit Logging (PRD-013 Part 11)
		$auditRecord = $auditService->recordAudit( 'publish_webinar', 1, 'webinar_101' );
		$logs        = $auditService->getAuditLogs();
		$this->assert( count( $logs ) > 0, 'AuditRecord appended to security audit trail' );

		// Test Encrypted Secret Manager & Key Rotation (PRD-013 Part 6)
		$sec = $secretManager->storeSecret( 'stripe_api_key', 'sk_live_secret123' );
		$this->assert( 1 === $sec->getVersion(), 'SecretManager stores encrypted secret at version 1' );

		$decrypted = $secretManager->retrieveSecret( 'stripe_api_key' );
		$this->assert( 'sk_live_secret123' === $decrypted, 'SecretManager decrypts stored secret plaintext' );

		$rotated = $secretManager->rotateSecret( 'stripe_api_key', 'sk_live_secret999' );
		$this->assert( 2 === $rotated->getVersion(), 'SecretManager increments version on key rotation' );

		// Test REST Security Middleware (PRD-013 Part 14)
		$middleware = new \Liventra\Middleware\SecurityMiddleware( $securityService, $authService, $auditService );
		$allowedReq = $middleware->handle( array( 'ip' => '10.0.0.1', 'user_id' => 1, 'capability' => 'publish_webinar' ) );
		$this->assert( true === $allowedReq, 'SecurityMiddleware authorizes valid request payload' );

		// Test PRD-014 Public API & Integration Platform
		echo "\n--- 5.11 Testing PRD-014 Public API & Integration Platform ---\n";
		$container->bind( \Liventra\Contracts\Repositories\ApiRepositoryInterface::class, \Liventra\Database\Repositories\ApiRepository::class );
		$container->bind( \Liventra\Contracts\Services\ApiGatewayInterface::class, \Liventra\Services\ApiGatewayService::class );
		$container->bind( \Liventra\Contracts\Services\WebhookServiceInterface::class, \Liventra\Services\WebhookService::class );
		$container->bind( \Liventra\Contracts\Services\ApiKeyServiceInterface::class, \Liventra\Services\ApiKeyService::class );
		$container->bind( \Liventra\Contracts\Services\OpenApiServiceInterface::class, \Liventra\Services\OpenApiService::class );

		$gatewayService = $container->get( \Liventra\Contracts\Services\ApiGatewayInterface::class );
		$webhookService = $container->get( \Liventra\Contracts\Services\WebhookServiceInterface::class );
		$apiKeyService  = $container->get( \Liventra\Contracts\Services\ApiKeyServiceInterface::class );
		$openApiService = $container->get( \Liventra\Contracts\Services\OpenApiServiceInterface::class );

		$this->assert( $gatewayService instanceof \Liventra\Contracts\Services\ApiGatewayInterface, 'Container resolved ApiGatewayInterface' );
		$this->assert( $webhookService instanceof \Liventra\Contracts\Services\WebhookServiceInterface, 'Container resolved WebhookServiceInterface' );
		$this->assert( $apiKeyService instanceof \Liventra\Contracts\Services\ApiKeyServiceInterface, 'Container resolved ApiKeyServiceInterface' );
		$this->assert( $openApiService instanceof \Liventra\Contracts\Services\OpenApiServiceInterface, 'Container resolved OpenApiServiceInterface' );

		// Test API Key Issuance & Validation (PRD-014 Part 6)
		$issuedKey = $apiKeyService->issueApiKey( 1, 'Zapier Integration Key' );
		$this->assert( 0 === strpos( $issuedKey->secretKey(), 'live_sk_' ), 'IssueApiKey generates prefixed secret key' );

		$validatedKey = $apiKeyService->validateApiKey( $issuedKey->secretKey() );
		$this->assert( null !== $validatedKey, 'ValidateApiKey verifies active API key' );

		// Test Webhook Registration & HMAC Signatures (PRD-014 Part 7)
		$whSub = $webhookService->registerWebhook( 'https://hooks.zapier.com/catch/123', array( 'registration.created' ), 'whsec_999' );
		$this->assert( 'https://hooks.zapier.com/catch/123' === $whSub->targetUrl(), 'RegisterWebhook creates active webhook subscription' );

		$whSig = $webhookService->generateWebhookSignature( '{"event":"registration.created"}', 'whsec_999' );
		$this->assert( true === $webhookService->verifyWebhookSignature( '{"event":"registration.created"}', $whSig, 'whsec_999' ), 'VerifyWebhookSignature validates HMAC payload signature' );

		$deliveries = $webhookService->triggerEvent( 'registration.created', array( 'attendee_id' => 88 ) );
		$this->assert( count( $deliveries ) > 0, 'TriggerEvent dispatches webhook delivery notification' );

		// Test Request Idempotency Validation (PRD-014 Part 8)
		$apiReq   = new \Liventra\Entities\ApiRequest( 'req_1', '/v1/registrations', 'POST', array( 'email' => 'test@example.com' ), array( 'X-Idempotency-Key' => 'idem_key_777' ) );
		$apiResp1 = $gatewayService->dispatch( $apiReq );
		$this->assert( 200 === $apiResp1->status(), 'ApiGateway dispatches v1 REST request successfully' );

		$cachedIdem = $gatewayService->validateIdempotency( 'idem_key_777', md5( '/v1/registrations|' . (string) wp_json_encode( array( 'email' => 'test@example.com' ) ) ) );
		$this->assert( null !== $cachedIdem, 'ValidateIdempotency returns cached response for duplicate mutation' );

		// Test OpenAPI 3.1 Specification Generator (PRD-014 Part 11)
		$openApiJson = $openApiService->generateOpenApi( 'json' );
		$this->assert( false !== strpos( $openApiJson, '3.1.0' ), 'GenerateOpenApi produces valid OpenAPI 3.1 JSON specification' );

		// Test PRD-015 Notification & Messaging Platform
		echo "\n--- 5.12 Testing PRD-015 Notification & Messaging Platform ---\n";
		$container->bind( \Liventra\Contracts\Repositories\NotificationRepositoryInterface::class, \Liventra\Database\Repositories\NotificationRepository::class );
		$container->bind( \Liventra\Contracts\Services\NotificationServiceInterface::class, \Liventra\Services\NotificationService::class );
		$container->bind( \Liventra\Contracts\Services\EmailServiceInterface::class, \Liventra\Services\EmailService::class );
		$container->bind( \Liventra\Contracts\Services\SmsServiceInterface::class, \Liventra\Services\SmsService::class );
		$container->bind( \Liventra\Contracts\Services\WhatsAppServiceInterface::class, \Liventra\Services\WhatsAppService::class );
		$container->bind( \Liventra\Contracts\Services\PushNotificationServiceInterface::class, \Liventra\Services\PushNotificationService::class );
		$container->bind( \Liventra\Contracts\Services\InAppNotificationServiceInterface::class, \Liventra\Services\InAppNotificationService::class );
		$container->bind( \Liventra\Contracts\Services\NotificationTemplateServiceInterface::class, \Liventra\Services\NotificationTemplateService::class );
		$container->bind( \Liventra\Contracts\Services\NotificationPreferenceServiceInterface::class, \Liventra\Services\NotificationPreferenceService::class );

		$notifService    = $container->get( \Liventra\Contracts\Services\NotificationServiceInterface::class );
		$emailService    = $container->get( \Liventra\Contracts\Services\EmailServiceInterface::class );
		$smsService      = $container->get( \Liventra\Contracts\Services\SmsServiceInterface::class );
		$templateService = $container->get( \Liventra\Contracts\Services\NotificationTemplateServiceInterface::class );
		$prefService     = $container->get( \Liventra\Contracts\Services\NotificationPreferenceServiceInterface::class );

		$this->assert( $notifService instanceof \Liventra\Contracts\Services\NotificationServiceInterface, 'Container resolved NotificationServiceInterface' );
		$this->assert( $emailService instanceof \Liventra\Contracts\Services\EmailServiceInterface, 'Container resolved EmailServiceInterface' );
		$this->assert( $smsService instanceof \Liventra\Contracts\Services\SmsServiceInterface, 'Container resolved SmsServiceInterface' );
		$this->assert( $templateService instanceof \Liventra\Contracts\Services\NotificationTemplateServiceInterface, 'Container resolved NotificationTemplateServiceInterface' );
		$this->assert( $prefService instanceof \Liventra\Contracts\Services\NotificationPreferenceServiceInterface, 'Container resolved NotificationPreferenceServiceInterface' );

		// Test Template Rendering & Variable Substitution (PRD-015 Part 6)
		$rendered = $notifService->renderTemplate( 'registration_welcome', array( 'first_name' => 'Sarah', 'webinar_name' => 'Evergreen Launch', 'join_url' => 'https://example.com/join' ) );
		$this->assert( false !== strpos( $rendered['subject'], 'Sarah' ), 'RenderTemplate substitutes {{first_name}} in subject' );
		$this->assert( false !== strpos( $rendered['body'], 'Evergreen Launch' ), 'RenderTemplate substitutes {{webinar_name}} in body' );

		// Test Notification Dispatching & Delivery Receipts (PRD-015 Part 4)
		$notifObj = new \Liventra\Entities\Notification( 'notif_unit_1', 42, 'email', $rendered['subject'], $rendered['body'] );
		$receipt  = $notifService->send( $notifObj );
		$this->assert( 'delivered' === $receipt->status(), 'Send returns valid delivered receipt status' );

		// Test Queuing & Scheduling (PRD-015 Part 9)
		$queued   = $notifService->queue( $notifObj );
		$this->assert( true === $queued, 'Queue enqueues notification for async processing' );

		$scheduled = $notifService->schedule( $notifObj, time() + 3600 );
		$this->assert( true === $scheduled, 'Schedule sets future delivery timestamp' );

		// Test Retries & Failover Policies (PRD-015 Part 10)
		$retried = $notifService->retry( 'notif_unit_1' );
		$this->assert( true === $retried, 'Retry dispatches notification retry attempt' );

		// Test User Preferences (PRD-015 Part 7)
		$userPref = $prefService->updatePreferences( 42, array( 'email' => true, 'sms' => false ) );
		$this->assert( false === $userPref->smsEnabled(), 'UpdatePreferences updates SMS delivery flag' );

		// Test PRD-016 Observability, Diagnostics & Operations Platform
		echo "\n--- 5.13 Testing PRD-016 Observability, Diagnostics & Operations Platform ---\n";
		$container->bind( \Liventra\Contracts\Repositories\ObservabilityRepositoryInterface::class, \Liventra\Database\Repositories\ObservabilityRepository::class );
		$container->bind( \Liventra\Contracts\Services\ObservabilityServiceInterface::class, \Liventra\Services\ObservabilityService::class );
		$container->bind( \Liventra\Contracts\Services\LoggingServiceInterface::class, \Liventra\Services\LoggingService::class );
		$container->bind( \Liventra\Contracts\Services\TracingServiceInterface::class, \Liventra\Services\TracingService::class );
		$container->bind( \Liventra\Contracts\Services\MetricsServiceInterface::class, \Liventra\Services\MetricsService::class );
		$container->bind( \Liventra\Contracts\Services\HealthCheckServiceInterface::class, \Liventra\Services\HealthCheckService::class );
		$container->bind( \Liventra\Contracts\Services\DiagnosticsServiceInterface::class, \Liventra\Services\DiagnosticsService::class );

		$obsService   = $container->get( \Liventra\Contracts\Services\ObservabilityServiceInterface::class );
		$logService   = $container->get( \Liventra\Contracts\Services\LoggingServiceInterface::class );
		$traceService = $container->get( \Liventra\Contracts\Services\TracingServiceInterface::class );
		$metricService= $container->get( \Liventra\Contracts\Services\MetricsServiceInterface::class );
		$diagService  = $container->get( \Liventra\Contracts\Services\DiagnosticsServiceInterface::class );

		$this->assert( $obsService instanceof \Liventra\Contracts\Services\ObservabilityServiceInterface, 'Container resolved ObservabilityServiceInterface' );
		$this->assert( $logService instanceof \Liventra\Contracts\Services\LoggingServiceInterface, 'Container resolved LoggingServiceInterface' );
		$this->assert( $traceService instanceof \Liventra\Contracts\Services\TracingServiceInterface, 'Container resolved TracingServiceInterface' );
		$this->assert( $metricService instanceof \Liventra\Contracts\Services\MetricsServiceInterface, 'Container resolved MetricsServiceInterface' );
		$this->assert( $diagService instanceof \Liventra\Contracts\Services\DiagnosticsServiceInterface, 'Container resolved DiagnosticsServiceInterface' );

		// Test Correlation ID & Structured Logging (PRD-016 Part 5)
		$cid    = $obsService->createCorrelationId();
		$this->assert( 0 === strpos( $cid, 'cid_' ), 'CreateCorrelationId generates prefixed request correlation ID' );

		$logEnt = $obsService->log( 'info', 'Session started successfully', array( 'session_id' => 99 ) );
		$this->assert( 'info' === $logEnt->level(), 'Log records structured telemetry entry with info level' );

		// Test Distributed Tracing Spans (PRD-016 Part 6)
		$span   = $obsService->startSpan( 'SessionService::sync' );
		$this->assert( 0 === strpos( $span->spanId(), 'span_' ), 'StartSpan initializes active tracing span' );
		$obsService->finishSpan( $span );
		$this->assert( null !== $span->endTime(), 'FinishSpan sets valid microtime duration timestamp' );

		// Test Metrics Collection & Gauges (PRD-016 Part 8)
		$metric = $obsService->recordMetric( 'api_latency_ms', 42.5, 'histogram', array( 'route' => '/v1/session' ) );
		$this->assert( 42.5 === $metric->value(), 'RecordMetric logs numerical metric gauge value' );

		// Test Subsystem Health Checks (PRD-016 Part 7)
		$healthResults = $obsService->runHealthChecks();
		$this->assert( count( $healthResults ) >= 3, 'RunHealthChecks executes registered probes for database, eventbus, and session engine' );

		// Test Diagnostic Report Generation (PRD-016 Part 15)
		$diagReport = $diagService->generateDiagnosticReport();
		$this->assert( 0 === strpos( $diagReport->reportId(), 'diag_' ), 'GenerateDiagnosticReport creates comprehensive health report' );

		// Test PRD-017 Performance, Scalability & Runtime Optimization Platform
		echo "\n--- 5.14 Testing PRD-017 Performance, Scalability & Runtime Optimization Platform ---\n";
		$container->bind( \Liventra\Contracts\Repositories\PerformanceRepositoryInterface::class, \Liventra\Database\Repositories\PerformanceRepository::class );
		$container->bind( \Liventra\Contracts\Services\PerformanceServiceInterface::class, \Liventra\Services\PerformanceService::class );
		$container->bind( \Liventra\Contracts\Services\CacheServiceInterface::class, \Liventra\Services\CacheService::class );
		$container->bind( \Liventra\Contracts\Services\QueueServiceInterface::class, \Liventra\Services\QueueService::class );
		$container->bind( \Liventra\Contracts\Services\WorkerServiceInterface::class, \Liventra\Services\WorkerService::class );
		$container->bind( \Liventra\Contracts\Services\BenchmarkServiceInterface::class, \Liventra\Services\BenchmarkService::class );
		$container->bind( \Liventra\Contracts\Services\CapacityPlanningServiceInterface::class, \Liventra\Services\CapacityPlanningService::class );

		$perfService    = $container->get( \Liventra\Contracts\Services\PerformanceServiceInterface::class );
		$cacheService   = $container->get( \Liventra\Contracts\Services\CacheServiceInterface::class );
		$queueService   = $container->get( \Liventra\Contracts\Services\QueueServiceInterface::class );
		$workerService  = $container->get( \Liventra\Contracts\Services\WorkerServiceInterface::class );
		$benchService   = $container->get( \Liventra\Contracts\Services\BenchmarkServiceInterface::class );
		$capService     = $container->get( \Liventra\Contracts\Services\CapacityPlanningServiceInterface::class );

		$this->assert( $perfService instanceof \Liventra\Contracts\Services\PerformanceServiceInterface, 'Container resolved PerformanceServiceInterface' );
		$this->assert( $cacheService instanceof \Liventra\Contracts\Services\CacheServiceInterface, 'Container resolved CacheServiceInterface' );
		$this->assert( $queueService instanceof \Liventra\Contracts\Services\QueueServiceInterface, 'Container resolved QueueServiceInterface' );
		$this->assert( $workerService instanceof \Liventra\Contracts\Services\WorkerServiceInterface, 'Container resolved WorkerServiceInterface' );
		$this->assert( $benchService instanceof \Liventra\Contracts\Services\BenchmarkServiceInterface, 'Container resolved BenchmarkServiceInterface' );
		$this->assert( $capService instanceof \Liventra\Contracts\Services\CapacityPlanningServiceInterface, 'Container resolved CapacityPlanningServiceInterface' );

		// Test Multi-Level Caching L1/L2 (PRD-017 Part 5)
		$cacheService->cache( 'webinar_100_compiled', array( 'events' => array( 1, 2, 3 ) ), 3600 );
		$cachedVal = $cacheService->remember( 'webinar_100_compiled', function() { return array(); } );
		$this->assert( count( $cachedVal['events'] ) === 3, 'Remember returns cached L1/L2 object entry on cache hit' );

		// Test Queue Platform & Worker Pools (PRD-017 Part 6 & 7)
		$job = $queueService->dispatch( 'analytics', array( 'event' => 'cta.click' ) );
		$this->assert( 0 === strpos( $job->jobId(), 'job_' ), 'QueueService dispatches async job' );

		$worker = $workerService->runWorker( 'analytics' );
		$this->assert( 'active' === $worker->status(), 'RunWorker starts worker pool instance' );

		// Test Benchmarking & Capacity Planning (PRD-017 Part 13 & 14)
		$bench = $perfService->benchmark( 'timeline_compilation' );
		$this->assert( $bench->executionTimeMs() > 0, 'Benchmark records execution duration in milliseconds' );

		$capReport = $perfService->estimateCapacity();
		$this->assert( $capReport->maxConcurrentAttendees() >= 50000, 'EstimateCapacity models 50k+ concurrent attendees' );

		// Test PRD-018 Plugin SDK & Marketplace Platform
		echo "\n--- 5.15 Testing PRD-018 Plugin SDK & Marketplace Platform ---\n";
		$container->bind( \Liventra\Contracts\Repositories\PluginRepositoryInterface::class, \Liventra\Database\Repositories\PluginRepository::class );
		$container->bind( \Liventra\Contracts\Services\PluginManagerInterface::class, \Liventra\Services\PluginManagerService::class );
		$container->bind( \Liventra\Contracts\Services\MarketplaceServiceInterface::class, \Liventra\Services\MarketplaceService::class );
		$container->bind( \Liventra\Contracts\Services\PluginSandboxInterface::class, \Liventra\Services\PluginSandbox::class );
		$container->bind( \Liventra\Contracts\Services\SdkServiceInterface::class, \Liventra\Services\SdkService::class );

		$pluginManager  = $container->get( \Liventra\Contracts\Services\PluginManagerInterface::class );
		$marketplaceSvc = $container->get( \Liventra\Contracts\Services\MarketplaceServiceInterface::class );
		$sandboxSvc     = $container->get( \Liventra\Contracts\Services\PluginSandboxInterface::class );
		$sdkSvc         = $container->get( \Liventra\Contracts\Services\SdkServiceInterface::class );

		$this->assert( $pluginManager instanceof \Liventra\Contracts\Services\PluginManagerInterface, 'Container resolved PluginManagerInterface' );
		$this->assert( $marketplaceSvc instanceof \Liventra\Contracts\Services\MarketplaceServiceInterface, 'Container resolved MarketplaceServiceInterface' );
		$this->assert( $sandboxSvc instanceof \Liventra\Contracts\Services\PluginSandboxInterface, 'Container resolved PluginSandboxInterface' );
		$this->assert( $sdkSvc instanceof \Liventra\Contracts\Services\SdkServiceInterface, 'Container resolved SdkServiceInterface' );

		// Test Plugin Installation & Signature Verification (PRD-018 Part 4 & 11)
		$manifest = new \Liventra\Entities\PluginManifest( 'plg_activecampaign', 'ActiveCampaign Integration', 'Liventra Partner', '1.0.0', '^1.0', array( 'read_webinars', 'write_registrations' ) );
		$plugin   = $pluginManager->install( $manifest );
		$this->assert( 'plg_activecampaign' === $plugin->pluginId(), 'Install validates manifest and creates plugin instance' );
		$this->assert( true === $plugin->signature()->isVerified(), 'Install verifies digital signature hash' );

		// Test Plugin Lifecycle States (PRD-018 Part 4)
		$enabled = $pluginManager->enable( 'plg_activecampaign' );
		$this->assert( true === $enabled, 'Enable activates installed plugin' );

		$disabled = $pluginManager->disable( 'plg_activecampaign' );
		$this->assert( true === $disabled, 'Disable deactivates plugin instance' );

		// Test Plugin Sandbox Capability Isolation (PRD-018 Part 8)
		$hasCaps = $sandboxSvc->enforceCapabilities( $plugin, array( 'read_webinars' ) );
		$this->assert( true === $hasCaps, 'EnforceCapabilities validates granted manifest permissions' );

		// Test Marketplace Catalog Browsing (PRD-018 Part 9)
		$listings = $marketplaceSvc->search( 'zapier' );
		$this->assert( count( $listings ) >= 2, 'Search returns available marketplace plugin listings' );

		// Test SDK Hook & Endpoint Registration (PRD-018 Part 6 & 12)
		$hookReg = $sdkSvc->registerHook( 'registration.created', function() {} );
		$this->assert( true === $hookReg, 'RegisterHook registers plugin subscriber on EventBus' );

		// Test PRD-019 Enterprise Platform, Organizations & Multi-Tenant Architecture
		echo "\n--- 5.16 Testing PRD-019 Enterprise Platform, Organizations & Multi-Tenant Architecture ---\n";
		$container->bind( \Liventra\Contracts\Repositories\EnterpriseRepositoryInterface::class, \Liventra\Database\Repositories\EnterpriseRepository::class );
		$container->bind( \Liventra\Contracts\Services\OrganizationServiceInterface::class, \Liventra\Services\OrganizationService::class );
		$container->bind( \Liventra\Contracts\Services\WorkspaceServiceInterface::class, \Liventra\Services\WorkspaceService::class );
		$container->bind( \Liventra\Contracts\Services\TenantServiceInterface::class, \Liventra\Services\TenantService::class );
		$container->bind( \Liventra\Contracts\Services\WhiteLabelServiceInterface::class, \Liventra\Services\WhiteLabelService::class );
		$container->bind( \Liventra\Contracts\Services\EnterpriseIdentityServiceInterface::class, \Liventra\Services\EnterpriseIdentityService::class );
		$container->bind( \Liventra\Contracts\Services\GovernanceServiceInterface::class, \Liventra\Services\GovernanceService::class );

		$orgService     = $container->get( \Liventra\Contracts\Services\OrganizationServiceInterface::class );
		$wsService      = $container->get( \Liventra\Contracts\Services\WorkspaceServiceInterface::class );
		$tenantService  = $container->get( \Liventra\Contracts\Services\TenantServiceInterface::class );
		$wlService      = $container->get( \Liventra\Contracts\Services\WhiteLabelServiceInterface::class );
		$ssoService     = $container->get( \Liventra\Contracts\Services\EnterpriseIdentityServiceInterface::class );
		$govService     = $container->get( \Liventra\Contracts\Services\GovernanceServiceInterface::class );

		$this->assert( $orgService instanceof \Liventra\Contracts\Services\OrganizationServiceInterface, 'Container resolved OrganizationServiceInterface' );
		$this->assert( $wsService instanceof \Liventra\Contracts\Services\WorkspaceServiceInterface, 'Container resolved WorkspaceServiceInterface' );
		$this->assert( $tenantService instanceof \Liventra\Contracts\Services\TenantServiceInterface, 'Container resolved TenantServiceInterface' );
		$this->assert( $wlService instanceof \Liventra\Contracts\Services\WhiteLabelServiceInterface, 'Container resolved WhiteLabelServiceInterface' );
		$this->assert( $ssoService instanceof \Liventra\Contracts\Services\EnterpriseIdentityServiceInterface, 'Container resolved EnterpriseIdentityServiceInterface' );
		$this->assert( $govService instanceof \Liventra\Contracts\Services\GovernanceServiceInterface, 'Container resolved GovernanceServiceInterface' );

		// Test Organization Creation & Membership (PRD-019 Part 4)
		$org = $orgService->createOrganization( 'Acme Enterprise Corp', 10 );
		$this->assert( 0 === strpos( $org->orgId(), 'org_' ), 'CreateOrganization initializes enterprise organization' );

		$member = $orgService->joinOrganization( $org->orgId(), 20, 'admin' );
		$this->assert( 'admin' === $member->role(), 'JoinOrganization assigns organization role' );

		// Test Workspace Isolation (PRD-019 Part 5)
		$ws = $wsService->createWorkspace( $org->orgId(), 'Marketing Launch' );
		$this->assert( 0 === strpos( $ws->workspaceId(), 'ws_' ), 'CreateWorkspace creates isolated workspace' );

		// Test Multi-Tenant Resolution (PRD-019 Part 6)
		$tenantService->assignTenant( $org->orgId(), 'live.acme.com' );
		$resolved = $tenantService->resolveTenant( 'live.acme.com' );
		$this->assert( null !== $resolved && $resolved->orgId() === $org->orgId(), 'ResolveTenant resolves custom domain host to organization' );

		// Test SAML Enterprise SSO (PRD-019 Part 7)
		$ssoAuth = $ssoService->authenticateEnterprise( '<saml></saml>' );
		$this->assert( true === $ssoAuth['authenticated'], 'AuthenticateEnterprise validates SAML SSO assertion' );

		// Test White-Label Branding Customization (PRD-019 Part 9)
		$branding = $wlService->configureBranding( $org->orgId(), array( 'custom_domain' => 'live.acme.com', 'primary_color' => '#10b981' ) );
		$this->assert( '#10b981' === $branding->primaryColor(), 'ConfigureBranding sets custom brand color' );

		// Test Enterprise Governance & Quota Enforcement (PRD-019 Part 10 & 11)
		$policy = $govService->applyPolicy( $org->orgId(), array( 'mfa_required' => true ) );
		$this->assert( true === $policy->mfaRequired(), 'ApplyPolicy enforces MFA requirement' );

		$quotaOk = $govService->checkQuota( $org->orgId(), 'webinar' );
		$this->assert( true === $quotaOk, 'CheckQuota validates usage limits' );

		$auditExp = $govService->exportAudit( $org->orgId() );
		$this->assert( 0 === strpos( $auditExp->exportId(), 'exp_' ), 'ExportAudit generates audit export record' );
	}

	protected function test_timeline_engine_jit_filtering() {
		echo "\n--- 6. Testing Timeline Engine Just-In-Time (JIT) Security ---\n";
		$timeline_module = new \Liventra\Modules\Timeline\TimelineModule();

		$events = array(
			array( 'id' => 1, 'trigger_time' => 30, 'event_type' => 'cta_buy' ),
			array( 'id' => 2, 'trigger_time' => 60, 'event_type' => 'poll_question' ),
			array( 'id' => 3, 'trigger_time' => 120, 'event_type' => 'bonus_download' ),
		);

		// Elapsed = 45s (Only event at 30s should be delivered)
		$spooled = $timeline_module->get_triggered_events( $events, 45, 0 );
		$this->assert( count( $spooled ) === 1, 'Only triggered events up to current elapsed time are spooled' );
		$this->assert( 1 === $spooled[0]['id'], 'Spooled event is the 30s CTA' );

		// Advanced elapsed = 90s, last_synced = 45s (Only event at 60s should be delivered)
		$spooled_next = $timeline_module->get_triggered_events( $events, 90, 45 );
		$this->assert( count( $spooled_next ) === 1, 'Subsequent sync returns only newly triggered events' );
		$this->assert( 2 === $spooled_next[0]['id'], 'Spooled event is the 60s poll' );
	}

	protected function test_session_rest_controller() {
		echo "\n--- 7. Testing Session REST Controller Endpoint Response ---\n";
		$plugin     = \Liventra\Plugin::instance();
		$controller = new \Liventra\REST\SessionController();

		$request = array(
			'scheduled_start'     => time() - 300, // 300 seconds ago
			'video_duration'      => 1800,
			'last_synced_offset'  => 0,
			'timeline_events'     => array(
				array( 'id' => 10, 'trigger_time' => 150, 'event_type' => 'cta_offer' ),
			),
		);

		$response = $controller->handle_session_sync( $request );
		$this->assert( true === $response['success'], 'REST controller returns success response' );
		$this->assert( 'live' === $response['data']['state'], 'REST sync data calculates live state' );
		$this->assert( count( $response['data']['events'] ) === 1, 'REST sync returns triggered JIT timeline events' );
	}
}

$runner = new TestRunner();
$success = $runner->run();
exit( $success ? 0 : 1 );
