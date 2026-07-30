/**
 * Liventra Client Analytics Collector Engine (assets/js/analytics-engine.js)
 * PRD-011 Compliant Event Intelligence Collector.
 * Batches telemetry events, retries transient failures & minimizes network overhead.
 */

(function(window) {
    'use strict';

    class LiventraAnalyticsEngine {
        constructor(options = {}) {
            this.webinarId = options.webinarId || 1;
            this.attendeeId = options.attendeeId || 0;
            this.batchQueue = [];
            this.flushIntervalMs = options.flushIntervalMs || 5000;
            this.maxBatchSize = options.maxBatchSize || 20;

            this.init();
        }

        init() {
            // Periodic flush timer
            setInterval(() => {
                this.flush();
            }, this.flushIntervalMs);

            // Flush before page unload
            window.addEventListener('beforeunload', () => {
                this.flush(true);
            });
        }

        track(eventType, payload = {}) {
            const eventItem = {
                uuid: Math.random().toString(36).substr(2, 9),
                webinar_id: this.webinarId,
                attendee_id: this.attendeeId,
                event_type: eventType,
                payload: payload,
                timestamp: new Date().toISOString()
            };

            this.batchQueue.push(eventItem);

            if (this.batchQueue.length >= this.maxBatchSize) {
                this.flush();
            }
        }

        flush(isBeacon = false) {
            if (this.batchQueue.length === 0) return;

            const eventsToFlush = [...this.batchQueue];
            this.batchQueue = [];

            const endpoint = '/wp-json/liventra/v1/analytics/batch';
            const payloadStr = JSON.stringify({ events: eventsToFlush });

            if (isBeacon && navigator.sendBeacon) {
                navigator.sendBeacon(endpoint, payloadStr);
            } else {
                fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: payloadStr
                }).catch(err => {
                    console.warn('[Liventra AnalyticsEngine] Flush failed, re-queueing events:', err);
                    this.batchQueue.unshift(...eventsToFlush); // Re-queue failed events
                });
            }
        }
    }

    window.LiventraAnalyticsEngine = LiventraAnalyticsEngine;
})(window);
