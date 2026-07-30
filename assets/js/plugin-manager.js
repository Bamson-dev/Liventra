/**
 * Liventra Plugin SDK & Marketplace Browser (assets/js/plugin-manager.js)
 * Interactive Plugin Lifecycle & Marketplace Component.
 */

(function(window) {
    'use strict';

    class LiventraPluginManager {
        constructor(options = {}) {
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.installedPlugins = [
                { id: 'plg_zapier', name: 'Zapier Automation Pro', author: 'Liventra Core Team', version: '1.2.0', active: true },
                { id: 'plg_hubspot', name: 'HubSpot CRM Sync', author: 'HubSpot Ecosystem', version: '2.1.0', active: false }
            ];

            this.init();
        }

        init() {
            if (!this.container) return;
            this.render();
        }

        render() {
            this.container.innerHTML = `
                <div class="liventra-admin-container">
                    <header class="liventra-header">
                        <div class="liventra-header-title">
                            <h2>🧩 Plugin SDK & Marketplace Catalog</h2>
                            <span class="liventra-badge liventra-badge-success">Sandbox Isolated</span>
                        </div>
                    </header>
                    <div style="padding:24px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                            <h3 style="margin:0;">Installed Developer Plugins</h3>
                            <button id="btn-inspect-sdk" class="liventra-btn liventra-btn-secondary">🛠️ SDK Hook Inspector</button>
                        </div>
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:16px;">
                            ${this.installedPlugins.map(p => `
                                <div class="liventra-card">
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                        <h4 style="margin:0 0 6px 0; font-size:16px;">${p.name}</h4>
                                        <span class="liventra-badge ${p.active ? 'liventra-badge-success' : 'liventra-badge-warning'}">${p.active ? 'Active' : 'Disabled'}</span>
                                    </div>
                                    <p style="margin:0 0 12px 0; font-size:12px; color:var(--lv-text-muted);">Author: ${p.author} | Version: v${p.version}</p>
                                    <div style="display:flex; gap:8px;">
                                        <button class="liventra-btn ${p.active ? 'liventra-btn-secondary' : 'liventra-btn-primary'}" style="font-size:11px; padding:4px 10px;" onclick="alert('${p.name} state toggled!')">${p.active ? 'Deactivate' : 'Activate'}</button>
                                        <button class="liventra-btn liventra-btn-secondary" style="font-size:11px; padding:4px 10px;" onclick="alert('Viewing Sandboxed Permissions for ${p.name}')">Permissions</button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;

            const btnInspect = this.container.querySelector('#btn-inspect-sdk');
            if (btnInspect) btnInspect.addEventListener('click', () => alert('SDK Inspector: 14 EventBus Subscriptions Active, 4 REST Hooks Registered.'));
        }
    }

    window.LiventraPluginManager = LiventraPluginManager;
})(window);
