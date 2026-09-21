# VOSTOKPRIBOR Enterprise Ecosystem

A comprehensive, 11-system digital infrastructure designed for industrial manufacturing and optical instrumentation excellence. This ecosystem serves as the core technological backbone for internal operations, external client interactions, e-commerce, and research & development.

---

## 🚀 System Portfolio

The ecosystem comprises 11 distinct yet interconnected applications, each engineered with a unified design language and advanced architectural standards.

| System ID | Code | Name | Primary Function |
| :--- | :--- | :--- | :--- |
| **01** | **WEB** | [Corporate Web Platform](VOSTOKPRIBOR%20Corporate%20Web%20Platform/index.html) | Public showcase, equipment catalog, global subsidiaries & RFQ portal. |
| **02** | **SHP** | [Online Shop B2B](VOSTOKPRIBOR%20Online%20Shop%20B2B/index.html) | Industrial optics storefront, bulk order matrices, and logistics checkout. |
| **03** | **CUS** | [Customer Portal](VOSTOKPRIBOR%20Customer%20Portal/index.html) | Client telemetry, order tracking, and priority ticketing. |
| **04** | **EMP** | [Employee Intranet](VOSTOKPRIBOR%20Employee%20Intranet/index.html) | Internal announcements, SOP policies, and employee directory. |
| **05** | **CRM** | [CRM Platform](VOSTOKPRIBOR%20CRM/index.html) | Sales pipelines, deal negotiation, quote generation, and client dossier lifecycle. |
| **06** | **HR** | [HR Management System](VOSTOKPRIBOR%20HR%20System/index.html) | Workforce directory, organizational chart, recruitment, and leave ledger. |
| **07** | **FIN** | [Finance & Billing](VOSTOKPRIBOR%20Finance%20&%20Billing/index.html) | Multi-currency invoicing, departmental budgets, and cashflow analytics. |
| **08** | **IT** | [IT Helpdesk & Service](VOSTOKPRIBOR%20IT%20Helpdesk%20&%20Service/index.html) | Incident escalation, asset management, and service desk ticketing. |
| **09** | **DOC** | [File Center Hub](VOSTOKPRIBOR%20File%20Center%20Hub/index.html) | Secure document repository, versioning, and approval workflows. |
| **10** | **DEV** | [Developer & API Portal](VOSTOKPRIBOR%20Developer%20&%20API%20Portal/index.html) | OpenAPI specifications, partner sandbox, webhooks, and documentation. |
| **11** | **ADM** | [Administration & Governance](VOSTOKPRIBOR%20Administration%20&%20Governance/index.html) | Access control, audit trails, emergency governance, and compliance. |

---

## 🌐 Unified Ecosystem Architecture

The entire platform is unified under a singular architectural vision, ensuring a seamless transition for users moving between different enterprise functions.

### Core Design Principles

- **Unified Navigation**: A persistent sidebar allows instant switching between any of the 11 systems.
- **Consistent Aesthetics**: All systems share the same color palette, typography, and component library.
- **Cross-System Interconnectivity**: Deep links and data references ensure smooth handoffs between related applications (e.g., CRM to Finance, HR to Intranet).

### Technology Stack

- **Frontend**: HTML5, CSS3 (Custom Properties & Design Tokens), and Vanilla JavaScript.
- **Icons**: Google Material Symbols.
- **Interconnectivity**: Leverages shared backend logic for authentication and session management.

---

## 🏗️ Project Structure

The root directory contains the **Ecosystem Launchpad** (`index.php`), which acts as the main entry point. All 11 individual projects are organized into their own dedicated subfolders.

```text
Full-11-Projects-FrontEnd/
├── VOSTOKPRIBOR Corporate Web Platform/  # System 01
├── VOSTOKPRIBOR Online Shop B2B/        # System 02
├── VOSTOKPRIBOR Customer Portal/        # System 03
├── VOSTOKPRIBOR Employee Intranet/      # System 04
├── VOSTOKPRIBOR CRM/                 # System 05
├── VOSTOKPRIBOR HR System/             # System 06
├── VOSTOKPRIBOR Finance & Billing/     # System 07
├── VOSTOKPRIBOR IT Helpdesk & Service/ # System 08
├── VOSTOKPRIBOR File Center Hub/       # System 09
├── VOSTOKPRIBOR Developer & API Portal/  # System 10
├── VOSTOKPRIBOR Administration & Governance/ # System 11
└── index.php                       # Ecosystem Launchpad
```

---

## 🔑 System Authentication & Security

The entire ecosystem relies on a centralized **Authentication Manager** (`login.php`) for secure session handling.

- **Role-Based Access Control (RBAC)**: Each system has distinct access tiers.
  - *Public*: Accessible to all.
  - *Internal*: Requires employee credentials.
  - *Restricted*: Requires specific clearance levels (e.g., HR, Finance, Admin).
  - *Secret*: Top-tier access for governance and sensitive operations.
- **Single Sign-On (SSO)**: Upon successful login, users gain access to all authorized systems without repeated authentication.
- **Centralized Session Management**: PHP sessions store authentication tokens, user roles, and system permissions, ensuring consistency across the entire platform.
