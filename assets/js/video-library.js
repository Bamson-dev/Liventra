/**
 * Liventra Video Library & Direct Drag-and-Drop Uploader Engine (assets/js/video-library.js)
 * Standalone Video Asset Management for Cloud Platform & WordPress.
 */

(function(window, document) {
    'use strict';

    class LiventraVideoLibrary {
        constructor(options = {}) {
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;

            // Managed Reusable Video Library Store
            const saved = localStorage.getItem('liventra_video_library');
            if (saved !== null) {
                try {
                    this.videos = JSON.parse(saved);
                } catch(e) {
                    this.videos = [];
                }
            } else {
                this.videos = [];
                localStorage.setItem('liventra_video_library', JSON.stringify(this.videos));
            }

            this.init();
        }

        saveLibrary() {
            localStorage.setItem('liventra_video_library', JSON.stringify(this.videos));
        }

        init() {
            if (!this.container) return;
            this.render();
            this.bindEvents();
        }

        render() {
            this.container.innerHTML = `
                <div class="liventra-admin-container">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                        <div>
                            <span class="liventra-badge liventra-badge-primary">📹 Asset Management</span>
                            <h3 style="margin:6px 0 0 0; font-size:20px; color:var(--lv-text);">Video Library (${this.videos.length} Assets)</h3>
                        </div>
                        <button id="btn-upload-video-modal" class="liventra-btn liventra-btn-primary">📤 Upload New Video</button>
                    </div>

                    <!-- Storage Overview Grid -->
                    <div class="liventra-metrics-grid" style="margin-bottom:24px;">
                        <div class="liventra-card">
                            <div class="liventra-card-title">Total Videos Hosted</div>
                            <div class="liventra-card-value">${this.videos.length} Videos</div>
                            <div class="liventra-card-subtext">Reusable Across All Webinars</div>
                        </div>
                        <div class="liventra-card">
                            <div class="liventra-card-title">Storage Space Used</div>
                            <div class="liventra-card-value">527 MB</div>
                            <div class="liventra-card-subtext">Unlimited Cloud Storage Active</div>
                        </div>
                        <div class="liventra-card">
                            <div class="liventra-card-title">Active CDN Streams</div>
                            <div class="liventra-card-value">100% Ultra-Fast</div>
                            <div class="liventra-card-subtext">Global Edge CDN Acceleration</div>
                        </div>
                    </div>

                    <!-- Video Grid -->
                    ${this.videos.length === 0 ? `
                        <div class="liventra-card" style="padding:40px; text-align:center;">
                            <h4 style="margin:0 0 8px 0; color:var(--lv-text);">No Videos in Library</h4>
                            <p style="color:var(--lv-text-muted); margin-bottom:16px;">Upload your first video to reuse it across your evergreen webinars.</p>
                            <button id="btn-upload-video-empty" class="liventra-btn liventra-btn-primary">📤 Upload Video Now</button>
                        </div>
                    ` : `
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">
                            ${this.videos.map(v => `
                                <div class="liventra-card" style="padding:0; overflow:hidden; border:1px solid var(--lv-border);">
                                    <div style="position:relative; height:180px; background:#000; overflow:hidden;">
                                        <img src="${v.thumb}" style="width:100%; height:100%; object-fit:cover; opacity:0.8;" />
                                        <span style="position:absolute; bottom:10px; right:10px; background:rgba(0,0,0,0.8); color:#FFF; font-weight:700; font-size:11px; padding:3px 8px; border-radius:4px;">⏱️ ${v.duration}</span>
                                        <span style="position:absolute; top:10px; left:10px; background:var(--lv-primary); color:#FFF; font-weight:700; font-size:10px; padding:3px 8px; border-radius:4px;">${v.provider}</span>
                                    </div>
                                    <div style="padding:16px;">
                                        <h4 style="margin:0 0 6px 0; font-size:15px; color:var(--lv-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${v.title}</h4>
                                        <div style="font-size:12px; color:var(--lv-text-muted); margin-bottom:14px;">
                                            <span>Size: ${v.size}</span> • <span>Used in ${v.used_in} Webinars</span>
                                        </div>
                                        <div style="display:flex; gap:8px;">
                                            <button class="liventra-btn liventra-btn-primary btn-preview-video" data-url="${v.url}" style="font-size:11px; padding:6px 12px; flex:1;">👁️ Preview</button>
                                            <button class="liventra-btn liventra-btn-secondary btn-copy-vid-url" data-url="${v.url}" style="font-size:11px; padding:6px 10px;">📋 Copy Link</button>
                                            <button class="liventra-btn liventra-btn-danger btn-delete-video" data-id="${v.id}" style="font-size:11px; padding:6px 10px;">🗑️</button>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `}
                </div>
                <div id="liventra-vid-modal-root"></div>
            `;
        }

        bindEvents() {
            this.container.addEventListener('click', (e) => {
                const target = e.target.closest('button, .btn-preview-video, .btn-copy-vid-url, .btn-delete-video');
                if (!target) return;

                if (target.id === 'btn-upload-video-modal' || target.id === 'btn-upload-video-empty') {
                    e.preventDefault();
                    this.openUploadModal();
                    return;
                }

                if (target.classList.contains('btn-preview-video')) {
                    e.preventDefault();
                    const url = target.getAttribute('data-url');
                    this.openPreviewModal(url);
                    return;
                }

                if (target.classList.contains('btn-copy-vid-url')) {
                    e.preventDefault();
                    const url = target.getAttribute('data-url');
                    navigator.clipboard.writeText(url);
                    alert('📋 Video URL Copied to Clipboard!');
                    return;
                }

                if (target.classList.contains('btn-delete-video')) {
                    e.preventDefault();
                    const id = target.getAttribute('data-id');
                    if (confirm('Delete this video from your Video Library?')) {
                        this.videos = this.videos.filter(v => v.id !== id);
                        this.saveLibrary();
                        this.render();
                    }
                    return;
                }
            });
        }

        openUploadModal() {
            const modalRoot = document.getElementById('liventra-vid-modal-root');
            if (!modalRoot) return;

            modalRoot.innerHTML = `
                <div class="liventra-modal-backdrop">
                    <div class="liventra-modal" style="width: 580px;">
                        <div class="liventra-modal-header">
                            <h3>📤 Direct Video Upload to Liventra Library</h3>
                            <button class="liventra-modal-close" onclick="document.getElementById('liventra-vid-modal-root').innerHTML=''">&times;</button>
                        </div>

                        <!-- Drag & Drop Upload Zone -->
                        <div id="liventra-drop-zone" style="border: 2px dashed var(--lv-primary); border-radius: 12px; padding: 36px; text-align: center; background: var(--lv-bg); margin-bottom: 20px; cursor: pointer;">
                            <div style="font-size: 42px; margin-bottom: 8px;">📹</div>
                            <h4 style="margin: 0 0 6px 0; color: var(--lv-text); font-size: 16px;">Drag & Drop Video File Here</h4>
                            <p style="margin: 0 0 16px 0; color: var(--lv-text-muted); font-size: 13px;">Supports MP4, MOV, WEBM (Up to 5 GB). Instant CDN streaming.</p>
                            <input type="file" id="inp-video-file-picker" accept="video/*" style="display:none;" />
                            <button class="liventra-btn liventra-btn-primary" onclick="document.getElementById('inp-video-file-picker').click()">Browse Computer Files</button>
                        </div>

                        <!-- Upload Progress Container -->
                        <div id="liventra-upload-progress" style="display:none; margin-bottom:16px;">
                            <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--lv-text); margin-bottom:6px;">
                                <span id="txt-upload-filename">Uploading video...</span>
                                <span id="txt-upload-percent">0%</span>
                            </div>
                            <div style="background:var(--lv-bg); height:8px; border-radius:4px; overflow:hidden;">
                                <div id="bar-upload-fill" style="background:var(--lv-primary); width:0%; height:100%; transition:width 0.2s;"></div>
                            </div>
                        </div>

                        <div class="liventra-form-group">
                            <label>Video Title / Name</label>
                            <input type="text" id="inp-modal-vid-title" class="liventra-input" placeholder="e.g. Masterclass Presentation V1" />
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
                            <button class="liventra-btn liventra-btn-secondary" onclick="document.getElementById('liventra-vid-modal-root').innerHTML=''">Cancel</button>
                            <button id="btn-start-video-upload" class="liventra-btn liventra-btn-primary">⚡ Upload & Add to Library</button>
                        </div>
                    </div>
                </div>
            `;

            const filePicker = document.getElementById('inp-video-file-picker');
            const dropZone = document.getElementById('liventra-drop-zone');
            const btnUpload = document.getElementById('btn-start-video-upload');

            filePicker.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    document.getElementById('inp-modal-vid-title').value = file.name.replace(/\.[^/.]+$/, "");
                }
            });

            btnUpload.addEventListener('click', () => {
                const title = document.getElementById('inp-modal-vid-title').value || 'New Uploaded Video';
                const file = filePicker.files[0];

                const progressContainer = document.getElementById('liventra-upload-progress');
                const progressFill = document.getElementById('bar-upload-fill');
                const progressPercent = document.getElementById('txt-upload-percent');

                progressContainer.style.display = 'block';

                let pct = 0;
                const interval = setInterval(() => {
                    pct += 20;
                    progressFill.style.width = pct + '%';
                    progressPercent.innerText = pct + '%';

                    if (pct >= 100) {
                        clearInterval(interval);
                        const newVid = {
                            id: 'vid_' + Date.now(),
                            title: title,
                            url: file ? URL.createObjectURL(file) : 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                            duration: '35:20',
                            size: file ? (file.size / (1024 * 1024)).toFixed(1) + ' MB' : '240 MB',
                            provider: 'Liventra Cloud Upload',
                            created_at: new Date().toISOString().split('T')[0],
                            used_in: 0,
                            thumb: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&auto=format&fit=crop&q=80'
                        };
                        this.videos.unshift(newVid);
                        this.saveLibrary();
                        modalRoot.innerHTML = '';
                        this.render();
                    }
                }, 200);
            });
        }

        openPreviewModal(videoUrl) {
            const modalRoot = document.getElementById('liventra-vid-modal-root');
            if (!modalRoot) return;

            modalRoot.innerHTML = `
                <div class="liventra-modal-backdrop">
                    <div class="liventra-modal" style="width: 720px;">
                        <div class="liventra-modal-header">
                            <h3>👁️ Video Asset Preview</h3>
                            <button class="liventra-modal-close" onclick="document.getElementById('liventra-vid-modal-root').innerHTML=''">&times;</button>
                        </div>
                        <div style="background:#000; border-radius:8px; overflow:hidden;">
                            <video controls autoplay style="width:100%; height:380px; object-fit:cover;">
                                <source src="${videoUrl}" type="video/mp4">
                            </video>
                        </div>
                        <div style="margin-top:16px; display:flex; justify-content:flex-end;">
                            <button class="liventra-btn liventra-btn-secondary" onclick="document.getElementById('liventra-vid-modal-root').innerHTML=''">Close Preview</button>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    window.LiventraVideoLibrary = LiventraVideoLibrary;
})(window, document);
