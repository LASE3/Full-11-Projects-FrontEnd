<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Enterprise Partner Registration - VOSTOKPRIBOR Developer Portal</title>
    <link rel="stylesheet" href="css/dev-tokens.css" />
    <link rel="stylesheet" href="css/dev-common.css" />
    <link rel="stylesheet" href="css/dev-registration.css" />
</head>
<body>
    <!-- TOP NAVIGATION BAR (System 10 Cyan 4px stripe) -->
    <header class="vk-top-navbar">
        <div style="display: flex; align-items: center; gap: 24px;">
            <a class="vk-brand-section" href="index.php">
                <img alt="VOSTOKPRIBOR Official Mark" class="brand-logo-img" style="height: 30px; width: 30px; object-fit: contain;" src="https://lh3.googleusercontent.com/aida/AEtjO1XMGDZ9meLzj0XkVJ6C4Xv2AoEzKtMyFqF7KQ8eMADmbywzsRZ7VF4Em6pQ7fZ8QRJExCZedCKEUUo1fN1LpEmGsQva25blyUsGhOPvX2vv2cHGkppzOp9iT33Xy2n4Nkr5e_YY_0J78vA8Q7vKUpViCouPJo13HFVvW5olf7QEFzU3EX-WCqc0SYyPXZb7E11PafOMC_KprpG6Tre6bN_DyZS-CI-J5qiCgBtikqVNYGueATNgWbi73Q" />
                <div style="display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-family: var(--font-heading); font-weight: 700; font-size: 15px; letter-spacing: -0.02em;">VOSTOKPRIBOR</span>
                        <span class="vk-system-badge">SYS-10 // DEV-PORTAL</span>
                    </div>
                    <span style="font-family: var(--font-mono); font-size: 10px; color: #94A3B8;">ALMATY CENTRAL • EST. 1968 • API GATEWAY v4.12.0</span>
                </div>
            </a>
            <div style="display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.06); padding: 4px 12px; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.1);">
                <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-sys-accent);">how_to_reg</span>
                <span style="font-family: var(--font-mono); font-size: 11px; color: #E2E8F0;">ONBOARDING: <strong>PARTNER ENCLAVE CLEARANCE</strong></span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="position: relative; width: 280px;">
                <span class="material-symbols-outlined text-[16px]" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #64748B;">search</span>
                <input class="search-trigger-input" type="text" placeholder="Search guides, forms (Ctrl + K)" readonly
                    style="width: 100%; height: 32px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: var(--radius-sm); padding-left: 32px; padding-right: 12px; font-family: var(--font-mono); font-size: 11px; color: #ffffff; cursor: pointer;" />
            </div>
            <div style="display: flex; align-items: center; gap: 6px; font-family: var(--font-mono); font-size: 11px; color: #94A3B8; background: rgba(0,0,0,0.25); padding: 4px 10px; border-radius: var(--radius-sm);">
                <span class="material-symbols-outlined text-[14px]" style="color: var(--vk-secondary);">schedule</span>
                <span class="station-live-clock">17:28:00 UTC+6</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; padding-left: 12px; border-left: 1px solid rgba(255,255,255,0.15);">
                <div style="text-align: right;">
                    <div style="font-size: 12px; font-weight: 600; color: #ffffff;">Jonas Richter</div>
                    <div style="font-family: var(--font-mono); font-size: 10px; color: var(--vk-sys-accent);">EMP-1020 • Lead Dev</div>
                </div>
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--vk-primary); display: flex; align-items: center; justify-content: center; border: 1px solid var(--vk-sys-accent);">
                    <span class="material-symbols-outlined text-[18px]" style="color: #ffffff;">person</span>
                </div>
            </div>
        </div>
    </header>

    <!-- LEFT SIDEBAR -->
    <aside class="vk-sidebar">
        <div class="vk-sidebar-nav">
            <div class="vk-sidebar-header">Core Documentation</div>
            <a class="vk-nav-item" href="index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                    <span>API Reference</span>
                </div>
                <span class="vk-tag" style="font-size: 10px;">v4.1</span>
            </a>
            <a class="vk-nav-item" href="guides.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">integration_instructions</span>
                    <span>Integration Guides</span>
                </div>
                <span class="vk-tag vk-tag-internal" style="font-size: 10px;">DOC-2026</span>
            </a>

            <div class="vk-sidebar-header" style="margin-top: 20px;">Developer Tools</div>
            <a class="vk-nav-item" href="credentials.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">key</span>
                    <span>API Credentials</span>
                </div>
            </a>
            <a class="vk-nav-item" href="sandbox.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">terminal</span>
                    <span>Interactive Sandbox</span>
                </div>
            </a>
            <a class="vk-nav-item" href="metrics.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">monitoring</span>
                    <span>Usage &amp; Telemetry</span>
                </div>
            </a>
            <a class="vk-nav-item active" href="partner-registration.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    <span>Partner Registration</span>
                </div>
            </a>

                        <div class="vk-sidebar-header" style="margin-top: 16px;">Unified Ecosystem</div>
            <a class="vk-nav-item" href="../VOSTOKPRIBOR Corporate Web Platform/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: #1B3A5C;">language</span>
                    <span>Corporate Platform</span>
                </div>
                <span class="vk-tag" style="font-size: 9px; background: rgba(27,58,92,0.1); color: #1B3A5C; border: 1px solid #1B3A5C;">SYS-01</span>
            </a>
            <a class="vk-nav-item" href="../Employee Intranet/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: #5C7290;">badge</span>
                    <span>Employee Intranet</span>
                </div>
                <span class="vk-tag" style="font-size: 9px; background: rgba(92,114,144,0.1); color: #5C7290; border: 1px solid #5C7290;">SYS-04</span>
            </a>
            <a class="vk-nav-item" href="../File Center/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: #5A6470;">folder_zip</span>
                    <span>File Center</span>
                </div>
                <span class="vk-tag" style="font-size: 9px; background: rgba(90,100,112,0.1); color: #5A6470; border: 1px solid #5A6470;">SYS-09</span>
            </a>
            <a class="vk-nav-item" href="../Admin & Governance Portal/index.php">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-alert);">shield</span>
                    <span>Admin &amp; Governance</span>
                </div>
                <span class="vk-tag vk-tag-confidential" style="font-size: 10px;">SYS-11</span>
            </a>
        </div>

        <div style="padding: 16px; border-top: 1px solid var(--vk-neutral-200); background: #ffffff;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <span class="vk-status-indicator online"></span>
                <span style="font-family: var(--font-mono); font-size: 11px; font-weight: 600; color: var(--vk-neutral-900);">REGISTRATION ENCLAVE</span>
            </div>
            <div style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600);">Auto-Vetting: Active (Level 2)</div>
            <div style="font-family: var(--font-mono); font-size: 10px; color: var(--vk-neutral-600); margin-top: 4px;">Approval SLA: &lt; 24 Hours</div>
        </div>
    
            <!-- Log Out -->
            <a href="login.php" class="sidebar-nav-item sidebar-nav-item--logout" id="btn-logout" onclick="(function(){sessionStorage.clear();localStorage.clear();})()">
              <div class="sidebar-item-left">
                <span class="sidebar-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                  </svg>
                </span>
                <span>Log Out</span>
              </div>
            </a>
      </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="vk-main-layout">
        <div class="reg-form-container">
            <!-- HEADER BLOCK -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--vk-neutral-200);">
                <div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                        <span class="vk-tag vk-tag-internal">CLASSIFICATION: INTERNAL // #3E7CB1</span>
                        <span style="font-family: var(--font-mono); font-size: 12px; color: var(--vk-neutral-600);">FORM: PARTNER-ONBOARD-v2.4</span>
                    </div>
                    <h1 style="font-size: 26px; font-weight: 700; color: var(--vk-primary); margin: 0; display: flex; align-items: center; gap: 10px;">
                        <span class="material-symbols-outlined" style="font-size: 28px; color: var(--vk-sys-accent);">how_to_reg</span>
                        Enterprise Partner &amp; Client Onboarding
                    </h1>
                    <p style="color: var(--vk-neutral-600); font-size: 14px; margin: 4px 0 0 0;">
                        Apply for automated SCADA gateway API access, issue client certificates, and establish telemetry bridge enclaves with VOSTOKPRIBOR.
                    </p>
                </div>
                <div>
                    <button class="vk-btn vk-btn-outline" id="btn-autofill-baltnord" type="button" style="font-size: 12px;">
                        <span class="material-symbols-outlined text-[16px]">dataset</span> Autofill BaltNord Spec (CUS-1002)
                    </button>
                </div>
            </div>

            <!-- REGISTRATION FORM -->
            <form id="partner-registration-form" class="vk-card" style="margin-bottom: 30px;">
                <div class="vk-card-header">
                    <div>
                        <div class="vk-card-title">Clearance Application Form</div>
                        <div class="vk-card-subtitle">All submitted credentials are encrypted via hardware HSM modules in Almaty Central Datacenter</div>
                    </div>
                    <span class="vk-tag" style="background: rgba(14,124,134,0.1); color: var(--vk-secondary); border: 1px solid var(--vk-secondary);">
                        IEC 62443 COMPLIANT
                    </span>
                </div>

                <div class="vk-card-body">
                    <!-- SECTION 1: ORGANIZATION & CONTACT -->
                    <div style="margin-bottom: 20px;">
                        <h3 style="font-size: 14px; font-weight: 700; color: var(--vk-primary); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.04em; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-sys-accent);">corporate_fare</span>
                            1. Organization &amp; Integration Contact
                        </h3>

                        <div class="form-grid-2col">
                            <div class="form-group">
                                <label class="form-label" for="reg-company">
                                    Organization / Legal Entity <span class="form-label-required">*</span>
                                </label>
                                <input class="form-input" id="reg-company" type="text" placeholder="e.g. BaltNord Process Systems" required />
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="reg-partner-id">
                                    Customer / Partner ID (if known)
                                </label>
                                <input class="form-input" id="reg-partner-id" type="text" placeholder="e.g. CUS-1002 (Leave blank if new partner)" />
                            </div>
                        </div>

                        <div class="form-grid-2col">
                            <div class="form-group">
                                <label class="form-label" for="reg-contact-name">
                                    Lead Technical Contact <span class="form-label-required">*</span>
                                </label>
                                <input class="form-input" id="reg-contact-name" type="text" placeholder="e.g. Kristaps Ozols" required />
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="reg-contact-email">
                                    Engineering Work Email <span class="form-label-required">*</span>
                                </label>
                                <input class="form-input" id="reg-contact-email" type="email" placeholder="e.g. kristaps.ozols@baltnord.lv" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reg-project-ref">
                                Project Reference / Integration Scope
                            </label>
                            <input class="form-input" id="reg-project-ref" type="text" placeholder="e.g. PRJ-2026-002 (Refinery Flow Monitoring & SCADA Bridge)" />
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--vk-neutral-200); margin: 24px 0;"></div>

                    <!-- SECTION 2: ACCESS SCOPE & ENCLAVE -->
                    <div style="margin-bottom: 20px;">
                        <h3 style="font-size: 14px; font-weight: 700; color: var(--vk-primary); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.04em; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-sys-accent);">tune</span>
                            2. Target Environment &amp; Requested Scopes
                        </h3>

                        <div class="form-group">
                            <label class="form-label" for="reg-env">
                                Target Enclave Environment <span class="form-label-required">*</span>
                            </label>
                            <select class="form-select" id="reg-env" required>
                                <option value="sandbox">Sandbox Enclave (Mock SCADA telemetry &amp; simulated responses)</option>
                                <option value="staging">Staging Gateway (Pre-production testing with physical hardware)</option>
                                <option value="production">Production Gateway (Requires L3 Security Officer Attestation)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Requested API Permissions (Scopes) <span class="form-label-required">*</span>
                            </label>
                            <div class="scope-checkbox-grid">
                                <label class="scope-card-option selected">
                                    <input type="checkbox" value="telemetry:read" checked style="margin-top: 2px;" />
                                    <div>
                                        <div style="font-weight: 600; font-size: 12px; color: var(--vk-primary);">telemetry:read</div>
                                        <div style="font-size: 11px; color: var(--vk-neutral-600); margin-top: 2px;">
                                            Read-only access to sensor streams (VP-1001, VP-1002, VP-1004).
                                        </div>
                                    </div>
                                </label>

                                <label class="scope-card-option selected">
                                    <input type="checkbox" value="orders:read_write" checked style="margin-top: 2px;" />
                                    <div>
                                        <div style="font-weight: 600; font-size: 12px; color: var(--vk-primary);">orders:read_write</div>
                                        <div style="font-size: 11px; color: var(--vk-neutral-600); margin-top: 2px;">
                                            B2B spare parts orders, stock queries, and dispatch tracking.
                                        </div>
                                    </div>
                                </label>

                                <label class="scope-card-option">
                                    <input type="checkbox" value="actuator:write" style="margin-top: 2px;" />
                                    <div>
                                        <div style="font-weight: 600; font-size: 12px; color: var(--vk-alert);">actuator:write (RESTRICTED)</div>
                                        <div style="font-size: 11px; color: var(--vk-neutral-600); margin-top: 2px;">
                                            Send control signals to industrial valves and actuators. Requires IEC 62443 L3 clearance.
                                        </div>
                                    </div>
                                </label>

                                <label class="scope-card-option">
                                    <input type="checkbox" value="audit:read" style="margin-top: 2px;" />
                                    <div>
                                        <div style="font-weight: 600; font-size: 12px; color: var(--vk-primary);">audit:read</div>
                                        <div style="font-size: 11px; color: var(--vk-neutral-600); margin-top: 2px;">
                                            Stream security audit logs and gateway transaction history.
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--vk-neutral-200); margin: 24px 0;"></div>

                    <!-- SECTION 3: PUBLIC KEY / MTLS IDENTITY -->
                    <div style="margin-bottom: 20px;">
                        <h3 style="font-size: 14px; font-weight: 700; color: var(--vk-primary); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.04em; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-sys-accent);">key</span>
                            3. Cryptographic Identity (Public Key / CSR)
                        </h3>

                        <div class="form-group">
                            <label class="form-label" for="reg-public-key">
                                Ed25519 Public Key or PEM Certificate Request (Optional for Sandbox)
                            </label>
                            <textarea class="form-textarea" id="reg-public-key" rows="3" placeholder="ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAA... or -----BEGIN CERTIFICATE REQUEST-----" style="font-family: var(--font-mono); font-size: 11px;"></textarea>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--vk-neutral-200); margin: 24px 0;"></div>

                    <!-- SECTION 4: COMPLIANCE & ATTESTATION -->
                    <div style="margin-bottom: 24px;">
                        <h3 style="font-size: 14px; font-weight: 700; color: var(--vk-primary); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.04em; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined text-[18px]" style="color: var(--vk-sys-accent);">gavel</span>
                            4. Regulatory Attestation &amp; Security Compliance
                        </h3>

                        <div class="legal-box">
                            <strong>VOSTOKPRIBOR API ACCESS &amp; TELEMETRY INTERCHANGE AGREEMENT:</strong><br/>
                            1. Access to the VOSTOKPRIBOR Enterprise API Gateway is subject to the security provisions defined in Statutory Document DOC-2026-010 (API_Integration_Guide.pdf).<br/>
                            2. Partner agrees to store API secret tokens and mTLS private keys exclusively within compliant HSM or encrypted key storage enclaves. Hardcoding keys in client-side code is strictly prohibited.<br/>
                            3. Telemetry streams originating from industrial controllers (VP-1001, VP-1002, VP-1004) are subject to Kazakhstani Industrial Cybersecurity Regulations and IEC 62443-4-2 standards.<br/>
                            4. VOSTOKPRIBOR reserves the authority to revoke API tokens immediately upon detecting anomalous polling frequencies or unauthorized actuator command dispatches.
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--vk-neutral-900); cursor: pointer;">
                                <input id="reg-compliance-doc" type="checkbox" required />
                                <span>I have read and agree to comply with <strong>DOC-2026-010 (API Integration Guide)</strong>.</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--vk-neutral-900); cursor: pointer;">
                                <input id="reg-compliance-iec" type="checkbox" required />
                                <span>We affirm that all connecting integration bridges meet <strong>IEC 62443-4-2</strong> cybersecurity levels.</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--vk-neutral-900); cursor: pointer;">
                                <input id="reg-compliance-nda" type="checkbox" required />
                                <span>We accept the bilateral Non-Disclosure Agreement under Almaty Central Jurisdiction.</span>
                            </label>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div style="display: flex; justify-content: flex-end; gap: 12px; align-items: center; padding-top: 16px; border-top: 1px solid var(--vk-neutral-200);">
                        <button class="vk-btn vk-btn-outline" type="reset">Reset Form</button>
                        <button class="vk-btn vk-btn-primary" type="submit">
                            <span class="material-symbols-outlined text-[18px]">verified_user</span>
                            Submit Application for Security Vetting
                        </button>
                    </div>
                </div>
            </form>

            <!-- REGISTRATION SUCCESS CONTAINER (Shown on submit) -->
            <div id="reg-success-container" class="vk-card" style="display: none; margin-bottom: 30px; border-left: 4px solid var(--vk-secondary);">
                <div class="vk-card-header" style="background: rgba(14,124,134,0.05);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span class="material-symbols-outlined text-[28px]" style="color: var(--vk-secondary);">check_circle</span>
                        <div>
                            <div class="vk-card-title">Clearance Request Successfully Enqueued</div>
                            <div class="vk-card-subtitle">Your onboarding application has been transmitted to VOSTOKPRIBOR Systems Engineering</div>
                        </div>
                    </div>
                    <span class="vk-tag vk-tag-internal">STATUS: IN_REVIEW</span>
                </div>

                <div class="vk-card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px; padding: 16px; background: var(--vk-neutral-50); border: 1px solid var(--vk-neutral-200); border-radius: var(--radius-sm);">
                        <div>
                            <div style="font-size: 11px; font-family: var(--font-mono); color: var(--vk-neutral-600);">TICKET ID</div>
                            <div id="disp-reg-ticket" style="font-family: var(--font-mono); font-weight: 700; font-size: 14px; color: var(--vk-primary); margin-top: 2px;">ENCLAVE-REQ-981244</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-family: var(--font-mono); color: var(--vk-neutral-600);">ORGANIZATION</div>
                            <div id="disp-reg-company" style="font-weight: 600; font-size: 13px; color: var(--vk-neutral-900); margin-top: 2px;">BaltNord Process Systems</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-family: var(--font-mono); color: var(--vk-neutral-600);">CONTACT</div>
                            <div id="disp-reg-contact" style="font-size: 13px; color: var(--vk-neutral-900); margin-top: 2px;">Kristaps Ozols</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; font-family: var(--font-mono); color: var(--vk-neutral-600);">TARGET ENCLAVE</div>
                            <div id="disp-reg-env" style="font-family: var(--font-mono); font-weight: 600; font-size: 13px; color: var(--vk-sys-accent); margin-top: 2px;">SANDBOX</div>
                        </div>
                    </div>

                    <p style="font-size: 13px; color: var(--vk-neutral-600); line-height: 1.6;">
                        A security engineer from the Developer &amp; API Portal team (Lead: <strong>Jonas Richter EMP-1020</strong>; Integration: <strong>Dana Yermak EMP-1017</strong>) will inspect your certificate request and provision the initial Sandbox API credentials. You will receive an automated dispatch notification upon clearance.
                    </p>

                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <a class="vk-btn vk-btn-primary" href="credentials.php">
                            <span class="material-symbols-outlined text-[16px]">key</span> Go to API Credentials Vault
                        </a>
                        <a class="vk-btn vk-btn-outline" href="sandbox.php">
                            <span class="material-symbols-outlined text-[16px]">terminal</span> Test Endpoints in Sandbox
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- PUBLIC-FACING / DEVELOPER FOOTER -->
    <footer class="vk-footer">
        <div class="vk-footer-grid">
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <span style="font-weight: 700; font-size: 14px; color: #ffffff; letter-spacing: 0.04em;">VOSTOKPRIBOR</span>
                    <span class="vk-tag" style="background: rgba(30,143,166,0.2); color: var(--vk-sys-accent); border: 1px solid var(--vk-sys-accent); font-size: 10px;">SYSTEM 10</span>
                </div>
                <p style="font-size: 12px; line-height: 1.6; margin: 0 0 12px 0; color: #94A3B8;">
                    Industrial equipment, automation, and logistics systems manufacturer. Established in 1968 in Almaty, Kazakhstan. Developer Platform &amp; API Enclave Gateway.
                </p>
                <div style="font-family: var(--font-mono); font-size: 11px; color: #64748B;">
                    FQDN: developer.vostokpribor.local • Node IP: 10.240.0.12
                </div>
            </div>

            <div>
                <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: #94A3B8; margin-bottom: 8px; letter-spacing: 0.05em;">Developer Resources</div>
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 12px;">
                    <a href="index.php" style="color: #CBD5E1; text-decoration: none;">API Reference (OpenAPI 3.1)</a>
                    <a href="guides.php" style="color: #CBD5E1; text-decoration: none;">Integration Guide (DOC-2026-010)</a>
                    <a href="credentials.php" style="color: #CBD5E1; text-decoration: none;">Partner Key Enclave</a>
                    <a href="sandbox.php" style="color: #CBD5E1; text-decoration: none;">Interactive Dispatch Console</a>
                </div>
            </div>

            <div>
                <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: #94A3B8; margin-bottom: 8px; letter-spacing: 0.05em;">Security &amp; Compliance</div>
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 12px;">
                    <span style="color: #94A3B8;">IEC 62443-4-2 Industrial Security</span>
                    <span style="color: #94A3B8;">ISO 27001 Certified Gateway</span>
                    <span style="color: #94A3B8;">mTLS Ed25519 Partner Clearance</span>
                    <a href="../Admin & Governance Portal/index.php" style="color: var(--vk-alert); text-decoration: none;">Admin Governance Enclave</a>
                </div>
            </div>

            <div>
                <div style="font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; color: #94A3B8; margin-bottom: 8px; letter-spacing: 0.05em;">Engineering Contacts</div>
                <div style="font-size: 12px; line-height: 1.6; color: #94A3B8;">
                    <div><strong>Dana Yermak (EMP-1017)</strong></div>
                    <div style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-sys-accent);">dana.yermak@vostokpribor.local</div>
                    <div style="margin-top: 6px;"><strong>Jonas Richter (EMP-1020)</strong></div>
                    <div style="font-family: var(--font-mono); font-size: 11px; color: var(--vk-sys-accent);">jonas.richter@vostokpribor.local</div>
                </div>
            </div>
        </div>
        <div style="max-width: 1400px; margin: 20px auto 0 auto; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.08); font-family: var(--font-mono); font-size: 11px; color: #64748B; display: flex; justify-content: space-between;">
            <span>&copy; 1968&ndash;2026 VOSTOKPRIBOR. All industrial and telemetric protocols reserved.</span>
            <span>DATA SENSITIVITY: INTERNAL (RESTRICTED TO PARTNER SYSTEMS)</span>
        </div>
    </footer>

    <!-- Universal Command Palette Modal -->
    <div class="cmd-palette-backdrop" id="cmd-palette-modal">
        <div class="cmd-palette-box">
            <div class="cmd-palette-header">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--vk-sys-accent);">terminal</span>
                <input class="cmd-palette-input" id="cmd-palette-input" type="text" placeholder="Type a command or jump to documentation..." />
            </div>
            <div class="cmd-palette-list" id="cmd-palette-results">
                <a class="cmd-palette-item" href="index.php">
                    <span class="material-symbols-outlined text-[16px]">menu_book</span>
                    <span>API Reference &amp; Endpoints</span>
                </a>
                <a class="cmd-palette-item" href="guides.php">
                    <span class="material-symbols-outlined text-[16px]">integration_instructions</span>
                    <span>Integration Guides &amp; DOC-2026-010</span>
                </a>
                <a class="cmd-palette-item" href="credentials.php">
                    <span class="material-symbols-outlined text-[16px]">key</span>
                    <span>API Credentials Vault</span>
                </a>
                <a class="cmd-palette-item" href="sandbox.php">
                    <span class="material-symbols-outlined text-[16px]">terminal</span>
                    <span>Interactive Sandbox Console</span>
                </a>
                <a class="cmd-palette-item" href="metrics.php">
                    <span class="material-symbols-outlined text-[16px]">monitoring</span>
                    <span>Usage Metrics &amp; Telemetry</span>
                </a>
                <a class="cmd-palette-item" href="partner-registration.php">
                    <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                    <span>Enterprise Partner Registration</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Universal Toast Container -->
    <div class="vk-toast-container"></div>

    <script src="js/dev-common.js"></script>
    <script src="js/dev-registration.js"></script>
</body>
</html>
