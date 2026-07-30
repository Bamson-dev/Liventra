/**
 * Liventra Client Session Engine (assets/js/session-engine.js)
 * PRD-004 Compliant Authoritative Client Time Anchor, Drift Sync & State Machine.
 */

(function(window) {
    'use strict';

    class LiventraSessionEngine {
        constructor(config) {
            this.webinarId = config.webinarId;
            this.scheduledStart = config.scheduledStart; // UTC timestamp (seconds)
            this.videoDuration = config.videoDuration;   // Total duration (seconds)
            this.attendeeToken = config.attendeeToken || 'anon';
            this.restEndpoint = config.restEndpoint || '/wp-json/liventra/v1/session/sync';
            this.syncIntervalMs = config.syncIntervalMs || 10000;
            this.driftThresholdSec = config.driftThresholdSec || 2.5; // PRD-004 Section 13: +-2.5s drift

            this.state = 'waiting_room'; // waiting_room | live | ended
            this.elapsedSeconds = 0;
            this.lastSyncedTimelineOffset = 0;
            this.consecutiveFailures = 0;

            this.listeners = {};
            this.tickerId = null;
            this.heartbeatTimer = null;
        }

        on(event, callback) {
            if (!this.listeners[event]) {
                this.listeners[event] = [];
            }
            this.listeners[event].push(callback);
        }

        emit(event, payload) {
            if (this.listeners[event]) {
                this.listeners[event].forEach(cb => cb(payload));
            }
        }

        start() {
            this.syncWithServer();
            this.startLocalTicker();
            
            this.heartbeatTimer = setInterval(() => {
                this.syncWithServer();
            }, this.syncIntervalMs);
        }

        stop() {
            if (this.tickerId) {
                cancelAnimationFrame(this.tickerId);
            }
            if (this.heartbeatTimer) {
                clearInterval(this.heartbeatTimer);
            }
        }

        startLocalTicker() {
            let lastTick = performance.now();

            const tick = (now) => {
                const deltaSec = (now - lastTick) / 1000;
                lastTick = now;

                if (this.state === 'live') {
                    this.elapsedSeconds += deltaSec;
                    if (this.elapsedSeconds >= this.videoDuration) {
                        this.state = 'ended';
                        this.emit('session.ended', { state: 'ended', elapsedSeconds: this.videoDuration });
                    } else {
                        this.emit('tick', {
                            elapsedSeconds: this.elapsedSeconds,
                            formattedTime: this.formatTime(this.elapsedSeconds)
                        });
                    }
                } else if (this.state === 'waiting_room') {
                    const localNowSec = Math.floor(Date.now() / 1000);
                    const remaining = Math.max(0, this.scheduledStart - localNowSec);
                    
                    if (remaining <= 0) {
                        this.state = 'live';
                        this.elapsedSeconds = Math.abs(this.scheduledStart - localNowSec);
                        this.emit('session.started', { state: 'live', elapsedSeconds: this.elapsedSeconds });
                    } else {
                        this.emit('waiting_tick', {
                            remainingSeconds: remaining,
                            formattedCountdown: this.formatTime(remaining)
                        });
                    }
                }

                this.tickerId = requestAnimationFrame(tick);
            };

            this.tickerId = requestAnimationFrame(tick);
        }

        async syncWithServer() {
            try {
                const response = await fetch(this.restEndpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        webinar_id: this.webinarId,
                        scheduled_start: this.scheduledStart,
                        video_duration: this.videoDuration,
                        attendee_token: this.attendeeToken,
                        client_elapsed: Math.floor(this.elapsedSeconds),
                        last_synced_offset: this.lastSyncedTimelineOffset
                    })
                });

                const result = await response.json();
                if (result.success && result.data) {
                    this.consecutiveFailures = 0;
                    this.handleSyncResponse(result.data);
                } else {
                    this.handleNetworkDegradation();
                }
            } catch (err) {
                this.handleNetworkDegradation(err);
            }
        }

        handleNetworkDegradation(err) {
            this.consecutiveFailures++;
            console.warn(`[Liventra SessionEngine] Network degraded (${this.consecutiveFailures} consecutive retries):`, err ? err.message : 'Invalid response');
            this.emit('network_degraded', { failures: this.consecutiveFailures });
        }

        handleSyncResponse(data) {
            const previousState = this.state;
            this.state = data.state;

            if (data.state === 'live') {
                const serverElapsed = data.elapsed_seconds;
                const drift = Math.abs(this.elapsedSeconds - serverElapsed);

                // PRD-004 Section 13: Force seek if drift exceeds threshold (2.5s)
                if (data.requires_seek || drift > this.driftThresholdSec || previousState !== 'live') {
                    this.elapsedSeconds = serverElapsed;
                    this.emit('session.resynchronized', { elapsedSeconds: serverElapsed, driftSec: drift });
                }
            }

            if (previousState !== this.state) {
                this.emit('state_change', { state: this.state, elapsedSeconds: this.elapsedSeconds });
                if (this.state === 'live') this.emit('session.live', { elapsedSeconds: this.elapsedSeconds });
                if (this.state === 'ended') this.emit('session.ended', { elapsedSeconds: this.videoDuration });
            }

            if (data.events && data.events.length > 0) {
                data.events.forEach(evt => {
                    this.emit('timeline_event', evt);
                    if (evt.trigger_second && evt.trigger_second > this.lastSyncedTimelineOffset) {
                        this.lastSyncedTimelineOffset = evt.trigger_second;
                    }
                });
            }
        }

        formatTime(seconds) {
            const sec = Math.floor(seconds);
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            const h = Math.floor(m / 60);
            const displayM = m % 60;

            if (h > 0) {
                return `${String(h).padStart(2, '0')}:${String(displayM).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            }
            return `${String(displayM).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }
    }

    window.LiventraSessionEngine = LiventraSessionEngine;
})(window);
