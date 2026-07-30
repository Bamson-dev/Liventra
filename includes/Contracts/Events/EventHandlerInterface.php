<?php
namespace Liventra\Contracts\Events;

use Liventra\Entities\TimelineEvent;
use Liventra\Events\EventResult;
use Liventra\Events\HandlerMetadata;

if ( ! defined( 'ABSPATH' ) && ! defined( 'LIVENTRA_TEST_SUITE' ) ) {
	exit;
}

/**
 * Interface EventHandlerInterface
 * Contract for Pluggable Subsystem and Third-Party Event Handlers (Part 1)
 */
interface EventHandlerInterface {

	/**
	 * Check if handler supports a given event type string
	 *
	 * @param string $eventType Event type identifier (e.g. 'cta.show', 'poll.open').
	 * @return bool
	 */
	public function supports( string $eventType ): bool;

	/**
	 * Get handler priority weight (higher numbers execute earlier)
	 *
	 * @return int
	 */
	public function priority(): int;

	/**
	 * Handle execution of a synchronized TimelineEvent
	 *
	 * @param TimelineEvent $event Event entity.
	 * @return EventResult Execution result object.
	 */
	public function handle( TimelineEvent $event ): EventResult;

	/**
	 * Get handler metadata for discovery and diagnostics
	 *
	 * @return HandlerMetadata
	 */
	public function metadata(): HandlerMetadata;
}
