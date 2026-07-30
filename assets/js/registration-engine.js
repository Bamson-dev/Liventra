/**
 * Liventra Client Registration Engine (assets/js/registration-engine.js)
 * PRD-008 Compliant Registration & Waiting Room Controller.
 * Handles registration form submissions, waiting room countdowns & admission flows.
 */

(function(window) {
    'use strict';

    class LiventraRegistrationEngine {
        constructor(options = {}) {
            this.formElement = options.formId ? document.getElementById(options.formId) : null;
            this.waitingRoomElement = options.waitingRoomId ? document.getElementById(options.waitingRoomId) : null;
            this.token = options.token || '';

            this.init();
        }

        init() {
            if (this.formElement) {
                this.formElement.addEventListener('submit', e => {
                    e.preventDefault();
                    this.handleRegistrationSubmit();
                });
            }
        }

        handleRegistrationSubmit() {
            const formData = new FormData(this.formElement);
            const payload = {
                email: formData.get('email'),
                first_name: formData.get('first_name'),
                last_name: formData.get('last_name')
            };

            fetch('/wp-json/liventra/v1/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).then(res => res.json()).then(data => {
                if (data.join_url) {
                    window.location.href = data.join_url;
                }
            }).catch(err => {
                console.error('[Liventra RegistrationEngine] Registration failed:', err);
            });
        }

        updateWaitingRoomCountdown(remainingSeconds) {
            if (!this.waitingRoomElement) return;

            const mins = Math.floor(remainingSeconds / 60);
            const secs = remainingSeconds % 60;
            const formatted = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

            const timerDisplay = this.waitingRoomElement.querySelector('.liventra-countdown-timer');
            if (timerDisplay) {
                timerDisplay.textContent = formatted;
            }
        }

        handleAdmission(videoEngine) {
            if (this.waitingRoomElement) {
                this.waitingRoomElement.style.display = 'none';
            }
            if (videoEngine) {
                videoEngine.play();
            }
        }
    }

    window.LiventraRegistrationEngine = LiventraRegistrationEngine;
})(window);
