/**
 * Node.js Static Syntax & Architectural Integrity Verifier for Liventra PHP Codebase
 */
const fs = require('fs');
const path = require('path');

const projectRoot = path.join(__dirname, '..');

let totalTests = 0;
let passedTests = 0;

function assert(condition, message) {
    totalTests++;
    if (condition) {
        passedTests++;
        console.log(`  ✅ PASS: ${message}`);
    } else {
        console.log(`  ❌ FAIL: ${message}`);
    }
}

console.log("====================================================");
console.log("🚀 RUNNING LIVENTRA CODEBASE & ARCHITECTURE VERIFIER");
console.log("====================================================\n");

// 1. Verify Core Plugin Bootstrap
console.log("--- 1. Verifying Core Plugin Bootstrap (liventra.php) ---");
const bootstrapPath = path.join(projectRoot, 'liventra.php');
assert(fs.existsSync(bootstrapPath), "liventra.php exists in root directory");
const bootstrapContent = fs.readFileSync(bootstrapPath, 'utf8');
assert(bootstrapContent.includes("Plugin Name: Liventra"), "Plugin metadata header present");
assert(bootstrapContent.includes("Autoloader::register()"), "PSR-4 autoloader registered");

// 2. Verify Autoloader
console.log("\n--- 2. Verifying Autoloader (includes/Autoloader.php) ---");
const autoloaderPath = path.join(projectRoot, 'includes', 'Autoloader.php');
assert(fs.existsSync(autoloaderPath), "includes/Autoloader.php exists");
const autoloaderContent = fs.readFileSync(autoloaderPath, 'utf8');
assert(autoloaderContent.includes("namespace Liventra;"), "Correct namespace declaration");
assert(autoloaderContent.includes("spl_autoload_register"), "spl_autoload_register implementation present");

// 3. Verify Database Migrator Schemas (PRD-003)
console.log("\n--- 3. Verifying Database Migrator Schemas (PRD-003 Specs) ---");
const migratorPath = path.join(projectRoot, 'includes', 'Database', 'Migrator.php');
assert(fs.existsSync(migratorPath), "includes/Database/Migrator.php exists");
const migratorContent = fs.readFileSync(migratorPath, 'utf8');
assert(migratorContent.includes("webinar_id bigint(20) unsigned"), "webinars table uses webinar_id PK");
assert(migratorContent.includes("session_id bigint(20) unsigned"), "sessions table uses session_id PK");
assert(migratorContent.includes("attendee_id bigint(20) unsigned"), "attendees table uses attendee_id PK");
assert(migratorContent.includes("event_id bigint(20) unsigned"), "timeline_events table uses event_id PK");
assert(migratorContent.includes("analytics_id bigint(20) unsigned"), "analytics_events table uses analytics_id PK");
assert(migratorContent.includes("trigger_second"), "timeline_events table uses trigger_second");

// 3.1 Verify Repository Classes
console.log("\n--- 3.1 Verifying Database Repositories ---");
const repos = ['WebinarRepository.php', 'SessionRepository.php', 'AttendeeRepository.php', 'TimelineRepository.php', 'AnalyticsRepository.php'];
repos.forEach(repoFile => {
    const repoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', repoFile);
    assert(fs.existsSync(repoPath), `Repository ${repoFile} exists`);
});

// 4. Verify All 10 Subsystem Modules
console.log("\n--- 4. Verifying Subsystem Modules (includes/Modules/*) ---");
const expectedModules = [
    'Webinar/WebinarModule.php',
    'Registration/RegistrationModule.php',
    'Session/SessionModule.php',
    'Timeline/TimelineModule.php',
    'Video/VideoModule.php',
    'CTA/CtaModule.php',
    'Chat/ChatModule.php',
    'Notification/NotificationModule.php',
    'Analytics/AnalyticsModule.php',
    'Cloud/CloudModule.php'
];

expectedModules.forEach(modFile => {
    const modPath = path.join(projectRoot, 'includes', 'Modules', modFile);
    assert(fs.existsSync(modPath), `Module ${modFile} exists`);
    const modContent = fs.readFileSync(modPath, 'utf8');
    assert(modContent.includes("implements ModuleInterface"), `Module ${modFile} implements ModuleInterface`);
});

// 5. Verify Plugin Singleton Registry
console.log("\n--- 5. Verifying Plugin Singleton Container (includes/Plugin.php) ---");
const pluginPath = path.join(projectRoot, 'includes', 'Plugin.php');
assert(fs.existsSync(pluginPath), "includes/Plugin.php exists");
const pluginContent = fs.readFileSync(pluginPath, 'utf8');
assert(pluginContent.includes("class Plugin"), "Plugin singleton class declared");
assert(pluginContent.includes("register_default_modules"), "Registers all 10 core modules");

// 6. Verify Container, Contracts, Entities & Services (PRD-004)
console.log("\n--- 6. Verifying DI Container, Contracts, Entities & Services (PRD-004 Specs) ---");
const containerPath = path.join(projectRoot, 'includes', 'Container.php');
assert(fs.existsSync(containerPath), "includes/Container.php exists");
const containerContent = fs.readFileSync(containerPath, 'utf8');
assert(containerContent.includes("class Container"), "Liventra Container class declared");

const sessionInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'SessionServiceInterface.php');
assert(fs.existsSync(sessionInterfacePath), "SessionServiceInterface.php exists");

const sessionEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Session.php');
assert(fs.existsSync(sessionEntityPath), "Entities/Session.php exists");
const sessionEntityContent = fs.readFileSync(sessionEntityPath, 'utf8');
assert(sessionEntityContent.includes("isLive"), "Session Domain Entity implements isLive()");
assert(sessionEntityContent.includes("isWaiting"), "Session Domain Entity implements isWaiting()");

const sessionServicePath = path.join(projectRoot, 'includes', 'Services', 'SessionService.php');
assert(fs.existsSync(sessionServicePath), "Services/SessionService.php exists");
const sessionServiceContent = fs.readFileSync(sessionServicePath, 'utf8');
assert(sessionServiceContent.includes("implements SessionServiceInterface"), "SessionService implements SessionServiceInterface");

// PRD-006 Checks
const timelineServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'TimelineServiceInterface.php');
assert(fs.existsSync(timelineServiceInterfacePath), "TimelineServiceInterface.php exists");

const timelineRepoInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Repositories', 'TimelineRepositoryInterface.php');
assert(fs.existsSync(timelineRepoInterfacePath), "TimelineRepositoryInterface.php exists");

const timelineEventEntityPath = path.join(projectRoot, 'includes', 'Entities', 'TimelineEvent.php');
assert(fs.existsSync(timelineEventEntityPath), "Entities/TimelineEvent.php exists");
const timelineEventContent = fs.readFileSync(timelineEventEntityPath, 'utf8');
assert(timelineEventContent.includes("isEligible"), "TimelineEvent entity implements isEligible()");
assert(timelineEventContent.includes("isReplayable"), "TimelineEvent entity implements isReplayable()");
assert(timelineEventContent.includes("priorityWeight"), "TimelineEvent entity implements priorityWeight()");

const timelineVersionEntityPath = path.join(projectRoot, 'includes', 'Entities', 'TimelineVersion.php');
assert(fs.existsSync(timelineVersionEntityPath), "Entities/TimelineVersion.php exists");

const eventDepEntityPath = path.join(projectRoot, 'includes', 'Entities', 'EventDependency.php');
assert(fs.existsSync(eventDepEntityPath), "Entities/EventDependency.php exists");

const eventExecEntityPath = path.join(projectRoot, 'includes', 'Entities', 'EventExecution.php');
assert(fs.existsSync(eventExecEntityPath), "Entities/EventExecution.php exists");

const eventRegistryPath = path.join(projectRoot, 'includes', 'Registries', 'EventRegistry.php');
assert(fs.existsSync(eventRegistryPath), "Registries/EventRegistry.php exists");
const eventRegistryContent = fs.readFileSync(eventRegistryPath, 'utf8');
assert(eventRegistryContent.includes("class EventRegistry"), "EventRegistry class declared");
assert(eventRegistryContent.includes("dispatch"), "EventRegistry handles handler dispatching");

const timelineServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'TimelineService.php');
assert(fs.existsSync(timelineServiceEnginePath), "Services/TimelineService.php exists");
const timelineServiceEngineContent = fs.readFileSync(timelineServiceEnginePath, 'utf8');
assert(timelineServiceEngineContent.includes("implements TimelineServiceInterface"), "TimelineService implements TimelineServiceInterface");
assert(timelineServiceEngineContent.includes("publishTimeline"), "TimelineService implements publishTimeline()");
assert(timelineServiceEngineContent.includes("validateTimeline"), "TimelineService implements validateTimeline()");
assert(timelineServiceEngineContent.includes("detectCircularDependencies"), "TimelineService detects circular dependencies");
assert(timelineServiceEngineContent.includes("restoreState"), "TimelineService implements restoreState()");

// Phase 10 Pluggable Extension Framework Checks
const eventHandlerInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Events', 'EventHandlerInterface.php');
assert(fs.existsSync(eventHandlerInterfacePath), "EventHandlerInterface.php exists");

const handlerMetadataPath = path.join(projectRoot, 'includes', 'Events', 'HandlerMetadata.php');
assert(fs.existsSync(handlerMetadataPath), "Events/HandlerMetadata.php exists");

const handlesEventAttributePath = path.join(projectRoot, 'includes', 'Attributes', 'HandlesEvent.php');
assert(fs.existsSync(handlesEventAttributePath), "Attributes/HandlesEvent.php exists");

const eventResultPath = path.join(projectRoot, 'includes', 'Events', 'EventResult.php');
assert(fs.existsSync(eventResultPath), "Events/EventResult.php exists");

const eventTypeRegistryPath = path.join(projectRoot, 'includes', 'Events', 'EventTypeRegistry.php');
assert(fs.existsSync(eventTypeRegistryPath), "Events/EventTypeRegistry.php exists");
const eventTypeRegistryContent = fs.readFileSync(eventTypeRegistryPath, 'utf8');
assert(eventTypeRegistryContent.includes("isValid"), "EventTypeRegistry implements isValid()");

const extensionManagerPath = path.join(projectRoot, 'includes', 'Extensions', 'ExtensionManager.php');
assert(fs.existsSync(extensionManagerPath), "Extensions/ExtensionManager.php exists");
const extensionManagerContent = fs.readFileSync(extensionManagerPath, 'utf8');
assert(extensionManagerContent.includes("registerExtension"), "ExtensionManager implements registerExtension()");

const extensionDiagnosticsPath = path.join(projectRoot, 'includes', 'Extensions', 'ExtensionDiagnostics.php');
assert(fs.existsSync(extensionDiagnosticsPath), "Extensions/ExtensionDiagnostics.php exists");
const extensionDiagnosticsContent = fs.readFileSync(extensionDiagnosticsPath, 'utf8');
assert(extensionDiagnosticsContent.includes("recordInvocation"), "ExtensionDiagnostics implements recordInvocation()");

// PRD-007 Video Engine Checks
const videoServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'VideoServiceInterface.php');
assert(fs.existsSync(videoServiceInterfacePath), "VideoServiceInterface.php exists");

const videoProviderInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Providers', 'VideoProviderInterface.php');
assert(fs.existsSync(videoProviderInterfacePath), "VideoProviderInterface.php exists");

const videoAssetEntityPath = path.join(projectRoot, 'includes', 'Entities', 'VideoAsset.php');
assert(fs.existsSync(videoAssetEntityPath), "Entities/VideoAsset.php exists");

const playbackStateEntityPath = path.join(projectRoot, 'includes', 'Entities', 'PlaybackState.php');
assert(fs.existsSync(playbackStateEntityPath), "Entities/PlaybackState.php exists");

const playbackQualityEntityPath = path.join(projectRoot, 'includes', 'Entities', 'PlaybackQuality.php');
assert(fs.existsSync(playbackQualityEntityPath), "Entities/PlaybackQuality.php exists");

const mp4ProviderPath = path.join(projectRoot, 'includes', 'Providers', 'MP4Provider.php');
assert(fs.existsSync(mp4ProviderPath), "Providers/MP4Provider.php exists");

const hlsProviderPath = path.join(projectRoot, 'includes', 'Providers', 'HLSProvider.php');
assert(fs.existsSync(hlsProviderPath), "Providers/HLSProvider.php exists");

