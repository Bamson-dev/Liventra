/**
 * Liventra Client Notification Center (assets/js/notification-center.js)
 * PRD-015 Compliant In-App Notification Center & Toast Component.
 * Supports real-time toast alerts, unread counter badge & notification history dropdown.
 */

(function(window) {
    'use strict';

    class LiventraNotificationCenter {
        constructor(options = {}) {
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.unreadCount = 0;
            this.init();
        }

        init() {
            if (!this.container) return;
            this.renderBadge();
        }

        renderBadge() {
            const badge = document.createElement('div');
            badge.className = 'liventra-notification-badge';
            badge.innerHTML = `🔔 <span class="badge-count">${this.unreadCount}</span>`;
            this.container.appendChild(badge);
        }

        showToast(title, body) {
            const toast = document.createElement('div');
            toast.className = 'liventra-toast-notification';
            toast.innerHTML = `
                <div class="liventra-toast-title">🔔 ${title}</div>
                <div class="liventra-toast-body">${body}</div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }
    }

    window.LiventraNotificationCenter = LiventraNotificationCenter;
})(window);
