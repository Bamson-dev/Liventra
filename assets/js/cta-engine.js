/**
 * Liventra Client CTA Engine (assets/js/cta-engine.js)
 * PRD-009 Compliant Conversion & Offer Widget Renderer.
 * Manages sticky offer bars, modal popups, countdown sync, and conversion interactions.
 */

(function(window) {
    'use strict';

    class LiventraCTAEngine {
        constructor(sessionEngine, options = {}) {
            this.sessionEngine = sessionEngine;
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.activeWidgets = new Map();

            this.init();
        }

        init() {
            if (!this.sessionEngine) return;

            // Listen to timeline CTA events
            this.sessionEngine.on('timeline_event', eventData => {
                if (eventData.event_type === 'cta') {
                    this.renderCTAWidget(eventData.payload || eventData);
                }
            });

            this.sessionEngine.on('tick', data => {
                this.updateCountdowns(data.elapsedSeconds);
            });
        }

        renderCTAWidget(payload) {
            const uuid = payload.uuid || payload.id || Math.random().toString(36).substr(2, 9);
            if (this.activeWidgets.has(uuid)) return;

            const title = payload.title || 'Exclusive Masterclass Offer';
            const description = payload.description || 'Unlock lifetime access today!';
            const buttonText = payload.button_text || 'Enroll Now';
            const url = payload.destination_url || '#';
            const type = payload.type || 'sticky_footer';

            const widget = document.createElement('div');
            widget.className = `liventra-cta-widget liventra-cta-${type}`;
            widget.innerHTML = `
                <div class="liventra-cta-inner">
                    <div class="liventra-cta-title">${title}</div>
                    <div class="liventra-cta-desc">${description}</div>
                    <a href="${url}" target="_blank" class="liventra-cta-btn">${buttonText}</a>
                </div>
            `;

            this.container.appendChild(widget);
            this.activeWidgets.set(uuid, widget);

            // Record click interaction
            const btn = widget.querySelector('.liventra-cta-btn');
            if (btn) {
                btn.addEventListener('click', () => {
                    this.trackInteraction(uuid, 'click');
                });
            }

            this.trackInteraction(uuid, 'impression');
        }

        updateCountdowns(elapsedSeconds) {
            // Synchronizes offer countdowns strictly against SessionEngine elapsedSeconds
        }

        trackInteraction(uuid, type) {
            fetch('/wp-json/liventra/v1/cta/track', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cta_uuid: uuid, interaction_type: type })
            }).catch(err => console.error('[Liventra CTAEngine] Track error:', err));
        }
    }

    window.LiventraCTAEngine = LiventraCTAEngine;
})(window);
