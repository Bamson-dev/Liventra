/**
 * Liventra Client Security Engine (assets/js/security-engine.js)
 * PRD-013 Compliant Client Security & CSRF Protection Controller.
 * Manages nonce refresh, token injection & expired session handling.
 */

(function(window) {
    'use strict';

    class LiventraSecurityEngine {
        constructor(options = {}) {
            this.nonce = options.nonce || '';
            this.authToken = options.token || '';
            this.init();
        }

        init() {
            // Attach CSRF header to global fetch calls
            const originalFetch = window.fetch;
            const self = this;

            window.fetch = function(url, options = {}) {
                options.headers = options.headers || {};
                if (self.nonce) {
                    options.headers['X-Liventra-Nonce'] = self.nonce;
                }
                if (self.authToken) {
                    options.headers['Authorization'] = `Bearer ${self.authToken}`;
                }
                return originalFetch(url, options).then(response => {
                    if (response.status === 401 || response.status === 403) {
                        console.warn('[Liventra Security] Access denied or session expired.');
                    }
                    return response;
                });
            };
        }

        setNonce(newNonce) {
            this.nonce = newNonce;
        }

        setAuthToken(newToken) {
            this.authToken = newToken;
        }
    }

    window.LiventraSecurityEngine = LiventraSecurityEngine;
})(window);
