# 🚀 Liventra — Cloud-Native Webinar Platform

**Liventra** is a modern, cloud-native SaaS webinar platform designed to transform pre-recorded videos into interactive, high-converting live webinar experiences. Embedded anywhere using Universal SDKs, APIs, and multi-platform connectors (WordPress, Shopify, Webflow, React, Vue, Next.js, Laravel, HTML).

---

## 🌟 Core Features

- 🎥 **Standalone Cloud SaaS Dashboard**: Intuitive SaaS admin studio for managing webinars, video assets, and conversion funnels.
- 📹 **Dedicated Video Library**: Direct drag-and-drop video uploads, thumbnail generation, duration calculation, and reusable asset storage.
- 🔒 **Advanced Playback Security & Lock Controls**: Forward seek prevention, forced 1.0x playback speed lock, and dynamic attendee watermark overlays.
- 💬 **Simulated Live Chat Stream**: Scripted chat timeline with timestamps, speaker avatars, and real-time audience engagement.
- ⚡ **Timed Conversion Offers**: Pop-up conversion offer banners with countdown timers and direct checkout integration.
- 🔗 **Universal Embedding Anywhere**: 1-click embed code generators for JavaScript (`embed.js`), React (`<LiventraLiveRoom />`), Vue, iFrame, HTML, and WordPress shortcodes.
- 🐳 **Coolify & Docker VPS Ready**: 1-click deployment on Contabo/Hetzner VPS via Docker and Coolify.

---

## 🏗️ Architecture & Monorepo Structure

```
liventra/
├── apps/
│   ├── dashboard/        # SaaS Admin Studio & Webinar Builder
│   ├── attendee/         # Standalone Live Room & Replay Player
│   └── landing/          # Platform Marketing Site & Developer Portal
├── packages/
│   ├── sdk-js/           # Universal embed.js JavaScript SDK
│   ├── sdk-react/        # React SDK Components (<LiventraLiveRoom />)
│   ├── sdk-vue/          # Vue 3 SDK Components
│   └── ui/               # Unified Design System
├── services/
│   ├── api/              # Standalone Node/Express REST API Gateway
│   ├── video/            # Video Asset Manager & Storage Service
│   └── analytics/        # Real-time Analytics & Revenue Engine
├── integrations/
│   ├── wordpress/        # WordPress Connector Plugin
│   ├── shopify/          # Shopify App Connector
│   └── webflow/          # Webflow Embed Component
├── docker/               # Coolify & Docker Compose Production Configs
└── nginx/                # Nginx Reverse Proxy Configuration
```

---

## ⚡ Deployment on VPS via Coolify

Deploy Liventra directly on any VPS (Contabo, Hetzner, DigitalOcean) using Coolify:

```bash
chmod +x scripts/deploy-coolify.sh
./scripts/deploy-coolify.sh
```

---

## 📄 License
Commercial Enterprise License © 2026 Liventra. All rights reserved.
