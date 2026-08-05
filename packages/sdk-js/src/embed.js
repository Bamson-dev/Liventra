/**
 * Liventra Universal JavaScript SDK (packages/sdk-js/src/embed.js)
 * Standalone Zero-Dependency Embed Engine for WordPress, Shopify, Webflow, Wix, Squarespace, Framer, HTML.
 */

(function(window, document) {
    'use strict';

    const API_ROOT = 'https://qtkuqwafpasalsgogpka.supabase.co';

    class LiventraUniversalSDK {
        constructor() {
            this.version = '2.0.0';
            this.initialized = false;
        }

        init() {
            if (this.initialized) return;
            this.initialized = true;
            this.autoMountElements();
        }

        autoMountElements() {
            const containers = document.querySelectorAll('[data-liventra-embed], .liventra-embed');
            containers.forEach(container => {
                if (container.dataset.mounted) return;
                container.dataset.mounted = 'true';

                const embedType = container.getAttribute('data-liventra-embed') || 'live';
                const webinarId = container.getAttribute('data-webinar-id') || '1';

                if (embedType === 'registration') {
                    this.mountRegistrationForm(container, webinarId);
                } else if (embedType === 'live') {
                    this.mountLiveRoom(container, webinarId);
                } else if (embedType === 'replay') {
                    this.mountReplayRoom(container, webinarId);
                } else if (embedType === 'checkout') {
                    this.mountCheckoutOffer(container, webinarId);
                }
            });
        }

        mountRegistrationForm(container, webinarId) {
            container.innerHTML = `
                <div style="max-width: 540px; margin: 20px auto; padding: 28px; background: #0F172A; color: #FFF; border-radius: 12px; font-family: sans-serif; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);">
                    <div style="text-align:center; margin-bottom: 20px;">
                        <span style="background: #2563EB; color: #FFF; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase;">⚡ Exclusive Masterclass</span>
                        <h2 style="font-size: 22px; font-weight: 800; margin: 10px 0 6px 0; color: #FFFFFF;">Transform Cold Prospects Into Customers</h2>
                        <p style="color: #94A3B8; font-size: 13px; margin: 0;">Reserve your virtual seat for the live automated webinar session!</p>
                    </div>

                    <form onsubmit="event.preventDefault(); alert('Registration Confirmed! Redirecting to Live Room...'); window.location.href='?liventra_room=live';" style="display: flex; flex-direction: column; gap: 14px;">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 600; color: #CBD5E1; margin-bottom: 4px;">Full Name</label>
                            <input type="text" required placeholder="John Doe" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #334155; background: #1E293B; color: #FFF; font-size: 13px; box-sizing: border-box;" />
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 600; color: #CBD5E1; margin-bottom: 4px;">Email Address</label>
                            <input type="email" required placeholder="john@example.com" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #334155; background: #1E293B; color: #FFF; font-size: 13px; box-sizing: border-box;" />
                        </div>
                        <button type="submit" style="width: 100%; padding: 12px; border: none; border-radius: 6px; background: #2563EB; color: #FFF; font-size: 14px; font-weight: 700; cursor: pointer;">
                            🚀 Reserve Free Seat Now →
                        </button>
                    </form>
                </div>
            `;
        }

        mountLiveRoom(container, webinarId) {
            container.innerHTML = `
                <div style="max-width: 1000px; margin: 20px auto; font-family: sans-serif;">
                    <div style="background: #0F172A; padding: 14px 20px; border-radius: 10px 10px 0 0; display: flex; justify-content: space-between; align-items: center;">
                        <strong style="color: #FFF; font-size: 15px;">🔴 Live Automated Webinar Room</strong>
                        <span style="color: #10B981; font-weight: 700; font-size: 13px;">👥 142 Attendees Watching</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 2fr 1fr; background: #020617; border-radius: 0 0 10px 10px; overflow: hidden;">
                        <div style="background: #000; padding: 8px;">
                            <video controls autoplay muted style="width: 100%; height: 380px; object-fit: cover;">
                                <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div style="background: #0F172A; border-left: 1px solid #1E293B; display: flex; flex-direction: column; height: 400px;">
                            <div style="padding: 10px; border-bottom: 1px solid #1E293B; color: #FFF; font-weight: 700; font-size: 12px;">💬 Audience Live Chat</div>
                            <div style="flex: 1; padding: 10px; overflow-y: auto; font-size: 12px;">
                                <div style="margin-bottom: 8px; background: #1E293B; padding: 6px 8px; border-radius: 6px;">
                                    <span style="color: #60A5FA; font-weight: 700;">Host Bamidele:</span>
                                    <p style="color: #FFF; margin: 2px 0 0 0;">Welcome everyone! Type your city in chat!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        mountReplayRoom(container, webinarId) {
            this.mountLiveRoom(container, webinarId);
        }

        mountCheckoutOffer(container, webinarId) {
            container.innerHTML = `
                <div style="background: linear-gradient(90deg, #1E1B4B 0%, #312E81 100%); border: 1px solid #6366F1; padding: 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; font-family: sans-serif;">
                    <div>
                        <span style="background: #10B981; color: #FFF; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">🔥 Active Offer</span>
                        <div style="font-weight: 700; color: #FFF; font-size: 14px; margin-top: 4px;">VIP Masterclass Offer ($497)</div>
                    </div>
                    <a href="https://liventra.com/checkout" target="_blank" style="padding: 8px 16px; background: #2563EB; color: #FFF; text-decoration: none; font-size: 12px; font-weight: 700; border-radius: 6px;">Claim Offer →</a>
                </div>
            `;
        }
    }

    window.LiventraSDK = new LiventraUniversalSDK();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.LiventraSDK.init());
    } else {
        window.LiventraSDK.init();
    }
})(window, document);
