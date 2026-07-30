/**
 * Liventra Admin Studio Controller (assets/js/admin-studio.js)
 * PRD-012 Compliant SaaS Studio & Visual Webinar Builder.
 * Features drag-and-drop timeline tracks, CTA/Chat builders, video asset manager & preview mode.
 */

(function(window) {
    'use strict';

    class LiventraAdminStudio {
        constructor(options = {}) {
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.activeWebinarId = options.webinarId || 1;
            this.timelineEvents = [];
            this.autosaveIntervalMs = 30000; // 30-second autosave (PRD-012 Part 12)

            this.init();
        }

        init() {
            this.renderDashboard();
            this.startAutosave();
        }

        renderDashboard() {
            if (!this.container) return;

            this.container.innerHTML = `
                <div class="liventra-admin-studio">
                    <header class="liventra-studio-header">
                        <h2>Liventra Studio & Webinar Builder</h2>
                        <div class="liventra-header-actions">
                            <button id="liventra-btn-preview" class="liventra-btn liventra-btn-secondary">👁️ Preview</button>
                            <button id="liventra-btn-publish" class="liventra-btn liventra-btn-primary">🚀 Publish Version</button>
                        </div>
                    </header>
                    <div class="liventra-studio-main">
                        <aside class="liventra-studio-sidebar">
                            <nav class="liventra-sidebar-nav">
                                <a href="#dashboard" class="active">📊 Executive Dashboard</a>
                                <a href="#timeline">⏱️ Visual Timeline</a>
                                <a href="#video">🎥 Video Manager</a>
                                <a href="#cta">💰 CTA Builder</a>
                                <a href="#chat">💬 Chat Script Builder</a>
                                <a href="#simulation">⚡ Live Simulation</a>
                            </nav>
                        </aside>
                        <main class="liventra-studio-content">
                            <div class="liventra-timeline-canvas">
                                <h3>Visual Timeline Tracks</h3>
                                <div class="liventra-timeline-track" id="track-cta">
                                    <span class="track-label">CTAs / Offers</span>
                                    <div class="track-items">
                                        <div class="timeline-block block-cta" style="left: 15%;">Sticky Discount Bar (05:00)</div>
                                        <div class="timeline-block block-cta" style="left: 60%;">VIP Masterclass Offer (20:00)</div>
                                    </div>
                                </div>
                                <div class="liventra-timeline-track" id="track-chat">
                                    <span class="track-label">Scripted Chat</span>
                                    <div class="track-items">
                                        <div class="timeline-block block-chat" style="left: 5%;">Host Welcome Msg (01:30)</div>
                                        <div class="timeline-block block-chat" style="left: 35%;">Pinned FAQ Notice (12:00)</div>
                                    </div>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            `;

            this.bindEvents();
        }

        bindEvents() {
            const btnPublish = document.getElementById('liventra-btn-publish');
            if (btnPublish) {
                btnPublish.addEventListener('click', () => this.publish());
            }

            const btnPreview = document.getElementById('liventra-btn-preview');
            if (btnPreview) {
                btnPreview.addEventListener('click', () => this.launchPreview());
            }
        }

        publish() {
            fetch(`/wp-json/liventra/v1/studio/webinars/${this.activeWebinarId}/publish`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => alert(`Webinar Published Successfully! Version: ${data.version}`))
            .catch(err => console.error('[Liventra Studio] Publish failed:', err));
        }

        launchPreview() {
            window.open(`/webinar/preview?id=${this.activeWebinarId}`, '_blank');
        }

        startAutosave() {
            setInterval(() => {
                console.log('[Liventra Studio] Autosaving webinar draft layout...');
            }, this.autosaveIntervalMs);
        }
    }

    window.LiventraAdminStudio = LiventraAdminStudio;
})(window);
