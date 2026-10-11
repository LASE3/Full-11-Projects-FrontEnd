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
  - **Required PHP Extensions**:
    - `pdo` - Core PHP Data Objects database abstraction
    - `pdo_mysql` - MariaDB / MySQL PDO database driver
    - `zip` - ZipArchive support for OpenXML (.docx, .xlsx) generation and inspection
    - `mbstring` - Multibyte string support for UTF-8 character encoding
    - `fileinfo` - Server-side MIME type inspection (`finfo`) for secure upload validation
    - `openssl` - Cryptographic signature, HMAC-SHA256, and AES-256 vault encryption
  - **Required PHP Configuration Settings (`php.ini`)**:
    - `upload_max_filesize = 50M` (matches File Center document intake threshold)
    - `post_max_size = 55M` (must exceed `upload_max_filesize` to avoid empty `$_POST`/`$_FILES` truncation)
    - `memory_limit = 256M` (safe execution headroom for binary stream parsing)
    - `display_errors = Off` (mandatory in production to eliminate server information leakage)
    - `session.cookie_httponly = 1` (blocks JavaScript access to session tokens)
    - `session.cookie_samesite = Lax` (CSRF cross-site mitigation)
    - `session.use_strict_mode = 1` (prevents uninitialized session ID fixation)
- **Frontend**: HTML5, Vanilla CSS3 (Custom Properties & Design Tokens), Vanilla JavaScript (No frameworks, no jQuery).
- **Icons & Typography**: Self-hosted local IBM Plex Sans, JetBrains Mono, and Material Symbols Outlined (`assets/fonts/` + `assets/css/fonts.css`).
- **AJAX & Security**: Centralized `assets/js/vp-ajax.js` with CSRF injection, timeout aborts, in-place DOM updates, and ARIA toasts.
- **Interconnectivity**: Unified `includes/integration_bus.php`, `includes/auth_guard.php`, and `includes/authorize.php`.

---

## 🏗️ What is in this Repo (Repository Map)

The root directory contains the **Ecosystem Launchpad** (`index.php`), which acts as the main entry point. All 11 individual projects are organized into dedicated subfolders with shared core libraries:

