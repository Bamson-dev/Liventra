/**
 * Liventra CTA & Conversion Widgets (assets/js/cta-widgets.js)
 * Listens to TimelineEngine CTA events and renders interactive offer cards, timers, and scarcity widgets.
 */

(function(window) {
    'use strict';

    class LiventraCTAEngine {
        constructor(timelineEngine, containerId) {
            this.timelineEngine = timelineEngine;
            this.container = document.getElementById(containerId);

            this.init();
        }

        init() {
            if (!this.timelineEngine || !this.container) return;

            this.timelineEngine.registerHandler('cta_offer', payload => {
                this.renderOffer(payload);
            });
        }

        renderOffer(payload) {
            const title = payload.title || 'Special Limited Time Offer!';
            const subtitle = payload.subtitle || 'Claim your exclusive bonus before session ends.';
            const btnText = payload.button_text || 'Claim Special Offer Now';
            const btnUrl = payload.button_url || '#';
            const price = payload.price || '$97';
            const originalPrice = payload.original_price || '$497';

            const card = document.createElement('div');
            card.className = 'liventra-cta-card';
            card.innerHTML = `
                <div class="liventra-cta-badge">🔥 LIMITED TIME OFFER</div>
                <h3 class="liventra-cta-title">${title}</h3>
                <p class="liventra-cta-subtitle">${subtitle}</p>
                <div class="liventra-cta-pricing">
                    <span class="liventra-cta-price">${price}</span>
                    <span class="liventra-cta-original">${originalPrice}</span>
                </div>
                <a href="${btnUrl}" target="_blank" class="liventra-cta-btn">${btnText}</a>
            `;

            this.container.appendChild(card);
            card.scrollIntoView({ behavior: 'smooth' });
        }
    }

    window.LiventraCTAEngine = LiventraCTAEngine;
})(window);
