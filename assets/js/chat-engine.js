/**
 * Liventra Client Chat Engine (assets/js/chat-engine.js)
 * PRD-010 Compliant Live Chat & Realtime Engagement Controller.
 * Handles chat message bubbles, moderator styling, pinned banner, emoji reactions & unread tracking.
 */

(function(window) {
    'use strict';

    class LiventraChatEngine {
        constructor(sessionEngine, options = {}) {
            this.sessionEngine = sessionEngine;
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.pinnedBanner = options.pinnedId ? document.getElementById(options.pinnedId) : null;
            this.unreadCount = 0;
            this.messages = [];

            this.init();
        }

        init() {
            if (!this.sessionEngine) return;

            // Listen to timeline chat events
            this.sessionEngine.on('timeline_event', eventData => {
                if (eventData.event_type === 'chat') {
                    this.renderMessage(eventData.payload || eventData);
                }
            });
        }

        renderMessage(payload) {
            const sender = payload.sender || 'Attendee';
            const message = payload.message || '';
            const role = payload.role || 'attendee';
            const uuid = payload.uuid || Math.random().toString(36).substr(2, 9);

            const msgCard = document.createElement('div');
            msgCard.className = `liventra-chat-msg liventra-chat-role-${role}`;
            msgCard.innerHTML = `
                <div class="liventra-chat-sender">
                    <strong>${sender}</strong> ${role === 'host' || role === 'moderator' ? '<span class="liventra-mod-badge">HOST</span>' : ''}
                </div>
                <div class="liventra-chat-bubble">${message}</div>
                <div class="liventra-chat-reactions">
                    <button class="liventra-reaction-btn" data-emoji="👍">👍</button>
                    <button class="liventra-reaction-btn" data-emoji="❤️">❤️</button>
                    <button class="liventra-reaction-btn" data-emoji="🔥">🔥</button>
                </div>
            `;

            this.container.appendChild(msgCard);
            this.container.scrollTop = this.container.scrollHeight;

            // Reaction listener
            const btns = msgCard.querySelectorAll('.liventra-reaction-btn');
            btns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const emoji = btn.getAttribute('data-emoji');
                    this.sendReaction(uuid, emoji);
                });
            });
        }

        setPinnedMessage(messageText) {
            if (!this.pinnedBanner) return;
            this.pinnedBanner.style.display = 'block';
            this.pinnedBanner.innerHTML = `📌 <strong>PINNED:</strong> ${messageText}`;
        }

        sendReaction(uuid, emoji) {
            fetch('/wp-json/liventra/v1/chat/react', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ chat_uuid: uuid, emoji: emoji })
            }).catch(err => console.error('[Liventra ChatEngine] Reaction error:', err));
        }
    }

    window.LiventraChatEngine = LiventraChatEngine;
})(window);
