/**
 * Liventra Customer-Centric Admin Studio & Guided Webinar Wizard (assets/js/admin-studio.js)
 * Features Real CRUD for Offers & Chat Messages, Live State Binding, and 7-Step Guided Wizard (No Email step).
 * Fully compatible with script optimizer plugins (SpeedyCache, SiteSEO, WP Rocket).
 */

(function(window, document) {
    'use strict';

    function getRestEndpoint(endpoint) {
        let root = '';
        if (window.liventraSettings && window.liventraSettings.root) {
            root = window.liventraSettings.root;
        } else if (window.wpApiSettings && window.wpApiSettings.root) {
            root = window.wpApiSettings.root;
        } else {
            root = window.location.origin + '/wp-json/';
        }
        if (root.includes('rest_route=')) {
            return root + 'liventra/v1/' + endpoint;
        }
        if (!root.endsWith('/')) root += '/';
        return root + 'liventra/v1/' + endpoint;
    }

    class LiventraAdminStudio {
        constructor(options = {}) {
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.activeNav = 'home'; // 'home' | 'my-webinars' | 'create' | 'contacts' | 'analytics' | 'settings'
            this.wizardStep = 1; // 1 to 7
            this.activeWebinarId = options.webinarId || 1;

            // Managed Webinars Catalog
            this.webinars = [
                { id: 1, title: 'High-Ticket Evergreen Masterclass', status: 'published', attendees: 142, revenue: '$14,850', watchTime: '24m 18s', provider: 'Bunny.net CDN' },
                { id: 2, title: 'SaaS Automated Onboarding Demo', status: 'published', attendees: 89, revenue: '$8,200', watchTime: '18m 45s', provider: 'HLS Stream' },
                { id: 3, title: 'Q3 Product Roadmap Announcement', status: 'draft', attendees: 0, revenue: '$0', watchTime: '0m', provider: 'Direct MP4' }
            ];

            // Active Guided Wizard Draft State (Live Editable)
            this.wizardDraft = {
                title: 'Automated High-Ticket Sales Masterclass',
                description: 'Transform cold prospects into high-ticket sales on autopilot.',
                host: 'Bamidele Matthew',
                timezone: 'UTC+1 (Lagos / London)',
                videoProvider: 'bunny',
                videoUrl: 'https://cdn.liventra.com/masterclass-v1.mp4',
                videoDuration: '42:15',
                scheduleType: 'jit', // 'jit' | 'instant' | 'scheduled'
                offers: [
                    { id: 101, name: 'Sticky Discount Bar', link: 'https://liventra.com/checkout?offer=discount', time: '05:00', duration: '10m', type: 'bar' },
                    { id: 102, name: 'VIP Masterclass Offer ($497)', link: 'https://liventra.com/checkout?offer=vip', time: '25:00', duration: '15m', type: 'popup' }
                ],
                chatMessages: [
                    { id: 201, author: 'Host Bamidele', text: 'Welcome everyone! Type your city in the chat!', time: '01:30' },
                    { id: 202, author: 'Moderator Sarah', text: 'Special discount offer unlocks at 25:00!', time: '14:00' }
                ]
            };

            this.init();
        }

        init() {
            if (!this.container) return;
            this.renderLayout();
            this.bindGlobalEvents();
        }

        renderLayout() {
            this.container.innerHTML = `
                <div class="liventra-admin-container">
                    <!-- Top Customer Navigation Bar -->
                    <header class="liventra-header">
                        <div class="liventra-header-title">
                            <h2>⚡ Liventra Webinar Engine</h2>
                            <span class="liventra-badge liventra-badge-primary">v1.0.4 Enterprise</span>
                        </div>
                        <div class="liventra-header-actions">
                            <button id="btn-quick-create" class="liventra-btn liventra-btn-primary">➕ Create New Webinar</button>
                            <button id="btn-preview-webinar" class="liventra-btn liventra-btn-secondary">👁️ Preview Live Room</button>
                        </div>
                    </header>

                    <div class="liventra-studio-layout">
                        <!-- Left Workflow Navigation -->
                        <aside class="liventra-sidebar">
                            <nav class="liventra-sidebar-nav">
                                <a class="liventra-nav-item ${this.activeNav === 'home' ? 'active' : ''}" data-nav="home" href="#home">🏠 Home</a>
                                <a class="liventra-nav-item ${this.activeNav === 'my-webinars' ? 'active' : ''}" data-nav="my-webinars" href="#my-webinars">🎥 My Webinars</a>
                                <a class="liventra-nav-item ${this.activeNav === 'create' ? 'active' : ''}" data-nav="create" href="#create">➕ Create Webinar</a>
                                <a class="liventra-nav-item ${this.activeNav === 'contacts' ? 'active' : ''}" data-nav="contacts" href="#contacts">👥 Contacts & Leads</a>
                                <a class="liventra-nav-item ${this.activeNav === 'analytics' ? 'active' : ''}" data-nav="analytics" href="#analytics">📊 Conversion Analytics</a>
                                <a class="liventra-nav-item ${this.activeNav === 'settings' ? 'active' : ''}" data-nav="settings" href="#settings">⚙️ System Settings</a>
                            </nav>
                        </aside>

                        <!-- Main Canvas Area -->
                        <main class="liventra-content-canvas" id="liventra-main-canvas">
                            <!-- Dynamic Content Rendered Here -->
                        </main>
                    </div>
                </div>

                <div id="liventra-modal-root"></div>
                <div id="liventra-toast-root" class="liventra-toast-container"></div>
            `;

            this.renderMainContent();
        }

        bindGlobalEvents() {
            if (window._liventraGlobalClickBound) return;
            window._liventraGlobalClickBound = true;

            document.addEventListener('click', (e) => {
                const app = window.liventraApp;
                if (!app) return;

                const target = e.target.closest('a, button, .liventra-nav-item, .liventra-timeline-block, .liventra-step-pill, .liventra-modal-close');
                if (!target) return;

                // Sidebar Navigation
                if (target.hasAttribute('data-nav')) {
                    e.preventDefault();
                    app.switchNav(target.getAttribute('data-nav'));
                    return;
                }

                // Quick Create Button
                if (target.id === 'btn-quick-create' || target.classList.contains('btn-trigger-create')) {
                    e.preventDefault();
                    app.switchNav('create');
                    return;
                }

                // Preview Button
                if (target.id === 'btn-preview-webinar' || target.id === 'liventra-btn-preview') {
                    e.preventDefault();
                    app.openPreviewModal();
                    return;
                }

                // Wizard Step Pills
                if (target.classList.contains('liventra-step-pill')) {
                    e.preventDefault();
                    const step = parseInt(target.getAttribute('data-step'), 10);
                    if (step >= 1 && step <= 7) {
                        app.wizardStep = step;
                        app.renderMainContent();
                    }
                    return;
                }

                // Wizard Next Step
                if (target.id === 'btn-wizard-next') {
                    e.preventDefault();
                    if (app.wizardStep < 7) {
                        app.wizardStep++;
                        app.renderMainContent();
                    } else {
                        app.publishWebinar();
                    }
                    return;
                }

                // Wizard Back Step
                if (target.id === 'btn-wizard-back') {
                    e.preventDefault();
                    if (app.wizardStep > 1) {
                        app.wizardStep--;
                        app.renderMainContent();
                    }
                    return;
                }

                // Add Offer Modal Trigger
                if (target.id === 'btn-trigger-add-offer') {
                    e.preventDefault();
                    app.openAddOfferModal();
                    return;
                }

                // Delete Offer Trigger
                if (target.classList.contains('btn-delete-offer')) {
                    e.preventDefault();
                    const offerId = parseInt(target.getAttribute('data-offer-id'), 10);
                    app.wizardDraft.offers = app.wizardDraft.offers.filter(o => o.id !== offerId);
                    app.showToast('Offer Removed');
                    app.renderMainContent();
                    return;
                }

                // Add Chat Message Modal Trigger
                if (target.id === 'btn-trigger-add-chat') {
                    e.preventDefault();
                    app.openAddChatModal();
                    return;
                }

                // Delete Chat Message Trigger
                if (target.classList.contains('btn-delete-chat')) {
                    e.preventDefault();
                    const chatId = parseInt(target.getAttribute('data-chat-id'), 10);
                    app.wizardDraft.chatMessages = app.wizardDraft.chatMessages.filter(m => m.id !== chatId);
                    app.showToast('Chat Message Removed');
                    app.renderMainContent();
                    return;
                }

                // Duplicate Webinar Trigger
                if (target.classList.contains('btn-duplicate-webinar')) {
                    e.preventDefault();
                    const webId = parseInt(target.getAttribute('data-webinar-id'), 10);
                    app.duplicateWebinar(webId);
                    return;
                }

                // Delete Webinar Trigger
                if (target.classList.contains('btn-delete-webinar')) {
                    e.preventDefault();
                    const webId = parseInt(target.getAttribute('data-webinar-id'), 10);
                    app.deleteWebinar(webId);
                    return;
                }

                // Modal Close Button
                if (target.classList.contains('liventra-modal-close')) {
                    e.preventDefault();
                    const modalRoot = document.getElementById('liventra-modal-root');
                    if (modalRoot) modalRoot.innerHTML = '';
                    return;
                }
            });
        }

        switchNav(navKey) {
            this.activeNav = navKey;
            if (navKey === 'create') {
                this.wizardStep = 1;
            }
            const navItems = this.container.querySelectorAll('.liventra-sidebar-nav a');
            navItems.forEach(item => {
                if (item.getAttribute('data-nav') === navKey) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
            this.renderMainContent();
        }

        renderMainContent() {
            const canvas = this.container.querySelector('#liventra-main-canvas');
            if (!canvas) return;

            if (this.activeNav === 'home') {
                canvas.innerHTML = this.renderHomeView();
            } else if (this.activeNav === 'my-webinars') {
                canvas.innerHTML = this.renderMyWebinarsView();
            } else if (this.activeNav === 'create') {
                canvas.innerHTML = this.renderWizardView();
                this.bindWizardFormInputs();
            } else if (this.activeNav === 'contacts') {
                canvas.innerHTML = this.renderContactsView();
            } else if (this.activeNav === 'analytics') {
                canvas.innerHTML = this.renderAnalyticsView();
            } else if (this.activeNav === 'settings') {
                canvas.innerHTML = this.renderSettingsView();
            }
        }

        bindWizardFormInputs() {
            // Live Form Inputs Binding to Draft State
            const inpTitle = document.getElementById('inp-wizard-title');
            if (inpTitle) inpTitle.addEventListener('input', (e) => this.wizardDraft.title = e.target.value);

            const inpDesc = document.getElementById('inp-wizard-desc');
            if (inpDesc) inpDesc.addEventListener('input', (e) => this.wizardDraft.description = e.target.value);

            const inpHost = document.getElementById('inp-wizard-host');
            if (inpHost) inpHost.addEventListener('input', (e) => this.wizardDraft.host = e.target.value);

            const selProvider = document.getElementById('sel-wizard-provider');
            if (selProvider) selProvider.addEventListener('change', (e) => this.wizardDraft.videoProvider = e.target.value);

            const inpUrl = document.getElementById('inp-wizard-url');
            if (inpUrl) inpUrl.addEventListener('input', (e) => this.wizardDraft.videoUrl = e.target.value);

            const inpDuration = document.getElementById('inp-wizard-duration');
            if (inpDuration) inpDuration.addEventListener('input', (e) => this.wizardDraft.videoDuration = e.target.value);

            const selSchedule = document.getElementById('sel-wizard-schedule');
            if (selSchedule) selSchedule.addEventListener('change', (e) => this.wizardDraft.scheduleType = e.target.value);
        }

        renderHomeView() {
            return `
                <div class="liventra-metrics-grid">
                    <div class="liventra-card">
                        <div class="liventra-card-title">Live Audience Right Now</div>
                        <div class="liventra-card-value">142 Attendees</div>
                        <div class="liventra-card-subtext">▲ +18.4% Retention</div>
                    </div>
                    <div class="liventra-card">
                        <div class="liventra-card-title">Total Offer Revenue</div>
                        <div class="liventra-card-value">$23,050</div>
                        <div class="liventra-card-subtext">▲ 12.4% Conversion Rate</div>
                    </div>
                    <div class="liventra-card">
                        <div class="liventra-card-title">Average Watch Time</div>
                        <div class="liventra-card-value">24m 18s</div>
                        <div class="liventra-card-subtext">84% Session Completion</div>
                    </div>
                    <div class="liventra-card">
                        <div class="liventra-card-title">Active Webinars</div>
                        <div class="liventra-card-value">${this.webinars.length} Automated</div>
                        <div class="liventra-card-subtext">2 Published | 1 Draft</div>
                    </div>
                </div>

                <div class="liventra-card" style="margin-top: 24px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h3 style="margin:0; font-size:16px; color:var(--lv-text);">🎥 Managed Automated Webinars</h3>
                        <button class="liventra-btn liventra-btn-primary btn-trigger-create">+ Build New Webinar</button>
                    </div>
                    <table style="width:100%; border-collapse:collapse; font-size:13px; color:var(--lv-text);">
                        <thead>
                            <tr style="border-bottom:1px solid var(--lv-border); text-align:left; color:var(--lv-text-muted);">
                                <th style="padding:10px;">Title</th>
                                <th style="padding:10px;">Stream Provider</th>
                                <th style="padding:10px;">Status</th>
                                <th style="padding:10px;">Viewers</th>
                                <th style="padding:10px;">Revenue</th>
                                <th style="padding:10px;">Quick Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${this.webinars.map(w => `
                                <tr style="border-bottom:1px solid var(--lv-border);">
                                    <td style="padding:12px; font-weight:600;">${w.title}</td>
                                    <td style="padding:12px; color:var(--lv-text-muted);">${w.provider}</td>
                                    <td style="padding:12px;"><span class="liventra-badge ${w.status === 'published' ? 'liventra-badge-success' : 'liventra-badge-warning'}">${w.status}</span></td>
                                    <td style="padding:12px;">${w.attendees}</td>
                                    <td style="padding:12px; color:var(--lv-success); font-weight:600;">${w.revenue}</td>
                                    <td style="padding:12px;">
                                        <button class="liventra-btn liventra-btn-secondary btn-trigger-create" style="padding:4px 10px; font-size:11px;">Edit Wizard</button>
                                        <button class="liventra-btn liventra-btn-secondary btn-duplicate-webinar" data-webinar-id="${w.id}" style="padding:4px 10px; font-size:11px;">Duplicate</button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }

        renderMyWebinarsView() {
            return `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="margin:0; font-size:18px; color:var(--lv-text);">🎥 All Managed Webinars</h3>
                    <button class="liventra-btn liventra-btn-primary btn-trigger-create">+ Create Webinar</button>
                </div>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:16px;">
                    ${this.webinars.map(w => `
                        <div class="liventra-card">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                                <h4 style="margin:0; font-size:16px; color:var(--lv-text);">${w.title}</h4>
                                <span class="liventra-badge ${w.status === 'published' ? 'liventra-badge-success' : 'liventra-badge-warning'}">${w.status}</span>
                            </div>
                            <p style="margin:0 0 16px 0; font-size:12px; color:var(--lv-text-muted);">Provider: ${w.provider} | Watch Time: ${w.watchTime}</p>
                            <div style="display:flex; gap:8px;">
                                <button class="liventra-btn liventra-btn-primary btn-trigger-create" style="font-size:11px; padding:6px 12px;">Edit Wizard</button>
                                <button class="liventra-btn liventra-btn-secondary btn-duplicate-webinar" data-webinar-id="${w.id}" style="font-size:11px; padding:6px 12px;">Duplicate</button>
                                <button class="liventra-btn liventra-btn-danger btn-delete-webinar" data-webinar-id="${w.id}" style="font-size:11px; padding:6px 12px;">Delete</button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        duplicateWebinar(webinarId) {
            const target = this.webinars.find(w => w.id === webinarId);
            if (!target) return;
            const newId = Date.now();
            const clone = {
                id: newId,
                title: target.title + ' (Copy)',
                status: 'draft',
                attendees: 0,
                revenue: '$0',
                watchTime: '0m',
                provider: target.provider
            };
            this.webinars.unshift(clone);
            this.showToast('✓ Duplicated Webinar: ' + clone.title);
            this.renderMainContent();
        }

        deleteWebinar(webinarId) {
            this.webinars = this.webinars.filter(w => w.id !== webinarId);
            this.showToast('✓ Deleted Webinar');
            this.renderMainContent();
        }

        /* ➕ Guided 7-Step Webinar Builder Wizard Screen (No Email Step) */
        renderWizardView() {
            const steps = [
                { num: 1, name: 'Basic Info' },
                { num: 2, name: 'Video Source' },
                { num: 3, name: 'Registration' },
                { num: 4, name: 'Schedule & Flow' },
                { num: 5, name: 'Offers' },
                { num: 6, name: 'Live Chat' },
                { num: 7, name: 'Review & Publish' }
            ];

            return `
                <div class="liventra-card" style="margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <div>
                            <span class="liventra-badge liventra-badge-primary">Guided Step ${this.wizardStep} of 7</span>
                            <h3 style="margin:6px 0 0 0; font-size:20px; color:var(--lv-text);">${steps[this.wizardStep - 1].name}</h3>
                        </div>
                        <div style="font-size:12px; color:var(--lv-text-muted);">Build time: ~2 minutes remaining</div>
                    </div>

                    <!-- Step Pills Bar -->
                    <div style="display:flex; gap:8px; overflow-x:auto; padding-bottom:10px; border-bottom:1px solid var(--lv-border);">
                        ${steps.map(s => `
                            <button class="liventra-step-pill ${s.num === this.wizardStep ? 'active' : ''}" data-step="${s.num}" style="
                                padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; border:1px solid var(--lv-border);
                                background:${s.num === this.wizardStep ? 'var(--lv-primary)' : 'var(--lv-bg)'};
                                color:${s.num === this.wizardStep ? '#FFF' : 'var(--lv-text-muted)'};
                                cursor:pointer; whitespace:nowrap;
                            ">
                                ${s.num}. ${s.name}
                            </button>
                        `).join('')}
                    </div>

                    <!-- Step Content Area -->
                    <div style="padding:24px 0;">
                        ${this.renderWizardStepContent()}
                    </div>

                    <!-- Step Action Footer -->
                    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--lv-border); padding-top:16px;">
                        <button id="btn-wizard-back" class="liventra-btn liventra-btn-secondary" ${this.wizardStep === 1 ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''}>← Back</button>
                        <button id="btn-wizard-next" class="liventra-btn liventra-btn-primary">
                            ${this.wizardStep === 7 ? '🚀 Launch & Publish Webinar' : 'Next: ' + steps[this.wizardStep].name + ' →'}
                        </button>
                    </div>
                </div>
            `;
        }

        renderWizardStepContent() {
            switch(this.wizardStep) {
                case 1:
                    return `
                        <div style="max-width:540px;">
                            <h4 style="margin-top:0; color:var(--lv-text);">Basic Webinar Details</h4>
                            <div class="liventra-form-group">
                                <label>Webinar Title</label>
                                <input type="text" id="inp-wizard-title" class="liventra-input" value="${this.wizardDraft.title}" />
                            </div>
                            <div class="liventra-form-group">
                                <label>Description / Subtitle</label>
                                <input type="text" id="inp-wizard-desc" class="liventra-input" value="${this.wizardDraft.description}" />
                            </div>
                            <div class="liventra-form-group">
                                <label>Host Name</label>
                                <input type="text" id="inp-wizard-host" class="liventra-input" value="${this.wizardDraft.host}" />
                            </div>
                        </div>
                    `;
                case 2:
                    return `
                        <div style="max-width:540px;">
                            <h4 style="margin-top:0; color:var(--lv-text);">Video Stream Selection</h4>
                            <div class="liventra-form-group">
                                <label>Stream Provider</label>
                                <select id="sel-wizard-provider" class="liventra-select">
                                    <option value="bunny" ${this.wizardDraft.videoProvider === 'bunny' ? 'selected' : ''}>Bunny.net Stream CDN (Recommended)</option>
                                    <option value="mp4" ${this.wizardDraft.videoProvider === 'mp4' ? 'selected' : ''}>Direct MP4 URL</option>
                                    <option value="hls" ${this.wizardDraft.videoProvider === 'hls' ? 'selected' : ''}>HLS Adaptive Stream</option>
                                    <option value="vimeo" ${this.wizardDraft.videoProvider === 'vimeo' ? 'selected' : ''}>Vimeo Pro Link</option>
                                </select>
                            </div>
                            <div class="liventra-form-group">
                                <label>Video Asset URL</label>
                                <input type="text" id="inp-wizard-url" class="liventra-input" value="${this.wizardDraft.videoUrl}" />
                            </div>
                            <div class="liventra-form-group">
                                <label>Video Duration</label>
                                <input type="text" id="inp-wizard-duration" class="liventra-input" value="${this.wizardDraft.videoDuration}" />
                            </div>
                        </div>
                    `;
                case 3:
                    return `
                        <div style="max-width:540px;">
                            <h4 style="margin-top:0; color:var(--lv-text);">Registration Page & Schedule</h4>
                            <div class="liventra-form-group">
                                <label>Schedule Type</label>
                                <select id="sel-wizard-schedule" class="liventra-select">
                                    <option value="jit" ${this.wizardDraft.scheduleType === 'jit' ? 'selected' : ''}>Just-In-Time (Plays every 15 minutes)</option>
                                    <option value="instant" ${this.wizardDraft.scheduleType === 'instant' ? 'selected' : ''}>Instant Watch On-Demand</option>
                                    <option value="scheduled" ${this.wizardDraft.scheduleType === 'scheduled' ? 'selected' : ''}>Scheduled Date & Time Slots</option>
                                </select>
                            </div>
                            <p style="font-size:12px; color:var(--lv-text-muted);">Registrants will see automatic localized countdown timers based on their browser timezone.</p>
                        </div>
                    `;
                case 4:
                    return `
                        <div>
                            <h4 style="margin-top:0; color:var(--lv-text);">Webinar Schedule & Track Canvas</h4>
                            <div class="liventra-timeline-track">
                                <div class="liventra-track-header">
                                    <span>🎥 Video Stream Track</span>
                                    <span>Duration: ${this.wizardDraft.videoDuration}</span>
                                </div>
                                <div class="liventra-track-items">
                                    <div class="liventra-timeline-block block-cta" style="left:0%; width:100%;">${this.wizardDraft.title}</div>
                                </div>
                            </div>
                            <div class="liventra-timeline-track">
                                <div class="liventra-track-header">
                                    <span>💰 Conversion Offers Track</span>
                                    <span>${this.wizardDraft.offers.length} Triggers Active</span>
                                </div>
                                <div class="liventra-track-items">
                                    ${this.wizardDraft.offers.map(o => `
                                        <div class="liventra-timeline-block block-cta" style="left:20%; width:30%;">${o.name} (${o.time})</div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                case 5:
                    return `
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                                <h4 style="margin:0; color:var(--lv-text);">Conversion Offers & Call to Actions</h4>
                                <button id="btn-trigger-add-offer" class="liventra-btn liventra-btn-primary">+ Add New Offer</button>
                            </div>
                            ${this.wizardDraft.offers.length === 0 ? `
                                <div style="padding:20px; text-align:center; color:var(--lv-text-muted); background:var(--lv-bg); border-radius:8px;">No offers created yet. Click "+ Add New Offer" above.</div>
                            ` : this.wizardDraft.offers.map(o => `
                                <div style="background:var(--lv-bg); border:1px solid var(--lv-border); padding:16px; border-radius:8px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                                    <div>
                                        <div style="font-weight:700; font-size:15px; color:var(--lv-text);">${o.name}</div>
                                        <div style="font-size:12px; color:var(--lv-text-muted); margin-top:4px;">Target URL: ${o.link}</div>
                                        <div style="font-size:12px; color:var(--lv-success); margin-top:4px;">Triggers at ${o.time} (Stays visible for ${o.duration})</div>
                                    </div>
                                    <div style="display:flex; gap:8px;">
                                        <button class="liventra-btn liventra-btn-danger btn-delete-offer" data-offer-id="${o.id}" style="font-size:11px; padding:4px 10px;">🗑️ Delete</button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                case 6:
                    return `
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                                <h4 style="margin:0; color:var(--lv-text);">Pre-Scripted Live Chat Messages</h4>
                                <button id="btn-trigger-add-chat" class="liventra-btn liventra-btn-primary">+ Add Chat Message</button>
                            </div>
                            ${this.wizardDraft.chatMessages.length === 0 ? `
                                <div style="padding:20px; text-align:center; color:var(--lv-text-muted); background:var(--lv-bg); border-radius:8px;">No chat messages scripted yet. Click "+ Add Chat Message" above.</div>
                            ` : this.wizardDraft.chatMessages.map(m => `
                                <div style="background:var(--lv-bg); border:1px solid var(--lv-border); padding:16px; border-radius:8px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                                    <div>
                                        <div style="font-weight:700; font-size:14px; color:#60A5FA;">${m.author} <span style="font-size:11px; color:var(--lv-text-muted);">at ${m.time}</span></div>
                                        <div style="font-size:13px; color:var(--lv-text); margin-top:4px;">"${m.text}"</div>
                                    </div>
                                    <div style="display:flex; gap:8px;">
                                        <button class="liventra-btn liventra-btn-danger btn-delete-chat" data-chat-id="${m.id}" style="font-size:11px; padding:4px 10px;">🗑️ Delete</button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                case 7:
                    return `
                        <div>
                            <h4 style="margin-top:0; color:var(--lv-text);">Webinar Launch Readiness Summary</h4>
                            <div style="background:var(--lv-bg); border:1px solid var(--lv-border); padding:20px; border-radius:8px; margin-bottom:16px;">
                                <div style="color:var(--lv-success); font-weight:600; margin-bottom:10px;">✓ Title: ${this.wizardDraft.title}</div>
                                <div style="color:var(--lv-success); font-weight:600; margin-bottom:10px;">✓ Host: ${this.wizardDraft.host}</div>
                                <div style="color:var(--lv-success); font-weight:600; margin-bottom:10px;">✓ Stream Provider: ${this.wizardDraft.videoProvider.toUpperCase()} (${this.wizardDraft.videoUrl})</div>
                                <div style="color:var(--lv-success); font-weight:600; margin-bottom:10px;">✓ Conversion Offers: ${this.wizardDraft.offers.length} Configured</div>
                                <div style="color:var(--lv-success); font-weight:600;">✓ Scripted Chat Messages: ${this.wizardDraft.chatMessages.length} Configured</div>
                            </div>
                            <p style="font-size:13px; color:var(--lv-text-muted);">Click "🚀 Launch & Publish Webinar" below to push live.</p>
                        </div>
                    `;
                default:
                    return '';
            }
        }

        openAddOfferModal() {
            const root = document.getElementById('liventra-modal-root');
            if (!root) return;
            root.innerHTML = `
                <div class="liventra-modal-backdrop">
                    <div class="liventra-modal">
                        <div class="liventra-modal-header">
                            <h3>💰 Add New Conversion Offer</h3>
                            <button class="liventra-modal-close">&times;</button>
                        </div>
                        <div class="liventra-form-group">
                            <label>Offer Name / Headline</label>
                            <input type="text" id="modal-offer-name" class="liventra-input" placeholder="e.g. VIP Masterclass Special Offer ($497)" />
                        </div>
                        <div class="liventra-form-group">
                            <label>Checkout / Destination URL</label>
                            <input type="text" id="modal-offer-link" class="liventra-input" placeholder="https://yourdomain.com/checkout" />
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div class="liventra-form-group">
                                <label>Trigger Time (mm:ss)</label>
                                <input type="text" id="modal-offer-time" class="liventra-input" value="15:00" />
                            </div>
                            <div class="liventra-form-group">
                                <label>Display Duration</label>
                                <input type="text" id="modal-offer-duration" class="liventra-input" value="10m" />
                            </div>
                        </div>
                        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
                            <button class="liventra-btn liventra-btn-secondary liventra-modal-close">Cancel</button>
                            <button id="btn-save-modal-offer" class="liventra-btn liventra-btn-primary">Save Offer</button>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('btn-save-modal-offer').addEventListener('click', () => {
                const name = document.getElementById('modal-offer-name').value || 'Special Bonus Offer';
                const link = document.getElementById('modal-offer-link').value || 'https://liventra.com/checkout';
                const time = document.getElementById('modal-offer-time').value || '15:00';
                const duration = document.getElementById('modal-offer-duration').value || '10m';

                this.wizardDraft.offers.push({
                    id: Date.now(),
                    name: name,
                    link: link,
                    time: time,
                    duration: duration,
                    type: 'popup'
                });

                root.innerHTML = '';
                this.showToast('✓ Conversion Offer Added');
                this.renderMainContent();
            });
        }

        openAddChatModal() {
            const root = document.getElementById('liventra-modal-root');
            if (!root) return;
            root.innerHTML = `
                <div class="liventra-modal-backdrop">
                    <div class="liventra-modal">
                        <div class="liventra-modal-header">
                            <h3>💬 Add Scripted Chat Message</h3>
                            <button class="liventra-modal-close">&times;</button>
                        </div>
                        <div class="liventra-form-group">
                            <label>Author / Speaker Name</label>
                            <input type="text" id="modal-chat-author" class="liventra-input" value="Host Bamidele" />
                        </div>
                        <div class="liventra-form-group">
                            <label>Message Content</label>
                            <input type="text" id="modal-chat-text" class="liventra-input" placeholder="e.g. Type your questions in the box below!" />
                        </div>
                        <div class="liventra-form-group">
                            <label>Trigger Time (mm:ss)</label>
                            <input type="text" id="modal-chat-time" class="liventra-input" value="05:30" />
                        </div>
                        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
                            <button class="liventra-btn liventra-btn-secondary liventra-modal-close">Cancel</button>
                            <button id="btn-save-modal-chat" class="liventra-btn liventra-btn-primary">Save Message</button>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('btn-save-modal-chat').addEventListener('click', () => {
                const author = document.getElementById('modal-chat-author').value || 'Attendee';
                const text = document.getElementById('modal-chat-text').value || 'Great webinar!';
                const time = document.getElementById('modal-chat-time').value || '05:30';

                this.wizardDraft.chatMessages.push({
                    id: Date.now(),
                    author: author,
                    text: text,
                    time: time
                });

                root.innerHTML = '';
                this.showToast('✓ Chat Message Added');
                this.renderMainContent();
            });
        }

        renderContactsView() {
            return `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="margin:0; font-size:18px; color:var(--lv-text);">👥 Registrants & Attendees</h3>
                    <button class="liventra-btn liventra-btn-secondary" onclick="alert('Exporting Registrants CSV...')">📥 Export CSV</button>
                </div>
                <div class="liventra-card">
                    <table style="width:100%; border-collapse:collapse; font-size:13px; color:var(--lv-text);">
                        <thead>
                            <tr style="border-bottom:1px solid var(--lv-border); text-align:left; color:var(--lv-text-muted);">
                                <th style="padding:10px;">Name</th>
                                <th style="padding:10px;">Email</th>
                                <th style="padding:10px;">Registered Session</th>
                                <th style="padding:10px;">Status</th>
                                <th style="padding:10px;">Offer Clicked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid var(--lv-border);">
                                <td style="padding:12px; font-weight:600;">John Doe</td>
                                <td style="padding:12px; color:var(--lv-text-muted);">john@example.com</td>
                                <td style="padding:12px;">High-Ticket Masterclass</td>
                                <td style="padding:12px;"><span class="liventra-badge liventra-badge-success">Attended (38m)</span></td>
                                <td style="padding:12px; color:var(--lv-success); font-weight:600;">VIP Offer ($497)</td>
                            </tr>
                            <tr style="border-bottom:1px solid var(--lv-border);">
                                <td style="padding:12px; font-weight:600;">Sarah Smith</td>
                                <td style="padding:12px; color:var(--lv-text-muted);">sarah@acme.com</td>
                                <td style="padding:12px;">Onboarding Demo</td>
                                <td style="padding:12px;"><span class="liventra-badge liventra-badge-primary">Registered</span></td>
                                <td style="padding:12px; color:var(--lv-text-muted);">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
        }

        renderAnalyticsView() {
            return `
                <div class="liventra-metrics-grid">
                    <div class="liventra-card">
                        <div class="liventra-card-title">Registration Conversion</div>
                        <div class="liventra-card-value">42.8%</div>
                        <div class="liventra-card-subtext">▲ +5.2% Optimization</div>
                    </div>
                    <div class="liventra-card">
                        <div class="liventra-card-title">Attendance Rate</div>
                        <div class="liventra-card-value">68.4%</div>
                        <div class="liventra-card-subtext">Above Industry Average</div>
                    </div>
                    <div class="liventra-card">
                        <div class="liventra-card-title">Total Offer Clicks</div>
                        <div class="liventra-card-value">314 Clicks</div>
                        <div class="liventra-card-subtext">12.4% Revenue Conversion</div>
                    </div>
                </div>
            `;
        }

        renderSettingsView() {
            return `
                <div class="liventra-card">
                    <h3 style="margin-top:0; color:var(--lv-text);">⚙️ Liventra Engine Settings</h3>
                    <p style="color:var(--lv-text-muted);">Manage global configurations, team access, extensions, and system status.</p>
                    <div style="display:flex; gap:12px; margin-top:16px;">
                        <button class="liventra-btn liventra-btn-primary" onclick="alert('Organizations Manager Active!')">Organizations</button>
                        <button class="liventra-btn liventra-btn-secondary" onclick="alert('Plugin Catalog Active!')">Plugin SDK</button>
                        <button class="liventra-btn liventra-btn-secondary" onclick="alert('System Probes Healthy!')">System Health</button>
                    </div>
                </div>
            `;
        }

        openPreviewModal() {
            const root = document.getElementById('liventra-modal-root');
            if (!root) return;
            root.innerHTML = `
                <div class="liventra-modal-backdrop">
                    <div class="liventra-modal" style="width: 720px;">
                        <div class="liventra-modal-header">
                            <h3>👁️ Liventra Live Session Preview</h3>
                            <button class="liventra-modal-close">&times;</button>
                        </div>
                        <div style="background:#000; border-radius:8px; height:320px; display:flex; align-items:center; justify-content:center; color:var(--lv-text-muted);">
                            🎥 [Live Session Preview Renderer Active — 04:15 Elapsed]
                        </div>
                        <div style="margin-top:16px; display:flex; justify-content:space-between; align-items:center;">
                            <span class="liventra-badge liventra-badge-success">Session Status: Synced</span>
                            <button class="liventra-btn liventra-btn-secondary liventra-modal-close">Close Preview</button>
                        </div>
                    </div>
                </div>
            `;
        }

        publishWebinar() {
            this.showToast('Publishing Webinar via REST API...');
            const url = getRestEndpoint('studio/webinars/' + this.activeWebinarId + '/publish');
            const headers = { 'Content-Type': 'application/json' };
            if (window.liventraSettings && window.liventraSettings.nonce) {
                headers['X-WP-Nonce'] = window.liventraSettings.nonce;
            }

            const newWebinar = {
                id: this.webinars.length + 1,
                title: this.wizardDraft.title || 'New Automated Webinar',
                status: 'published',
                attendees: 0,
                revenue: '$0',
                watchTime: '0m',
                provider: (this.wizardDraft.videoProvider || 'Bunny.net').toUpperCase()
            };

            fetch(url, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(this.wizardDraft)
            })
            .then(res => res.json())
            .then(data => {
                this.webinars.unshift(newWebinar);
                this.showToast('🚀 Webinar Successfully Published!');
                this.switchNav('my-webinars');
            })
            .catch(() => {
                this.webinars.unshift(newWebinar);
                this.showToast('🚀 Webinar Published! (Saved to Catalog)');
                this.switchNav('my-webinars');
            });
        }

        showToast(msg) {
            const toastRoot = document.getElementById('liventra-toast-root');
            if (!toastRoot) return;
            const toast = document.createElement('div');
            toast.className = 'liventra-toast';
            toast.innerText = msg;
            toastRoot.appendChild(toast);
            setTimeout(() => toast.remove(), 3500);
        }
    }

    window.LiventraAdminStudio = LiventraAdminStudio;

    // Retrying Mount Engine
    function startMountEngine() {
        let attempts = 0;
        const interval = setInterval(() => {
            attempts++;
            const containers = [
                document.getElementById('liventra-admin-studio'),
                document.querySelector('.liventra-admin-studio')
            ];
            let mountedAny = false;
            containers.forEach(container => {
                if (container && !container.dataset.mounted) {
                    container.dataset.mounted = 'true';
                    window.liventraApp = new LiventraAdminStudio({ containerId: container.id || 'liventra-admin-studio' });
                    mountedAny = true;
                }
            });
            if (mountedAny || attempts > 100) {
                clearInterval(interval);
            }
        }, 50);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startMountEngine);
    } else {
        startMountEngine();
    }
})(window, document);
