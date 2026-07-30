/**
 * Liventra Video Engine Player Renderer (assets/js/video-player.js)
 * Chromeless Video Renderer bound directly to SessionEngine events.
 * Enforces muted autoplay fallback, seek-locking, and zero browser scrub controls.
 */

(function(window) {
    'use strict';

    class LiventraVideoPlayer {
        constructor(containerId, sessionEngine, options) {
            this.container = document.getElementById(containerId);
            this.sessionEngine = sessionEngine;
            this.videoUrl = options.videoUrl;
            this.videoProvider = options.videoProvider || 'mp4'; // mp4 | hls
            this.allowPause = options.allowPause || false;

            this.videoElement = null;
            this.audioOverlay = null;

            this.init();
        }

        init() {
            if (!this.container) {
                console.error('[Liventra VideoPlayer] Container element not found');
                return;
            }

            this.container.classList.add('liventra-video-stage');

            // Create Video Element
            this.videoElement = document.createElement('video');
            this.videoElement.className = 'liventra-chromeless-player';
            this.videoElement.src = this.videoUrl;
            this.videoElement.playsInline = true;
            this.videoElement.controls = false; // Chromeless
            this.videoElement.disablePictureInPicture = true;
            this.videoElement.setAttribute('controlsList', 'nodownload nofailback noremoteplayback');

            // Prevent Right-Click Context Menu
            this.videoElement.addEventListener('contextmenu', e => e.preventDefault());

            this.container.appendChild(this.videoElement);

            // Bind SessionEngine Events
            this.bindSessionEngine();
        }

        bindSessionEngine() {
            // Listen for Session Ticks & Sync
            this.sessionEngine.on('tick', data => {
                this.syncPlayback(data.elapsedSeconds);
            });

            this.sessionEngine.on('drift_corrected', data => {
                this.forceSeek(data.elapsedSeconds);
            });

            this.sessionEngine.on('state_change', data => {
                if (data.state === 'live') {
                    this.attemptPlay(data.elapsedSeconds);
                } else if (data.state === 'ended') {
                    this.videoElement.pause();
                }
            });
        }

        attemptPlay(elapsedSeconds) {
            if (elapsedSeconds !== undefined) {
                this.videoElement.currentTime = elapsedSeconds;
            }

            const playPromise = this.videoElement.play();

            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.warn('[Liventra VideoPlayer] Unmuted autoplay blocked by browser policy. Falling back to muted autoplay.', error);
                    this.videoElement.muted = true;
                    this.videoElement.play();
                    this.showAudioOverlay();
                });
            }
        }

        syncPlayback(targetElapsedSeconds) {
            if (this.videoElement.paused && this.sessionEngine.state === 'live') {
                this.attemptPlay(targetElapsedSeconds);
                return;
            }

            const drift = Math.abs(this.videoElement.currentTime - targetElapsedSeconds);
            if (drift > 2.5) {
                this.forceSeek(targetElapsedSeconds);
            }
        }

        forceSeek(targetSeconds) {
            if (this.videoElement) {
                this.videoElement.currentTime = targetSeconds;
            }
        }

        showAudioOverlay() {
            if (this.audioOverlay) return;

            this.audioOverlay = document.createElement('div');
            this.audioOverlay.className = 'liventra-audio-overlay';
            this.audioOverlay.innerHTML = `
                <button class="liventra-unmute-btn" id="liventra-unmute-trigger">
                    <span class="liventra-icon">🔊</span>
                    <span class="liventra-text">Tap to Enable Sound</span>
                </button>
            `;

            this.container.appendChild(this.audioOverlay);

            const btn = this.audioOverlay.querySelector('#liventra-unmute-trigger');
            btn.addEventListener('click', () => {
                this.videoElement.muted = false;
                this.audioOverlay.remove();
                this.audioOverlay = null;
            });
        }
    }

    window.LiventraVideoPlayer = LiventraVideoPlayer;
})(window);
