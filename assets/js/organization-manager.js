/**
 * Liventra Organization & Multi-Tenant Manager (assets/js/organization-manager.js)
 * Interactive SaaS Organization Switcher & White-Label Customizer.
 */

(function(window) {
    'use strict';

    class LiventraOrganizationManager {
        constructor(options = {}) {
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.activeOrg = 'Acme Enterprise Corp';
            this.workspaces = [
                { id: 'ws_mkt', name: 'Marketing Launch Workspace', webinars: 12, status: 'isolated' },
                { id: 'ws_sales', name: 'Sales Demo Workspace', webinars: 45, status: 'isolated' }
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
                            <h2>🏢 Enterprise Organizations & Tenancy</h2>
                            <span class="liventra-badge liventra-badge-primary">${this.activeOrg}</span>
                        </div>
                        <div class="liventra-header-actions">
                            <button id="btn-create-ws" class="liventra-btn liventra-btn-primary">+ Create Workspace</button>
                            <button id="btn-invite-member" class="liventra-btn liventra-btn-secondary">✉️ Invite Member</button>
                        </div>
                    </header>
                    <div style="padding:24px;">
                        <div class="liventra-metrics-grid">
                            <div class="liventra-card">
                                <div class="liventra-card-title">Tenant Domain</div>
                                <div class="liventra-card-value" style="font-size:20px;">live.acme.com</div>
                                <div class="liventra-card-subtext">Custom Domain SSL Active</div>
                            </div>
                            <div class="liventra-card">
                                <div class="liventra-card-title">Isolated Workspaces</div>
                                <div class="liventra-card-value">${this.workspaces.length}</div>
                                <div class="liventra-card-subtext">Data & Assets Segmented</div>
                            </div>
                            <div class="liventra-card">
                                <div class="liventra-card-title">Organization Members</div>
                                <div class="liventra-card-value">4 Users</div>
                                <div class="liventra-card-subtext">SAML 2.0 SSO Enforced</div>
                            </div>
                        </div>

                        <div class="liventra-card" style="margin-top:24px;">
                            <h3>Isolated Workspaces</h3>
                            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-top:16px;">
                                ${this.workspaces.map(w => `
                                    <div style="background:var(--lv-bg); border:1px solid var(--lv-border); padding:16px; border-radius:8px;">
                                        <h4 style="margin:0 0 6px 0; font-size:15px; color:var(--lv-text);">${w.name}</h4>
                                        <p style="margin:0 0 12px 0; font-size:12px; color:var(--lv-text-muted);">${w.webinars} Webinars | ${w.status} Scope</p>
                                        <button class="liventra-btn liventra-btn-secondary" style="font-size:11px; padding:4px 10px;" onclick="alert('Switched to ${w.name}')">Switch Workspace</button>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const btnWs = this.container.querySelector('#btn-create-ws');
            if (btnWs) btnWs.addEventListener('click', () => {
                this.workspaces.push({ id: 'ws_' + Date.now(), name: 'New Regional Workspace', webinars: 0, status: 'isolated' });
                this.render();
            });

            const btnInv = this.container.querySelector('#btn-invite-member');
            if (btnInv) btnInv.addEventListener('click', () => alert('Invitation Sent to New Organization Member!'));
        }
    }

    window.LiventraOrganizationManager = LiventraOrganizationManager;
})(window);
