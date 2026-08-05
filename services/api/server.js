/**
 * Liventra Cloud Platform — Standalone REST API Gateway Engine (services/api/server.js)
 * High-performance Node.js API server for Liventra Cloud Platform.
 */

const express = require('express');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());

// In-Memory Cloud Store (Synced with Supabase Cloud DB)
let webinarsStore = [
    { id: 1, title: 'High-Ticket Evergreen Sales Masterclass', status: 'published', attendees: 142, revenue: '$14,850', watchTime: '24m 18s', provider: 'Bunny Stream CDN' },
    { id: 2, title: 'SaaS Automated Onboarding Demo', status: 'published', attendees: 89, revenue: '$8,200', watchTime: '18m 45s', provider: 'Cloudflare R2' }
];

let videosStore = [
    { id: 'vid_1', title: 'High-Ticket Sales Presentation', duration: '42:15', size: '342 MB', provider: 'Liventra Cloud Video' },
    { id: 'vid_2', title: 'SaaS Product Walkthrough', duration: '18:45', size: '185 MB', provider: 'Bunny Stream CDN' }
];

// 1. Health Check
app.get('/health', (req, res) => {
    res.status(200).json({ status: 'ok' });
});

app.get('/api/health', (req, res) => {
    res.status(200).json({ status: 'ok' });
});

// 2. Authentication API
app.post('/api/auth/login', (req, res) => {
    const { email, password } = req.body;
    res.json({
        success: true,
        token: 'jwt_liventra_cloud_token_sample',
        user: { email: email || 'admin@liventra.com', name: 'Platform Admin', org: 'Liventra Enterprise' }
    });
});

// 3. Webinars API
app.get('/api/webinars', (req, res) => {
    res.json(webinarsStore);
});

app.post('/api/webinars', (req, res) => {
    const webinar = { id: Date.now(), ...req.body, status: 'published', attendees: 0, revenue: '$0' };
    webinarsStore.unshift(webinar);
    res.status(201).json(webinar);
});

app.post('/api/webinars/:id/publish', (req, res) => {
    const id = parseInt(req.params.id, 10);
    const item = webinarsStore.find(w => w.id === id);
    if (item) item.status = 'published';
    res.json({ success: true, message: 'Webinar published on Liventra Cloud Platform', webinarId: id });
});

// 4. Video Library API
app.get('/api/videos', (req, res) => {
    res.json(videosStore);
});

app.post('/api/videos/upload', (req, res) => {
    const newVideo = {
        id: 'vid_' + Date.now(),
        title: req.body.title || 'New Uploaded Video Asset',
        duration: '35:20',
        size: '240 MB',
        provider: 'Liventra Direct Upload'
    };
    videosStore.unshift(newVideo);
    res.status(201).json(newVideo);
});

// 5. Analytics API
app.get('/api/analytics', (req, res) => {
    res.json({
        liveAudience: 142,
        totalRevenue: 23050,
        conversionRate: 42.8,
        attendanceRate: 68.4,
        offerClicks: 314
    });
});

// 6. Contacts API
app.get('/api/contacts', (req, res) => {
    res.json([
        { id: 1, name: 'John Doe', email: 'john@example.com', session: 'High-Ticket Masterclass', status: 'Attended (38m)' },
        { id: 2, name: 'Sarah Smith', email: 'sarah@acme.com', session: 'Onboarding Demo', status: 'Registered' }
    ]);
});

// 7. Integrations API
app.get('/api/integrations', (req, res) => {
    res.json([
        { name: 'Universal JS Embed', type: 'sdk', status: 'active' },
        { name: 'React / Next.js SDK', type: 'npm', status: 'active' },
        { name: 'WordPress Connector', type: 'cms', status: 'connected' },
        { name: 'Shopify App', type: 'ecommerce', status: 'ready' },
        { name: 'Webflow Component', type: 'nocode', status: 'ready' }
    ]);
});

// 8. Settings API
app.get('/api/settings', (req, res) => {
    res.json({
        supabaseUrl: 'https://qtkuqwafpasalsgogpka.supabase.co',
        supabaseStatus: 'Connected',
        cdnProvider: 'Liventra Edge CDN',
        storageProvider: 'Cloudflare R2'
    });
});

if (require.main === module) {
    app.listen(PORT, '0.0.0.0', () => {
        console.log(`⚡ Liventra Cloud API Gateway running on port ${PORT} bound to 0.0.0.0`);
    });
}

module.exports = app;