const bunnyProviderPath = path.join(projectRoot, 'includes', 'Providers', 'BunnyProvider.php');
assert(fs.existsSync(bunnyProviderPath), "Providers/BunnyProvider.php exists");

const providerResolverPath = path.join(projectRoot, 'includes', 'Resolvers', 'ProviderResolver.php');
assert(fs.existsSync(providerResolverPath), "Resolvers/ProviderResolver.php exists");

const videoServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'VideoService.php');
assert(fs.existsSync(videoServiceEnginePath), "Services/VideoService.php exists");
const videoServiceEngineContent = fs.readFileSync(videoServiceEnginePath, 'utf8');
assert(videoServiceEngineContent.includes("implements VideoServiceInterface"), "VideoService implements VideoServiceInterface");
assert(videoServiceEngineContent.includes("synchronize"), "VideoService implements synchronize()");

const videoEngineClientJsPath = path.join(projectRoot, 'assets', 'js', 'video-engine.js');
assert(fs.existsSync(videoEngineClientJsPath), "assets/js/video-engine.js exists");

// PRD-008 Registration Engine Checks
const regServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'RegistrationServiceInterface.php');
assert(fs.existsSync(regServiceInterfacePath), "RegistrationServiceInterface.php exists");

const regRepoInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Repositories', 'RegistrationRepositoryInterface.php');
assert(fs.existsSync(regRepoInterfacePath), "RegistrationRepositoryInterface.php exists");

const regEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Registration.php');
assert(fs.existsSync(regEntityPath), "Entities/Registration.php exists");

const attendeeIdentityEntityPath = path.join(projectRoot, 'includes', 'Entities', 'AttendeeIdentity.php');
assert(fs.existsSync(attendeeIdentityEntityPath), "Entities/AttendeeIdentity.php exists");

const joinTokenEntityPath = path.join(projectRoot, 'includes', 'Entities', 'JoinToken.php');
assert(fs.existsSync(joinTokenEntityPath), "Entities/JoinToken.php exists");

const waitingRoomStateEntityPath = path.join(projectRoot, 'includes', 'Entities', 'WaitingRoomState.php');
assert(fs.existsSync(waitingRoomStateEntityPath), "Entities/WaitingRoomState.php exists");

const regRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'RegistrationRepository.php');
assert(fs.existsSync(regRepoPath), "Repositories/RegistrationRepository.php exists");

const regServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'RegistrationService.php');
assert(fs.existsSync(regServiceEnginePath), "Services/RegistrationService.php exists");
const regServiceEngineContent = fs.readFileSync(regServiceEnginePath, 'utf8');
assert(regServiceEngineContent.includes("implements RegistrationServiceInterface"), "RegistrationService implements RegistrationServiceInterface");
assert(regServiceEngineContent.includes("authorizeJoin"), "RegistrationService implements authorizeJoin()");

const regClientJsPath = path.join(projectRoot, 'assets', 'js', 'registration-engine.js');
assert(fs.existsSync(regClientJsPath), "assets/js/registration-engine.js exists");

// PRD-009 CTA Engine Checks
const ctaServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'CTAServiceInterface.php');
assert(fs.existsSync(ctaServiceInterfacePath), "CTAServiceInterface.php exists");

const ctaRepoInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Repositories', 'CTARepositoryInterface.php');
assert(fs.existsSync(ctaRepoInterfacePath), "CTARepositoryInterface.php exists");

const ctaEntityPath = path.join(projectRoot, 'includes', 'Entities', 'CTA.php');
assert(fs.existsSync(ctaEntityPath), "Entities/CTA.php exists");

const ctaStateEntityPath = path.join(projectRoot, 'includes', 'Entities', 'CTAState.php');
assert(fs.existsSync(ctaStateEntityPath), "Entities/CTAState.php exists");

const ctaInteractionEntityPath = path.join(projectRoot, 'includes', 'Entities', 'CTAInteraction.php');
assert(fs.existsSync(ctaInteractionEntityPath), "Entities/CTAInteraction.php exists");

const offerEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Offer.php');
assert(fs.existsSync(offerEntityPath), "Entities/Offer.php exists");

const ctaRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'CTARepository.php');
assert(fs.existsSync(ctaRepoPath), "Repositories/CTARepository.php exists");

const ctaServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'CTAService.php');
assert(fs.existsSync(ctaServiceEnginePath), "Services/CTAService.php exists");
const ctaServiceEngineContent = fs.readFileSync(ctaServiceEnginePath, 'utf8');
assert(ctaServiceEngineContent.includes("implements CTAServiceInterface"), "CTAService implements CTAServiceInterface");
assert(ctaServiceEngineContent.includes("resolveEligibility"), "CTAService implements resolveEligibility()");

const ctaClientJsPath = path.join(projectRoot, 'assets', 'js', 'cta-engine.js');
assert(fs.existsSync(ctaClientJsPath), "assets/js/cta-engine.js exists");

// PRD-010 Live Chat Engine Checks
const chatServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'ChatServiceInterface.php');
assert(fs.existsSync(chatServiceInterfacePath), "ChatServiceInterface.php exists");

const chatRepoInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Repositories', 'ChatRepositoryInterface.php');
assert(fs.existsSync(chatRepoInterfacePath), "ChatRepositoryInterface.php exists");

const chatMessageEntityPath = path.join(projectRoot, 'includes', 'Entities', 'ChatMessage.php');
assert(fs.existsSync(chatMessageEntityPath), "Entities/ChatMessage.php exists");

const chatStateEntityPath = path.join(projectRoot, 'includes', 'Entities', 'ChatState.php');
assert(fs.existsSync(chatStateEntityPath), "Entities/ChatState.php exists");

const chatReactionEntityPath = path.join(projectRoot, 'includes', 'Entities', 'ChatReaction.php');
assert(fs.existsSync(chatReactionEntityPath), "Entities/ChatReaction.php exists");

const moderatorProfileEntityPath = path.join(projectRoot, 'includes', 'Entities', 'ModeratorProfile.php');
assert(fs.existsSync(moderatorProfileEntityPath), "Entities/ModeratorProfile.php exists");

const chatRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'ChatRepository.php');
assert(fs.existsSync(chatRepoPath), "Repositories/ChatRepository.php exists");

const chatServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'ChatService.php');
assert(fs.existsSync(chatServiceEnginePath), "Services/ChatService.php exists");
const chatServiceEngineContent = fs.readFileSync(chatServiceEnginePath, 'utf8');
assert(chatServiceEngineContent.includes("implements ChatServiceInterface"), "ChatService implements ChatServiceInterface");
assert(chatServiceEngineContent.includes("pinMessage"), "ChatService implements pinMessage()");

const chatClientJsPath = path.join(projectRoot, 'assets', 'js', 'chat-engine.js');
assert(fs.existsSync(chatClientJsPath), "assets/js/chat-engine.js exists");

// PRD-011 Analytics Engine Checks
const analyticsServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'AnalyticsServiceInterface.php');
assert(fs.existsSync(analyticsServiceInterfacePath), "AnalyticsServiceInterface.php exists");

const analyticsRepoInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Repositories', 'AnalyticsRepositoryInterface.php');
assert(fs.existsSync(analyticsRepoInterfacePath), "AnalyticsRepositoryInterface.php exists");

const analyticsEventEntityPath = path.join(projectRoot, 'includes', 'Entities', 'AnalyticsEvent.php');
assert(fs.existsSync(analyticsEventEntityPath), "Entities/AnalyticsEvent.php exists");

const metricEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Metric.php');
assert(fs.existsSync(metricEntityPath), "Entities/Metric.php exists");

const funnelEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Funnel.php');
assert(fs.existsSync(funnelEntityPath), "Entities/Funnel.php exists");

const engagementSnapshotEntityPath = path.join(projectRoot, 'includes', 'Entities', 'EngagementSnapshot.php');
assert(fs.existsSync(engagementSnapshotEntityPath), "Entities/EngagementSnapshot.php exists");

const analyticsRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'AnalyticsRepository.php');
assert(fs.existsSync(analyticsRepoPath), "Repositories/AnalyticsRepository.php exists");

const analyticsServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'AnalyticsService.php');
assert(fs.existsSync(analyticsServiceEnginePath), "Services/AnalyticsService.php exists");
const analyticsServiceEngineContent = fs.readFileSync(analyticsServiceEnginePath, 'utf8');
assert(analyticsServiceEngineContent.includes("implements AnalyticsServiceInterface"), "AnalyticsService implements AnalyticsServiceInterface");
assert(analyticsServiceEngineContent.includes("getDashboardMetrics"), "AnalyticsService implements getDashboardMetrics()");

const analyticsClientJsPath = path.join(projectRoot, 'assets', 'js', 'analytics-engine.js');
assert(fs.existsSync(analyticsClientJsPath), "assets/js/analytics-engine.js exists");

// PRD-012 Admin Studio Checks
const adminStudioServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'AdminStudioServiceInterface.php');
assert(fs.existsSync(adminStudioServiceInterfacePath), "AdminStudioServiceInterface.php exists");

const adminStudioServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'AdminStudioService.php');
assert(fs.existsSync(adminStudioServiceEnginePath), "Services/AdminStudioService.php exists");
const adminStudioServiceEngineContent = fs.readFileSync(adminStudioServiceEnginePath, 'utf8');
assert(adminStudioServiceEngineContent.includes("implements AdminStudioServiceInterface"), "AdminStudioService implements AdminStudioServiceInterface");
assert(adminStudioServiceEngineContent.includes("validateConfiguration"), "AdminStudioService implements validateConfiguration()");

const adminStudioControllerPath = path.join(projectRoot, 'includes', 'REST', 'AdminStudioController.php');
assert(fs.existsSync(adminStudioControllerPath), "REST/AdminStudioController.php exists");

const adminStudioClientJsPath = path.join(projectRoot, 'assets', 'js', 'admin-studio.js');
assert(fs.existsSync(adminStudioClientJsPath), "assets/js/admin-studio.js exists");

const adminStudioCssPath = path.join(projectRoot, 'assets', 'css', 'admin.css');
assert(fs.existsSync(adminStudioCssPath), "assets/css/admin.css exists");

// PRD-013 Security Platform Checks
const securityServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'SecurityServiceInterface.php');
assert(fs.existsSync(securityServiceInterfacePath), "SecurityServiceInterface.php exists");

const authServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'AuthorizationServiceInterface.php');
assert(fs.existsSync(authServiceInterfacePath), "AuthorizationServiceInterface.php exists");

const auditServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'AuditServiceInterface.php');
assert(fs.existsSync(auditServiceInterfacePath), "AuditServiceInterface.php exists");

const secretManagerInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'SecretManagerInterface.php');
assert(fs.existsSync(secretManagerInterfacePath), "SecretManagerInterface.php exists");

const securityTokenEntityPath = path.join(projectRoot, 'includes', 'Entities', 'SecurityToken.php');
assert(fs.existsSync(securityTokenEntityPath), "Entities/SecurityToken.php exists");

const auditRecordEntityPath = path.join(projectRoot, 'includes', 'Entities', 'AuditRecord.php');
assert(fs.existsSync(auditRecordEntityPath), "Entities/AuditRecord.php exists");

const secretEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Secret.php');
assert(fs.existsSync(secretEntityPath), "Entities/Secret.php exists");

const securityRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'SecurityRepository.php');
assert(fs.existsSync(securityRepoPath), "Repositories/SecurityRepository.php exists");

const securityServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'SecurityService.php');
assert(fs.existsSync(securityServiceEnginePath), "Services/SecurityService.php exists");
const securityServiceEngineContent = fs.readFileSync(securityServiceEnginePath, 'utf8');
assert(securityServiceEngineContent.includes("implements SecurityServiceInterface"), "SecurityService implements SecurityServiceInterface");
assert(securityServiceEngineContent.includes("checkRateLimit"), "SecurityService implements checkRateLimit()");

const securityMiddlewarePath = path.join(projectRoot, 'includes', 'Middleware', 'SecurityMiddleware.php');
assert(fs.existsSync(securityMiddlewarePath), "Middleware/SecurityMiddleware.php exists");

const securityClientJsPath = path.join(projectRoot, 'assets', 'js', 'security-engine.js');
assert(fs.existsSync(securityClientJsPath), "assets/js/security-engine.js exists");

// PRD-014 Public API & Integration Platform Checks
const apiGatewayInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'ApiGatewayInterface.php');
assert(fs.existsSync(apiGatewayInterfacePath), "ApiGatewayInterface.php exists");

const webhookServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'WebhookServiceInterface.php');
assert(fs.existsSync(webhookServiceInterfacePath), "WebhookServiceInterface.php exists");

const apiKeyServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'ApiKeyServiceInterface.php');
assert(fs.existsSync(apiKeyServiceInterfacePath), "ApiKeyServiceInterface.php exists");

const openApiServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'OpenApiServiceInterface.php');
assert(fs.existsSync(openApiServiceInterfacePath), "OpenApiServiceInterface.php exists");

const apiKeyEntityPath = path.join(projectRoot, 'includes', 'Entities', 'ApiKey.php');
assert(fs.existsSync(apiKeyEntityPath), "Entities/ApiKey.php exists");

const webhookSubscriptionEntityPath = path.join(projectRoot, 'includes', 'Entities', 'WebhookSubscription.php');
assert(fs.existsSync(webhookSubscriptionEntityPath), "Entities/WebhookSubscription.php exists");

const webhookDeliveryEntityPath = path.join(projectRoot, 'includes', 'Entities', 'WebhookDelivery.php');
assert(fs.existsSync(webhookDeliveryEntityPath), "Entities/WebhookDelivery.php exists");

const idempotencyKeyEntityPath = path.join(projectRoot, 'includes', 'Entities', 'IdempotencyKey.php');
assert(fs.existsSync(idempotencyKeyEntityPath), "Entities/IdempotencyKey.php exists");

const apiRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'ApiRepository.php');
assert(fs.existsSync(apiRepoPath), "Repositories/ApiRepository.php exists");

const apiGatewayServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'ApiGatewayService.php');
assert(fs.existsSync(apiGatewayServiceEnginePath), "Services/ApiGatewayService.php exists");
const apiGatewayServiceEngineContent = fs.readFileSync(apiGatewayServiceEnginePath, 'utf8');
assert(apiGatewayServiceEngineContent.includes("implements ApiGatewayInterface"), "ApiGatewayService implements ApiGatewayInterface");
assert(apiGatewayServiceEngineContent.includes("validateIdempotency"), "ApiGatewayService implements validateIdempotency()");

const apiGatewayControllerPath = path.join(projectRoot, 'includes', 'REST', 'ApiGatewayController.php');
assert(fs.existsSync(apiGatewayControllerPath), "REST/ApiGatewayController.php exists");

const devPortalClientJsPath = path.join(projectRoot, 'assets', 'js', 'developer-portal.js');
assert(fs.existsSync(devPortalClientJsPath), "assets/js/developer-portal.js exists");

// PRD-015 Notification & Messaging Platform Checks
const notifServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'NotificationServiceInterface.php');
assert(fs.existsSync(notifServiceInterfacePath), "NotificationServiceInterface.php exists");

const emailServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'EmailServiceInterface.php');
assert(fs.existsSync(emailServiceInterfacePath), "EmailServiceInterface.php exists");

const smsServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'SmsServiceInterface.php');
assert(fs.existsSync(smsServiceInterfacePath), "SmsServiceInterface.php exists");

const whatsAppServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'WhatsAppServiceInterface.php');
assert(fs.existsSync(whatsAppServiceInterfacePath), "WhatsAppServiceInterface.php exists");

const pushServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'PushNotificationServiceInterface.php');
assert(fs.existsSync(pushServiceInterfacePath), "PushNotificationServiceInterface.php exists");

const inAppServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'InAppNotificationServiceInterface.php');
assert(fs.existsSync(inAppServiceInterfacePath), "InAppNotificationServiceInterface.php exists");

const templateServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'NotificationTemplateServiceInterface.php');
assert(fs.existsSync(templateServiceInterfacePath), "NotificationTemplateServiceInterface.php exists");

const prefServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'NotificationPreferenceServiceInterface.php');
assert(fs.existsSync(prefServiceInterfacePath), "NotificationPreferenceServiceInterface.php exists");

const notifEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Notification.php');
assert(fs.existsSync(notifEntityPath), "Entities/Notification.php exists");

const notifTemplateEntityPath = path.join(projectRoot, 'includes', 'Entities', 'NotificationTemplate.php');
assert(fs.existsSync(notifTemplateEntityPath), "Entities/NotificationTemplate.php exists");

const deliveryReceiptEntityPath = path.join(projectRoot, 'includes', 'Entities', 'DeliveryReceipt.php');
assert(fs.existsSync(deliveryReceiptEntityPath), "Entities/DeliveryReceipt.php exists");

const notifRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'NotificationRepository.php');
assert(fs.existsSync(notifRepoPath), "Repositories/NotificationRepository.php exists");

const notifServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'NotificationService.php');
assert(fs.existsSync(notifServiceEnginePath), "Services/NotificationService.php exists");
const notifServiceEngineContent = fs.readFileSync(notifServiceEnginePath, 'utf8');
assert(notifServiceEngineContent.includes("implements NotificationServiceInterface"), "NotificationService implements NotificationServiceInterface");
assert(notifServiceEngineContent.includes("renderTemplate"), "NotificationService implements renderTemplate()");

const notifControllerPath = path.join(projectRoot, 'includes', 'REST', 'NotificationController.php');
assert(fs.existsSync(notifControllerPath), "REST/NotificationController.php exists");

const notifCenterJsPath = path.join(projectRoot, 'assets', 'js', 'notification-center.js');
assert(fs.existsSync(notifCenterJsPath), "assets/js/notification-center.js exists");

const notifAdminJsPath = path.join(projectRoot, 'assets', 'js', 'notification-admin.js');
assert(fs.existsSync(notifAdminJsPath), "assets/js/notification-admin.js exists");

// PRD-016 Observability, Diagnostics & Operations Platform Checks
const obsServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'ObservabilityServiceInterface.php');
assert(fs.existsSync(obsServiceInterfacePath), "ObservabilityServiceInterface.php exists");

const loggingServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'LoggingServiceInterface.php');
assert(fs.existsSync(loggingServiceInterfacePath), "LoggingServiceInterface.php exists");

const tracingServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'TracingServiceInterface.php');
assert(fs.existsSync(tracingServiceInterfacePath), "TracingServiceInterface.php exists");

const metricsServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'MetricsServiceInterface.php');
assert(fs.existsSync(metricsServiceInterfacePath), "MetricsServiceInterface.php exists");

const healthCheckServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'HealthCheckServiceInterface.php');
assert(fs.existsSync(healthCheckServiceInterfacePath), "HealthCheckServiceInterface.php exists");

const diagServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'DiagnosticsServiceInterface.php');
assert(fs.existsSync(diagServiceInterfacePath), "DiagnosticsServiceInterface.php exists");

const logEntryEntityPath = path.join(projectRoot, 'includes', 'Entities', 'LogEntry.php');
assert(fs.existsSync(logEntryEntityPath), "Entities/LogEntry.php exists");

const spanEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Span.php');
assert(fs.existsSync(spanEntityPath), "Entities/Span.php exists");

const obsMetricEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Metric.php');
assert(fs.existsSync(obsMetricEntityPath), "Entities/Metric.php exists");

const healthStatusEntityPath = path.join(projectRoot, 'includes', 'Entities', 'HealthStatus.php');
assert(fs.existsSync(healthStatusEntityPath), "Entities/HealthStatus.php exists");

const obsRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'ObservabilityRepository.php');
assert(fs.existsSync(obsRepoPath), "Repositories/ObservabilityRepository.php exists");

const obsServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'ObservabilityService.php');
assert(fs.existsSync(obsServiceEnginePath), "Services/ObservabilityService.php exists");
const obsServiceEngineContent = fs.readFileSync(obsServiceEnginePath, 'utf8');
assert(obsServiceEngineContent.includes("implements ObservabilityServiceInterface"), "ObservabilityService implements ObservabilityServiceInterface");
assert(obsServiceEngineContent.includes("createCorrelationId"), "ObservabilityService implements createCorrelationId()");

const obsControllerPath = path.join(projectRoot, 'includes', 'REST', 'ObservabilityController.php');
assert(fs.existsSync(obsControllerPath), "REST/ObservabilityController.php exists");

const opsDashboardJsPath = path.join(projectRoot, 'assets', 'js', 'operations-dashboard.js');
assert(fs.existsSync(opsDashboardJsPath), "assets/js/operations-dashboard.js exists");

const traceViewerJsPath = path.join(projectRoot, 'assets', 'js', 'trace-viewer.js');
assert(fs.existsSync(traceViewerJsPath), "assets/js/trace-viewer.js exists");

// PRD-017 Performance Platform Checks
const perfServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'PerformanceServiceInterface.php');
assert(fs.existsSync(perfServiceInterfacePath), "PerformanceServiceInterface.php exists");

const cacheServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'CacheServiceInterface.php');
assert(fs.existsSync(cacheServiceInterfacePath), "CacheServiceInterface.php exists");

const queueServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'QueueServiceInterface.php');
assert(fs.existsSync(queueServiceInterfacePath), "QueueServiceInterface.php exists");

const workerServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'WorkerServiceInterface.php');
assert(fs.existsSync(workerServiceInterfacePath), "WorkerServiceInterface.php exists");

const benchmarkServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'BenchmarkServiceInterface.php');
assert(fs.existsSync(benchmarkServiceInterfacePath), "BenchmarkServiceInterface.php exists");

const capacityPlanningServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'CapacityPlanningServiceInterface.php');
assert(fs.existsSync(capacityPlanningServiceInterfacePath), "CapacityPlanningServiceInterface.php exists");

const cacheEntryEntityPath = path.join(projectRoot, 'includes', 'Entities', 'CacheEntry.php');
assert(fs.existsSync(cacheEntryEntityPath), "Entities/CacheEntry.php exists");

const queueJobEntityPath = path.join(projectRoot, 'includes', 'Entities', 'QueueJob.php');
assert(fs.existsSync(queueJobEntityPath), "Entities/QueueJob.php exists");

const workerEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Worker.php');
assert(fs.existsSync(workerEntityPath), "Entities/Worker.php exists");

const benchmarkResultEntityPath = path.join(projectRoot, 'includes', 'Entities', 'BenchmarkResult.php');
assert(fs.existsSync(benchmarkResultEntityPath), "Entities/BenchmarkResult.php exists");

const perfRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'PerformanceRepository.php');
assert(fs.existsSync(perfRepoPath), "Repositories/PerformanceRepository.php exists");

const perfServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'PerformanceService.php');
assert(fs.existsSync(perfServiceEnginePath), "Services/PerformanceService.php exists");
const perfServiceEngineContent = fs.readFileSync(perfServiceEnginePath, 'utf8');
assert(perfServiceEngineContent.includes("implements PerformanceServiceInterface"), "PerformanceService implements PerformanceServiceInterface");
assert(perfServiceEngineContent.includes("estimateCapacity"), "PerformanceService implements estimateCapacity()");

const perfControllerPath = path.join(projectRoot, 'includes', 'REST', 'PerformanceController.php');
assert(fs.existsSync(perfControllerPath), "REST/PerformanceController.php exists");

const perfDashboardJsPath = path.join(projectRoot, 'assets', 'js', 'performance-dashboard.js');
assert(fs.existsSync(perfDashboardJsPath), "assets/js/performance-dashboard.js exists");

// PRD-018 Plugin SDK & Marketplace Platform Checks
const pluginManagerInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'PluginManagerInterface.php');
assert(fs.existsSync(pluginManagerInterfacePath), "PluginManagerInterface.php exists");

const pluginInstallerInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'PluginInstallerInterface.php');
assert(fs.existsSync(pluginInstallerInterfacePath), "PluginInstallerInterface.php exists");

const pluginRegistryInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'PluginRegistryInterface.php');
assert(fs.existsSync(pluginRegistryInterfacePath), "PluginRegistryInterface.php exists");

const pluginSandboxInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'PluginSandboxInterface.php');
assert(fs.existsSync(pluginSandboxInterfacePath), "PluginSandboxInterface.php exists");

const marketplaceServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'MarketplaceServiceInterface.php');
assert(fs.existsSync(marketplaceServiceInterfacePath), "MarketplaceServiceInterface.php exists");

const sdkServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'SdkServiceInterface.php');
assert(fs.existsSync(sdkServiceInterfacePath), "SdkServiceInterface.php exists");

const pluginEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Plugin.php');
assert(fs.existsSync(pluginEntityPath), "Entities/Plugin.php exists");

