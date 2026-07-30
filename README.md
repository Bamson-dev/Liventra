# ⚡ Liventra — Enterprise Realtime Webinar Platform for WordPress

[![CI Pipeline](https://github.com/Bamson-dev/Liventra/actions/workflows/ci.yml/badge.svg)](https://github.com/Bamson-dev/Liventra/actions)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Release](https://img.shields.io/badge/Release-v1.0.0--rc1-indigo.svg)](https://github.com/Bamson-dev/Liventra/releases)

**Liventra** is the premier, enterprise-grade automated webinar simulation and live engagement platform built natively for WordPress. Operating on a robust **Event-Driven Architecture (EDA)**, Liventra powers high-concurrency automated webinars, deterministic timeline execution, chromeless video playback, dynamic call-to-action (CTA) conversions, live chat, multi-tenant organizations, and third-party developer plugin ecosystems.

---

## 🏗️ Architecture Overview

Liventra is built on **19 Production Specifications (PRD-001 through PRD-019)** enforcing strict SOLID separation of concerns, PSR-4 autoloader compliance, constructor dependency injection, thin controllers, persistence-only repositories, and orchestration-only services.

```text
[ Client Session Engine (Tickers & Video) ]
                    │
                    ▼
       [ Authoritative REST API ]
                    │
                    ▼
          [ Security Platform ]
      (SSO, Rate Limits, Audit Logs)
                    │
                    ▼
     [ Core Orchestration Services ]
   (Timeline, CTA, Chat, Video, Registrations)
                    │
                    ▼
     [ Enterprise & Multi-Tenant Layer ]
      (Orgs, Workspaces, White-Labeling)
                    │
                    ▼
       [ EventBus & Analytics Engine ]
```

---

## 🚀 Quick Start & Installation

### Requirements
* PHP `>= 7.4` (PHP 8.2+ recommended)
* WordPress `>= 5.8`
* MySQL `>= 5.7` or MariaDB `>= 10.3`

### Developer Setup
```bash
git clone https://github.com/Bamson-dev/Liventra.git
cd Liventra
composer install
node tests/run-tests.js
```

---

## 🧪 Testing & Code Quality

Run the complete verification suite:
```bash
composer quality
```

Runs:
* Static Analysis (`PHPStan` Level 9, `Psalm` Strict Mode)
* Coding Standards (`PHPCS` WordPress Standards)
* Unit & Integration Tests (`PHPUnit`)
* E2E Browser Testing (`Playwright`)

---

## 👤 Author & Project Ownership

Liventra is solely authored, owned, designed, and maintained by **Bamidele Matthew**.

* **Author**: Bamidele Matthew
* **Maintainer**: Bamidele Matthew
* **System Architect**: Bamidele Matthew
* **Lead Software Engineer**: Bamidele Matthew
* **GitHub**: [https://github.com/Bamson-dev](https://github.com/Bamson-dev)
* **Repository**: [https://github.com/Bamson-dev/Liventra](https://github.com/Bamson-dev/Liventra)

---

## 📜 Documentation & Contributing

* **[CONTRIBUTING.md](file:///Users/donbamz/Liventra/CONTRIBUTING.md)**: Open-source contribution guidelines.
* **[SECURITY.md](file:///Users/donbamz/Liventra/SECURITY.md)**: Vulnerability disclosure & security policy.
* **[CHANGELOG.md](file:///Users/donbamz/Liventra/CHANGELOG.md)**: Version release notes.
* **[RELEASE.md](file:///Users/donbamz/Liventra/RELEASE.md)**: Release candidate verification process.

---

## ⚖️ License
Liventra is open-source software licensed under the **GNU General Public License v3.0 or later**.
