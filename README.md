# VOSTOKPRIBOR Enterprise Ecosystem

A comprehensive, 11-system digital infrastructure designed for industrial manufacturing and optical instrumentation excellence. This ecosystem serves as the core technological backbone for internal operations, external client interactions, e-commerce, and research & development.

---

## 🚀 System Portfolio

The ecosystem comprises 11 distinct yet interconnected applications, each engineered with a unified design language, shared integration bus (`vp_emit()`), and advanced architectural standards.

| Display | Code | Name | FQDN | Local Directory | Primary Function |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **01** | **WEB** | [Corporate Web Platform](VOSTOKPRIBOR%20Corporate%20Web%20Platform/index.html) | `www.vostokpribor.local` | `VOSTOKPRIBOR Corporate Web Platform/` | Public showcase, equipment catalog, global subsidiaries & RFQ portal. |
| **02** | **SHP** | [Online Shop B2B](Online%20Shop%20B2B/index.html) | `shop.vostokpribor.local` | `Online Shop B2B/` | Industrial optics storefront, bulk order matrices, and logistics checkout. |
| **03** | **CUS** | [Customer Portal](Customer%20Portal/index.html) | `portal.vostokpribor.local` | `Customer Portal/` | Client telemetry, order tracking, and priority ticketing. |
| **04** | **EMP** | [Employee Intranet](Employee%20Intranet/index.html) | `intranet.vostokpribor.local` | `Employee Intranet/` | Internal announcements, SOP policies, department boards, and OPS queue. |
| **05** | **CRM** | [CRM Platform](CRM/index.html) | `crm.vostokpribor.local` | `CRM/` | Sales pipelines, deal negotiation, lead conversion, and client dossier lifecycle. |
| **06** | **HR** | [HR Management System](HR%20System/index.html) | `hr.vostokpribor.local` | `HR System/` | Workforce directory, organizational chart, onboarding, and offboarding. |
| **07** | **FIN** | [Finance & Billing](Finance%20&%20Billing/index.html) | `finance.vostokpribor.local` | `Finance & Billing/` | Multi-currency invoicing, milestone billing, audit, and payment reconciliation. |
| **08** | **IT** | [IT Helpdesk](IT%20Helpdesk/index.html) | `helpdesk.vostokpribor.local` | `IT Helpdesk/` | Incident escalation, asset management, and service desk ticketing. |
| **09** | **DOC** | [File Center](File%20Center/index.html) | `files.vostokpribor.local` | `File Center/` | Secure document repository, versioning, classification, and approval workflows. |
| **10** | **DEV** | [Developer Portal](Developer/index.html) | `developer.vostokpribor.local` | `Developer/` | OpenAPI specifications, partner sandbox, webhooks, and telemetry keys. |
| **11** | **ADM** | [Admin & Governance Portal](Admin%20&%20Governance%20Portal/index.html) | `admin.vostokpribor.local` | `Admin & Governance Portal/` | Access control, audit trails, link health telemetry, and orphaned access audit. |

---

## 🌐 Unified Ecosystem Architecture

The entire platform is unified under a singular architectural vision, ensuring a seamless transition for users moving between different enterprise functions.

### Core Design Principles

- **Unified Navigation**: A persistent sidebar allows instant switching between any of the 11 systems.
- **Consistent Aesthetics**: All systems share the same color palette, typography, and component library.
- **Centralized Integration Bus (`vp_emit`)**: Real-time asynchronous/synchronous event propagation logging to `system_integration_logs`, `audit_logs`, `security_events`, and `portal_notifications`.
- **RBAC & SSO**: Session-cookie hardening (Strict/Secure/HttpOnly for ADM/FIN/HR), CSRF validation, login rate limiting, and 8-hour SSO token validation via `jti`.

### Technology Stack

- **Backend**: PHP 8.2 with PDO, MariaDB 10.4, Strict Typed Database Layer.
- **Frontend**: HTML5, Vanilla CSS3 (Custom Properties & Design Tokens), and Vanilla JavaScript.
- **Icons**: Google Material Symbols.
- **Interconnectivity**: Unified `includes/integration_bus.php` and `includes/auth_guard.php`.

---

## 🏗️ Project Structure

The root directory contains the **Ecosystem Launchpad** (`index.php`), which acts as the main entry point. All 11 individual projects are organized into their own dedicated subfolders.

```text
Full-11-Projects-FrontEnd/
├── VOSTOKPRIBOR Corporate Web Platform/  # 01 WEB (www.vostokpribor.local)
├── Online Shop B2B/                     # 02 SHP (shop.vostokpribor.local)
├── Customer Portal/                     # 03 CUS (portal.vostokpribor.local)
├── Employee Intranet/                   # 04 EMP (intranet.vostokpribor.local)
├── CRM/                                 # 05 CRM (crm.vostokpribor.local)
├── HR System/                           # 06 HR  (hr.vostokpribor.local)
├── Finance & Billing/                   # 07 FIN (finance.vostokpribor.local)
├── IT Helpdesk/                         # 08 IT  (helpdesk.vostokpribor.local)
├── File Center/                         # 09 DOC (files.vostokpribor.local)
├── Developer/                           # 10 DEV (developer.vostokpribor.local)
├── Admin & Governance Portal/           # 11 ADM (admin.vostokpribor.local)
├── config/                      # Database & SSO Configuration
├── includes/                    # Integration Bus, Auth Guard, Enterprise Flows, Audit Logger
├── DataBase/                    # Migrations & Baseline Production Seed
│   ├── migrations/              # 001..006 Idempotent Database Migrations
│   ├── seed_baseline.sql        # Canonical Locked Baseline Seed
│   └── test_fixtures.sql        # Isolated Test Fixtures & Legacy Data
├── tests/                       # Complete Acceptance Test Suite (Phases 1-5)
├── index.php                    # Ecosystem Launchpad
└── login.php                    # Central SSO Login Handler
```

---

## 🔑 System Authentication & Security

The entire ecosystem relies on a centralized **Authentication Guard** (`includes/auth_guard.php`) and **SSO Handler** (`login.php`) for secure session handling.

- **Role-Based Access Control (RBAC)**: Strictly enforced via `employee_roles`, `roles`, `permissions`, and `role_system_access`. Never uses hardcoded emails or department arrays.
- **Tenant Isolation**: Inbound customer API calls and data access queries strictly restrict rows where `cus_id` matches the authenticated customer session.
- **Single Sign-On (SSO)**: 8-hour HMAC-SHA256 encrypted SSO cookie containing unique `jti` tracked in `user_sessions`, enabling instant enterprise-wide session revocation upon employee offboarding.