const pluginManifestEntityPath = path.join(projectRoot, 'includes', 'Entities', 'PluginManifest.php');
assert(fs.existsSync(pluginManifestEntityPath), "Entities/PluginManifest.php exists");

const marketplaceListingEntityPath = path.join(projectRoot, 'includes', 'Entities', 'MarketplaceListing.php');
assert(fs.existsSync(marketplaceListingEntityPath), "Entities/MarketplaceListing.php exists");

const pluginRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'PluginRepository.php');
assert(fs.existsSync(pluginRepoPath), "Repositories/PluginRepository.php exists");

const pluginManagerServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'PluginManagerService.php');
assert(fs.existsSync(pluginManagerServiceEnginePath), "Services/PluginManagerService.php exists");
const pluginManagerServiceEngineContent = fs.readFileSync(pluginManagerServiceEnginePath, 'utf8');
assert(pluginManagerServiceEngineContent.includes("implements PluginManagerInterface"), "PluginManagerService implements PluginManagerInterface");

const pluginControllerPath = path.join(projectRoot, 'includes', 'REST', 'PluginController.php');
assert(fs.existsSync(pluginControllerPath), "REST/PluginController.php exists");

const marketplaceControllerPath = path.join(projectRoot, 'includes', 'REST', 'MarketplaceController.php');
assert(fs.existsSync(marketplaceControllerPath), "REST/MarketplaceController.php exists");

const pluginManagerJsPath = path.join(projectRoot, 'assets', 'js', 'plugin-manager.js');
assert(fs.existsSync(pluginManagerJsPath), "assets/js/plugin-manager.js exists");

const marketplaceBrowserJsPath = path.join(projectRoot, 'assets', 'js', 'marketplace-browser.js');
assert(fs.existsSync(marketplaceBrowserJsPath), "assets/js/marketplace-browser.js exists");

const sdkInspectorJsPath = path.join(projectRoot, 'assets', 'js', 'sdk-inspector.js');
assert(fs.existsSync(sdkInspectorJsPath), "assets/js/sdk-inspector.js exists");

// PRD-019 Enterprise Platform Checks
const orgServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'OrganizationServiceInterface.php');
assert(fs.existsSync(orgServiceInterfacePath), "OrganizationServiceInterface.php exists");

const wsServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'WorkspaceServiceInterface.php');
assert(fs.existsSync(wsServiceInterfacePath), "WorkspaceServiceInterface.php exists");

const tenantServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'TenantServiceInterface.php');
assert(fs.existsSync(tenantServiceInterfacePath), "TenantServiceInterface.php exists");

const wlServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'WhiteLabelServiceInterface.php');
assert(fs.existsSync(wlServiceInterfacePath), "WhiteLabelServiceInterface.php exists");

const ssoServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'EnterpriseIdentityServiceInterface.php');
assert(fs.existsSync(ssoServiceInterfacePath), "EnterpriseIdentityServiceInterface.php exists");

const govServiceInterfacePath = path.join(projectRoot, 'includes', 'Contracts', 'Services', 'GovernanceServiceInterface.php');
assert(fs.existsSync(govServiceInterfacePath), "GovernanceServiceInterface.php exists");

const orgEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Organization.php');
assert(fs.existsSync(orgEntityPath), "Entities/Organization.php exists");

const workspaceEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Workspace.php');
assert(fs.existsSync(workspaceEntityPath), "Entities/Workspace.php exists");

const tenantEntityPath = path.join(projectRoot, 'includes', 'Entities', 'Tenant.php');
assert(fs.existsSync(tenantEntityPath), "Entities/Tenant.php exists");

const memberEntityPath = path.join(projectRoot, 'includes', 'Entities', 'OrganizationMember.php');
assert(fs.existsSync(memberEntityPath), "Entities/OrganizationMember.php exists");

const policyEntityPath = path.join(projectRoot, 'includes', 'Entities', 'EnterprisePolicy.php');
assert(fs.existsSync(policyEntityPath), "Entities/EnterprisePolicy.php exists");

const brandingEntityPath = path.join(projectRoot, 'includes', 'Entities', 'BrandingProfile.php');
assert(fs.existsSync(brandingEntityPath), "Entities/BrandingProfile.php exists");

const enterpriseRepoPath = path.join(projectRoot, 'includes', 'Database', 'Repositories', 'EnterpriseRepository.php');
assert(fs.existsSync(enterpriseRepoPath), "Repositories/EnterpriseRepository.php exists");

const orgServiceEnginePath = path.join(projectRoot, 'includes', 'Services', 'OrganizationService.php');
assert(fs.existsSync(orgServiceEnginePath), "Services/OrganizationService.php exists");
const orgServiceEngineContent = fs.readFileSync(orgServiceEnginePath, 'utf8');
assert(orgServiceEngineContent.includes("implements OrganizationServiceInterface"), "OrganizationService implements OrganizationServiceInterface");

const orgControllerPath = path.join(projectRoot, 'includes', 'REST', 'OrganizationController.php');
assert(fs.existsSync(orgControllerPath), "REST/OrganizationController.php exists");

const orgManagerJsPath = path.join(projectRoot, 'assets', 'js', 'organization-manager.js');
assert(fs.existsSync(orgManagerJsPath), "assets/js/organization-manager.js exists");

const wsManagerJsPath = path.join(projectRoot, 'assets', 'js', 'workspace-manager.js');
assert(fs.existsSync(wsManagerJsPath), "assets/js/workspace-manager.js exists");

const brandingManagerJsPath = path.join(projectRoot, 'assets', 'js', 'branding-manager.js');
assert(fs.existsSync(brandingManagerJsPath), "assets/js/branding-manager.js exists");

const govDashboardJsPath = path.join(projectRoot, 'assets', 'js', 'governance-dashboard.js');
assert(fs.existsSync(govDashboardJsPath), "assets/js/governance-dashboard.js exists");

// Liventra RC1 Production Readiness & CI/CD Checks
const composerJsonPath = path.join(projectRoot, 'composer.json');
assert(fs.existsSync(composerJsonPath), "composer.json exists");
const composerJsonContent = fs.readFileSync(composerJsonPath, 'utf8');
assert(composerJsonContent.includes('"quality"'), "composer.json defines quality script");
assert(composerJsonContent.includes('"ci"'), "composer.json defines ci script");

