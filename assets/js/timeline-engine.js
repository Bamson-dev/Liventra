/**
 * Liventra Client Timeline Engine (assets/js/timeline-engine.js)
 * PRD-006 Part 13 Compliant Central Event Scheduler.
 * Receives spooled JIT events, maintains execution state, prevents duplicates & handles reconnect catch-up.
 */

(function(window) {
    'use strict';

    class LiventraTimelineEngine {
        constructor(sessionEngine, options = {}) {
            this.sessionEngine = sessionEngine;
            this.handlers = {};
            this.executedUuids = new Set();
            this.lastSyncedOffset = 0;

            this.init();
        }

        init() {
            if (!this.sessionEngine) return;

            // Listen strictly to SessionEngine events
            this.sessionEngine.on('timeline_event', eventData => {
                this.processEvent(eventData);
            });

            this.sessionEngine.on('session.resynchronized', data => {
                this.handleReconnectRestore(data);
            });
        }

        registerHandler(eventType, handlerCallback) {
            if (!this.handlers[eventType]) {
                this.handlers[eventType] = [];
            }
            this.handlers[eventType].push(handlerCallback);
        }

        processEvent(eventData) {
            const uuid = eventData.uuid || eventData.id || `evt-${eventData.trigger_second || eventData.trigger_time}-${eventData.event_type}`;
            const isReplayable = eventData.replayable !== undefined ? eventData.replayable : true;

            // PRD-006 Part 5: Exactly-Once Execution for non-replayable events
            if (!isReplayable && this.executedUuids.has(uuid)) {
                console.log(`[Liventra TimelineEngine] Skipped duplicate execution of non-replayable event [${uuid}]`);
                return;
            }

            this.executedUuids.add(uuid);
            this.dispatchEvent(eventData);
        }

        dispatchEvent(eventData) {
            const type = eventData.event_type || 'cta';
            const payload = eventData.payload || eventData.event_payload || {};

            if (this.handlers[type]) {
                this.handlers[type].forEach(handler => {
                    try {
                        handler(payload, eventData);
                    } catch (err) {
                        console.error(`[Liventra TimelineEngine] Handler failure on event [${type}]:`, err);
                    }
                });
            }
        }

        handleReconnectRestore(data) {
            console.log(`[Liventra TimelineEngine] Restoring timeline state at offset [${data.elapsedSeconds}s]`);
            // Trigger persistent element restoration if needed
        }
    }

    window.LiventraTimelineEngine = LiventraTimelineEngine;
})(window);
