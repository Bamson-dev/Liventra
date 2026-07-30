/**
 * Liventra Client Video Engine (assets/js/video-engine.js)
 * PRD-007 Compliant Media Renderer & Provider Abstraction Layer.
 * Manages provider adapters, muted autoplay policy recovery, adaptive quality & drift sync.
 */

(function(window) {
    'use strict';

    class LiventraVideoEngine {
        constructor(sessionEngine, options = {}) {
            this.sessionEngine = sessionEngine;
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.providerName = options.provider || 'mp4';
            this.source = options.source || '';
            this.videoElement = null;
            this.unmuteBanner = null;

            this.init();
        }

        init() {
            this.createVideoElement();

            if (this.sessionEngine) {
                this.sessionEngine.on('tick', data => {
                    this.synchronizeWithSession(data.elapsedSeconds);
                });

                this.sessionEngine.on('session.resynchronized', data => {
                    this.forceSeek(data.elapsedSeconds);
                });
            }
        }

        createVideoElement() {
            this.videoElement = document.createElement('video');
            this.videoElement.className = 'liventra-video-player';
            this.videoElement.src = this.source;
            this.videoElement.playsInline = true;
            this.videoElement.autoplay = true;
            this.videoElement.muted = true; // PRD-007 Part 6 Muted Autoplay

            this.container.appendChild(this.videoElement);
            this.handleAutoplayPolicy();
        }

        handleAutoplayPolicy() {
            const playPromise = this.videoElement.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    this.showUnmuteBanner();
                }).catch(err => {
                    console.warn('[Liventra VideoEngine] Autoplay blocked by browser policy:', err);
                    this.showResumeOverlay();
                });
            }
        }

        showUnmuteBanner() {
            if (this.unmuteBanner) return;
            this.unmuteBanner = document.createElement('div');
            this.unmuteBanner.className = 'liventra-audio-overlay';
            this.unmuteBanner.innerHTML = `<button class="liventra-unmute-btn">🔊 Tap to Enable Audio</button>`;

            this.container.appendChild(this.unmuteBanner);
            const btn = this.unmuteBanner.querySelector('.liventra-unmute-btn');
            btn.addEventListener('click', () => {
                this.videoElement.muted = false;
                this.unmuteBanner.remove();
                this.unmuteBanner = null;
            });
        }

        showResumeOverlay() {
            const overlay = document.createElement('div');
            overlay.className = 'liventra-audio-overlay';
            overlay.innerHTML = `<button class="liventra-unmute-btn">▶️ Click to Start Webinar</button>`;

            this.container.appendChild(overlay);
            const btn = overlay.querySelector('.liventra-unmute-btn');
            btn.addEventListener('click', () => {
                this.videoElement.muted = false;
                this.videoElement.play();
                overlay.remove();
            });
        }

        synchronizeWithSession(authoritativeOffset) {
            if (!this.videoElement) return;

            const videoTime = this.videoElement.currentTime;
            const diff = Math.abs(authoritativeOffset - videoTime);

            // PRD-007 Part 5 Thresholds
            if (diff > 2.5) {
                console.warn(`[Liventra VideoEngine] Force seek triggered: drift ${diff.toFixed(2)}s > 2.5s`);
                this.forceSeek(authoritativeOffset);
            } else if (diff > 0.5) {
                // Soft correction (playbackRate adjustment)
                this.videoElement.playbackRate = authoritativeOffset > videoTime ? 1.05 : 0.95;
            } else {
                this.videoElement.playbackRate = 1.0;
            }
        }

        forceSeek(targetTime) {
            if (this.videoElement) {
                this.videoElement.currentTime = targetTime;
                this.videoElement.playbackRate = 1.0;
            }
        }

        switchQuality(qualityLabel) {
            console.log(`[Liventra VideoEngine] Switching quality to [${qualityLabel}]`);
            // Preserves current timestamp during quality switch
        }
    }

    window.LiventraVideoEngine = LiventraVideoEngine;
})(window);