```text
Full-11-Projects-FrontEnd/
├── VOSTOKPRIBOR Corporate Web Platform/  # 01 WEB (www.vostokpribor.local)
│   ├── index.php                         # Public showcase, equipment catalog, RFQ
│   ├── admin.php                         # Corporate platform management console
│   └── css/tailwind-compiled.css         # Compiled static Tailwind CSS
├── Online Shop B2B/                     # 02 SHP (shop.vostokpribor.local)
│   ├── index.php                         # Industrial optics storefront & product catalog
│   ├── Dashboard.php                     # B2B buyer account & order management
│   └── api/                              # Orders, quotes, cart endpoints
├── Customer Portal/                     # 03 CUS (portal.vostokpribor.local)
│   ├── Dashboard.php                     # Telemetry overview & project milestones
│   ├── Invoices.php                      # Client invoices & payment verification
│   ├── Documents.php                     # Certified documents & technical specs
│   └── css/tailwind-compiled.css         # Compiled static Tailwind CSS
├── Employee Intranet/                   # 04 EMP (intranet.vostokpribor.local)
│   ├── Dashboard.php                     # Department boards, leave management
│   ├── EmployeeDirectory.php             # Staff directory (role-masked)
│   └── PoliciesAndForms.php              # SOP policies & forms
├── CRM/                                 # 05 CRM (crm.vostokpribor.local)
│   ├── Dashboard.php                     # Sales funnel, rep quotas, pipeline KPIs
│   ├── Opportunities.php                 # Interactive kanban board (AJAX drag & drop)
│   └── Customers.php                     # Enterprise account directory
├── HR System/                           # 06 HR  (hr.vostokpribor.local)
│   ├── Dashboard.php                     # Headcount metrics, turnover telemetry
│   ├── EmployeeRecords.php               # Full HR master records (HRA/EXE only)
│   ├── OnboardingTracker.php             # Stage-by-stage onboarding workflows
│   └── Offboarding.php                   # Separation & security clearance revoking
├── Finance & Billing/                   # 07 FIN (finance.vostokpribor.local)
│   ├── Dashboard.php                     # Cash-flow KPIs, aging ledger
│   ├── Invoices.php                      # Maker-checker dual-control invoicing
│   └── PaymentsReconciliation.php        # SPFS & banking feed reconciliation
├── IT Helpdesk/                         # 08 IT  (helpdesk.vostokpribor.local)
│   ├── Dashboard.php                     # Incident queues, MTTR SLA countdown
│   ├── TicketQueue.php                   # Real-time ticket dispatch & triaging
│   └── AssetManagement.php               # Hardware/software asset register
├── File Center/                         # 09 DOC (files.vostokpribor.local)
│   ├── Dashboard.php                     # Vault ledger, classification index
│   ├── upload.php                        # Drag-and-drop secure file intake
│   └── retention.php                     # Legal holds & lifecycle policies
├── Developer/                           # 10 DEV (developer.vostokpribor.local)
│   ├── Dashboard.php                     # API documentation & gateway specs
│   ├── credentials.php                   # API key vault & hashed client secrets
│   ├── sandbox.php                       # Interactive REST sandbox runner
│   └── partner-registration.php          # External developer clearance intake
├── Admin & Governance Portal/           # 11 ADM (admin.vostokpribor.local)
│   ├── mainDashboard.php                 # Ecosystem telemetry & system health
│   ├── accessMatrix.php                  # RBAC permission matrix manager
│   ├── AuditLogs.php                     # Immutable forensic audit trail
│   ├── EmergencyLockdown.php             # Fail-safe security isolation kill-switch
│   └── css/tailwind-compiled.css         # Compiled static Tailwind CSS
├── assets/                              # Shared Ecosystem Assets
│   ├── css/fonts.css                    # Offline-safe self-hosted typography
│   ├── css/responsive-core.css          # Universal 360-1920px responsive rules
│   ├── fonts/                           # Self-hosted WOFF2 fonts (IBM Plex, JetBrains, Material)
│   ├── js/vp-ajax.js                    # Unified fetch helper, CSRF, debounce, ARIA toasts
│   └── logo.svg                         # Canonical enterprise brand mark
├── config/                              # Configuration & Database Connection
│   └── db.php                           # PDO connection, SSO JWT signer, session guards
├── includes/                            # Core Framework Logic & Security
│   ├── authorize.php                    # Deny-by-default granular authorization engine
│   ├── auth_guard.php                   # System-level authentication barriers
│   ├── integration_bus.php              # Real-time telemetry event bus (`vp_emit()`)
│   ├── audit_logger.php                 # Forensic immutable audit ledger
│   └── id_generator.php                 # Single-source-of-truth ID sequencing
├── DataBase/                            # Database Migrations & Seeds
│   ├── build.php                        # Reproducible zero-step automated database builder
│   ├── migrations/                      # Numbered idempotent SQL migrations (001..015)
│   ├── seed_baseline.sql                # Locked production baseline (95 staff, 8 dept, etc.)
│   └── test_fixtures.sql                # Isolated test fixtures
├── tests/                               # Comprehensive Automated Test Suites
│   ├── test_all_acceptance.php          # 10/10 Master Acceptance Suite
│   ├── test_isolation_matrix.php        # 30/30 Granular User Data Isolation Matrix
│   └── e2e/responsive.spec.js           # 279/279 Playwright offline responsive layout tests
├── .env.example                         # Production environment template (no secrets)
├── index.php                            # Ecosystem Launchpad (all 11 subsystems)
└── login.php                            # Unified Enterprise SSO Gateway
```

---

## 🔑 System Authentication & Security

The entire ecosystem relies on a centralized **Authentication Guard** (`includes/auth_guard.php`) and **SSO Handler** (`login.php`) for secure session handling.

- **Role-Based Access Control (RBAC)**: Strictly enforced via `employee_roles`, `roles`, `permissions`, and `role_system_access`. Never uses hardcoded emails or department arrays.
- **Tenant Isolation**: Inbound customer API calls and data access queries strictly restrict rows where `cus_id` matches the authenticated customer session.
- **Single Sign-On (SSO)**: 8-hour HMAC-SHA256 encrypted SSO cookie containing unique `jti` tracked in `user_sessions`, enabling instant enterprise-wide session revocation upon employee offboarding.
