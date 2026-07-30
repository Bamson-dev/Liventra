/**
 * Liventra Developer Portal Application (assets/js/developer-portal.js)
 * PRD-014 Compliant Developer Portal & API Explorer.
 * Supports API key management, webhook testing & OpenAPI viewer.
 */

(function(window) {
    'use strict';

    class LiventraDeveloperPortal {
        constructor(options = {}) {
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.init();
        }

        init() {
            if (!this.container) return;
            this.container.innerHTML = `
                <div class="liventra-developer-portal">
                    <header class="liventra-dev-header">
                        <h2>🔌 Liventra Developer Portal & REST API Gateway</h2>
                        <span class="liventra-badge">OpenAPI 3.1 Ready</span>
                    </header>
                    <div class="liventra-dev-sections">
                        <section class="liventra-card">
                            <h3>🔑 API Keys & Personal Access Tokens</h3>
                            <button id="liventra-btn-genkey" class="liventra-btn liventra-btn-primary">+ Issue Secret Key</button>
                        </section>
                        <section class="liventra-card">
                            <h3>⚡ Webhook Subscriptions</h3>
                            <button id="liventra-btn-webhook" class="liventra-btn liventra-btn-secondary">+ Add Webhook Endpoint</button>
                        </section>
                        <section class="liventra-card">
                            <h3>📘 OpenAPI Specification</h3>
                            <a href="/wp-json/liventra/v1/openapi.json" target="_blank" class="liventra-link">View Raw OpenAPI 3.1 JSON Spec</a>
                        </section>
                    </div>
                </div>
            `;
        }
    }

    window.LiventraDeveloperPortal = LiventraDeveloperPortal;
})(window);
