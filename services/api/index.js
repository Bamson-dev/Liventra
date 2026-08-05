/**
 * Liventra Cloud Platform API Gateway & Webhook Dispatcher (services/api/index.js)
 */

class LiventraCloudAPI {
    constructor() {
        this.webhooks = [];
    }

    registerWebhook(event, targetUrl) {
        this.webhooks.push({ event, targetUrl, created_at: new Date() });
        return { success: true, message: `Webhook registered for ${event}` };
    }

    async dispatchEvent(eventType, payload) {
        const matching = this.webhooks.filter(w => w.event === eventType || w.event === '*');
        const results = [];

        for (const hook of matching) {
            try {
                results.push({ url: hook.targetUrl, status: 200 });
            } catch (err) {
                results.push({ url: hook.targetUrl, status: 500, error: err.message });
            }
        }

        return { event: eventType, dispatched: results.length, results };
    }
}

module.exports = new LiventraCloudAPI();
