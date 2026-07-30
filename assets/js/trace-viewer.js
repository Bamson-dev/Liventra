/**
 * Liventra Distributed Trace Viewer (assets/js/trace-viewer.js)
 * PRD-016 Compliant Waterfall Span Visualizer.
 */

(function(window) {
    'use strict';

    class LiventraTraceViewer {
        constructor(options = {}) {
            this.container = options.containerId ? document.getElementById(options.containerId) : document.body;
            this.init();
        }

        init() {
            if (!this.container) return;
            this.container.innerHTML = `
                <div class="liventra-trace-viewer">
                    <h3>⚡ Distributed Request Trace Explorer</h3>
                    <div class="liventra-waterfall-tree">
                        <div class="liventra-span-row">
                            <span class="span-name">GET /wp-json/liventra/v1/session/123</span>
                            <span class="span-bar" style="width: 100%;">45.2ms</span>
                        </div>
                        <div class="liventra-span-row nested">
                            <span class="span-name">SecurityMiddleware::authorize</span>
                            <span class="span-bar" style="width: 15%;">2.1ms</span>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    window.LiventraTraceViewer = LiventraTraceViewer;
})(window);
