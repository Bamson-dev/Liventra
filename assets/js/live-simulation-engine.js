/**
 * Liventra Client Live Simulation Engine (assets/js/live-simulation-engine.js)
 * PRD-005 Compliant Experience Orchestrator Layer.
 * Manages priority collision queues, purchase popups, scarcity countdowns, polls & dynamic viewer counts.
 */

(function(window) {
    'use strict';

    class LiventraLiveSimulationEngine {
        constructor(sessionEngine, options = {}) {
            this.sessionEngine = sessionEngine;
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.viewerCountElement = options.viewerCountId ? document.getElementById(options.viewerCountId) : null;
            
            this.activeOverlays = [];
            this.maxConcurrentOverlays = options.maxConcurrentOverlays || 2;
            this.eventQueue = [];

            this.init();
        }

        init() {
            if (!this.sessionEngine) return;

            // Bind SessionEngine events
            this.sessionEngine.on('timeline_event', evt => {
                this.handleSimulationEvent(evt);
            });

            this.sessionEngine.on('tick', data => {
                this.updateViewerCount(data.elapsedSeconds);
            });

            this.sessionEngine.on('waiting_tick', data => {
                this.updateWaitingViewerCount(data.remainingSeconds);
            });
        }

        handleSimulationEvent(eventData) {
            const eventType = eventData.event_type || 'notification';
            const payload = eventData.payload || eventData.event_payload || {};
            const priority = eventData.priority || this.getPriorityForType(eventType);

            const item = {
                id: eventData.event_id || eventData.id || Math.random().toString(36).substr(2, 9),
                type: eventType,
                payload: payload,
                priority: priority,
                timestamp: Date.now()
            };

            this.enqueueEvent(item);
        }

        enqueueEvent(item) {
            this.eventQueue.push(item);
            // PRD-005 Section 16: Sort queue by priority
            this.eventQueue.sort((a, b) => b.priority - a.priority);

            this.processQueue();
        }

        processQueue() {
            while (this.eventQueue.length > 0 && this.activeOverlays.length < this.maxConcurrentOverlays) {
                const eventItem = this.eventQueue.shift();
                this.renderSimulationOverlay(eventItem);
            }
        }

        renderSimulationOverlay(item) {
            if (item.type === 'purchase' || item.type === 'notification') {
                this.renderPurchaseNotification(item);
            } else if (item.type === 'poll') {
                this.renderPollCard(item);
            } else if (item.type === 'scarcity') {
                this.renderScarcityBanner(item);
            }
        }

        renderPurchaseNotification(item) {
            const payload = item.payload;
            const name = payload.name || 'Joshua';
            const location = payload.location || 'Austin, TX';
            const product = payload.product || 'Liventra Pro License';
            const duration = payload.duration_ms || 4500;

            const popup = document.createElement('div');
            popup.className = 'liventra-purchase-popup';
            popup.innerHTML = `
                <div class="liventra-popup-icon">🛒</div>
                <div class="liventra-popup-content">
                    <div class="liventra-popup-title"><strong>${name}</strong> from ${location}</div>
                    <div class="liventra-popup-desc">just enrolled in <em>${product}</em></div>
                </div>
            `;

            this.container.appendChild(popup);
            this.activeOverlays.push(popup);

            setTimeout(() => {
                popup.classList.add('liventra-popup-fadeout');
                setTimeout(() => {
                    popup.remove();
                    this.activeOverlays = this.activeOverlays.filter(el => el !== popup);
                    this.processQueue();
                }, 300);
            }, duration);
        }

        renderPollCard(item) {
            const payload = item.payload;
            const question = payload.question || 'Quick Poll: What is your primary webinar goal?';
            const options = payload.options || ['Automate Sales', 'Save Time', 'Higher Conversions'];

            const card = document.createElement('div');
            card.className = 'liventra-poll-card';
            card.innerHTML = `
                <div class="liventra-poll-header">📊 LIVE POLL</div>
                <h4 class="liventra-poll-question">${question}</h4>
                <div class="liventra-poll-options">
                    ${options.map(opt => `<button class="liventra-poll-opt-btn">${opt}</button>`).join('')}
                </div>
            `;

            this.container.appendChild(card);
            this.activeOverlays.push(card);

            const buttons = card.querySelectorAll('.liventra-poll-opt-btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    buttons.forEach(b => b.disabled = true);
                    btn.classList.add('selected');
                    setTimeout(() => {
                        card.remove();
                        this.activeOverlays = this.activeOverlays.filter(el => el !== card);
                        this.processQueue();
                    }, 2000);
                });
            });
        }

        renderScarcityBanner(item) {
            const payload = item.payload;
            const message = payload.message || '⚠️ Only 7 spots remaining at this discount!';

            const banner = document.createElement('div');
            banner.className = 'liventra-scarcity-banner';
            banner.innerHTML = `<span>${message}</span>`;

            this.container.appendChild(banner);
            this.activeOverlays.push(banner);

            setTimeout(() => {
                banner.remove();
                this.activeOverlays = this.activeOverlays.filter(el => el !== banner);
                this.processQueue();
            }, 6000);
        }

        updateViewerCount(elapsedSeconds) {
            if (!this.viewerCountElement) return;

            // PRD-005 Section 6: Dynamic curve simulation formula
            const base = 85;
            const max = 340;
            const totalSec = this.sessionEngine.videoDuration || 3600;
            const progress = Math.min(1.0, elapsedSeconds / totalSec);
            const count = Math.floor(base + (max - base) * Math.sin(progress * (Math.PI / 2)));

            this.viewerCountElement.textContent = `${count} watching now`;
        }

        updateWaitingViewerCount(remainingSeconds) {
            if (!this.viewerCountElement) return;
            this.viewerCountElement.textContent = `42 attendees waiting`;
        }

        getPriorityForType(type) {
            switch(type) {
                case 'cta': return 90;
                case 'poll': return 80;
                case 'purchase':
                case 'notification': return 70;
                case 'scarcity': return 60;
                case 'chat': return 50;
                default: return 30;
            }
        }
    }

    window.LiventraLiveSimulationEngine = LiventraLiveSimulationEngine;
})(window);