const phpstanPath = path.join(projectRoot, 'phpstan.neon');
assert(fs.existsSync(phpstanPath), "phpstan.neon static analysis config exists");

const psalmPath = path.join(projectRoot, 'psalm.xml');
assert(fs.existsSync(psalmPath), "psalm.xml static analysis config exists");

const phpcsPath = path.join(projectRoot, 'phpcs.xml');
assert(fs.existsSync(phpcsPath), "phpcs.xml WordPress standard config exists");

const phpunitXmlPath = path.join(projectRoot, 'phpunit.xml');
assert(fs.existsSync(phpunitXmlPath), "phpunit.xml exists");

const playwrightConfigPath = path.join(projectRoot, 'playwright.config.ts');
assert(fs.existsSync(playwrightConfigPath), "playwright.config.ts exists");

const playwrightE2ePath = path.join(projectRoot, 'tests', 'Playwright', 'e2e.spec.ts');
assert(fs.existsSync(playwrightE2ePath), "tests/Playwright/e2e.spec.ts exists");

const ciWfPath = path.join(projectRoot, '.github', 'workflows', 'ci.yml');
assert(fs.existsSync(ciWfPath), ".github/workflows/ci.yml exists");

const releaseWfPath = path.join(projectRoot, '.github', 'workflows', 'release.yml');
assert(fs.existsSync(releaseWfPath), ".github/workflows/release.yml exists");

const readmePath = path.join(projectRoot, 'README.md');
assert(fs.existsSync(readmePath), "README.md exists");

const changelogPath = path.join(projectRoot, 'CHANGELOG.md');
assert(fs.existsSync(changelogPath), "CHANGELOG.md exists");

const releaseMdPath = path.join(projectRoot, 'RELEASE.md');
assert(fs.existsSync(releaseMdPath), "RELEASE.md exists");

const unitTestPath = path.join(projectRoot, 'tests', 'Unit', 'SessionEngineTest.php');
assert(fs.existsSync(unitTestPath), "tests/Unit/SessionEngineTest.php exists");

const sessionControllerPath = path.join(projectRoot, 'includes', 'REST', 'SessionController.php');
assert(fs.existsSync(sessionControllerPath), "SessionController.php exists");
const sessionControllerContent = fs.readFileSync(sessionControllerPath, 'utf8');
assert(sessionControllerContent.includes("Container::getInstance()"), "SessionController consumes DI Container");

// 7. Verify Client Session Engine JS & Video Player JS
console.log("\n--- 7. Verifying Client SessionEngine & Video Player JS ---");
const clientJsPath = path.join(projectRoot, 'assets', 'js', 'session-engine.js');
assert(fs.existsSync(clientJsPath), "assets/js/session-engine.js exists");
const clientJsContent = fs.readFileSync(clientJsPath, 'utf8');
assert(clientJsContent.includes("class LiventraSessionEngine"), "LiventraSessionEngine JS class declared");
assert(clientJsContent.includes("requestAnimationFrame"), "High-frequency local ticker implemented");
assert(clientJsContent.includes("syncWithServer"), "REST heartbeat sync implemented");

const videoPlayerJsPath = path.join(projectRoot, 'assets', 'js', 'video-player.js');
assert(fs.existsSync(videoPlayerJsPath), "assets/js/video-player.js exists");
const videoPlayerContent = fs.readFileSync(videoPlayerJsPath, 'utf8');
assert(videoPlayerContent.includes("class LiventraVideoPlayer"), "LiventraVideoPlayer chromeless renderer declared");
assert(videoPlayerContent.includes("showAudioOverlay"), "Muted autoplay audio overlay fallback present");

const timelineJsPath = path.join(projectRoot, 'assets', 'js', 'timeline-engine.js');
assert(fs.existsSync(timelineJsPath), "assets/js/timeline-engine.js exists");
const timelineJsContent = fs.readFileSync(timelineJsPath, 'utf8');
assert(timelineJsContent.includes("class LiventraTimelineEngine"), "LiventraTimelineEngine JS class declared");

const ctaJsPath = path.join(projectRoot, 'assets', 'js', 'cta-widgets.js');
assert(fs.existsSync(ctaJsPath), "assets/js/cta-widgets.js exists");
const ctaJsContent = fs.readFileSync(ctaJsPath, 'utf8');
assert(ctaJsContent.includes("class LiventraCTAEngine"), "LiventraCTAEngine JS class declared");

const cssPath = path.join(projectRoot, 'assets', 'css', 'frontend.css');
assert(fs.existsSync(cssPath), "assets/css/frontend.css exists");

// 8. Verifying Docker & Coolify Deployment Specs
console.log("\n--- 8. Verifying Docker & Coolify Deployment Specs ---");
const rootDockerPath = path.join(projectRoot, 'Dockerfile');
assert(fs.existsSync(rootDockerPath), "Root Dockerfile exists");
const rootDockerContent = fs.readFileSync(rootDockerPath, 'utf8');
assert(rootDockerContent.includes("EXPOSE 3000"), "Dockerfile exposes port 3000");
assert(rootDockerContent.includes('CMD ["npm", "start"]') || rootDockerContent.includes('CMD ["node"'), "Dockerfile defines start command");

const rootComposePath = path.join(projectRoot, 'docker-compose.yml');
assert(fs.existsSync(rootComposePath), "Root docker-compose.yml exists");

const rootPkgPath = path.join(projectRoot, 'package.json');
assert(fs.existsSync(rootPkgPath), "Root package.json exists");
const rootPkgContent = fs.readFileSync(rootPkgPath, 'utf8');
assert(rootPkgContent.includes("node services/api/server.js"), "Root package.json start script points to services/api/server.js");

const serverApiPath = path.join(projectRoot, 'services', 'api', 'server.js');
assert(fs.existsSync(serverApiPath), "services/api/server.js exists");
const serverApiContent = fs.readFileSync(serverApiPath, 'utf8');
assert(serverApiContent.includes("0.0.0.0"), "Express server explicitly binds to 0.0.0.0 network interface");

console.log("\n====================================================");
console.log(`📊 RESULTS: ${passedTests} / ${totalTests} Verifications Passed`);
console.log("====================================================");

if (passedTests === totalTests) {
    process.exit(0);
} else {
    process.exit(1);
}
