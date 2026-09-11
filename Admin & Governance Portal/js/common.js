/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Global Common Interactivity & Navigation Router
 */

(function () {
    'use strict';

    // ==========================================
    // 1. PAGE ROUTING & NAVIGATION MAPPING
    // ==========================================
    const SYSTEM_NAV_MAP = [
        { path: 'dashboard', href: 'mainDashborde.html', label: 'Main Dashboard', icon: 'dashboard', badge: 'KPI & Threat' },
        { path: 'access-matrix-and-role-review', href: 'accessMatrix.html', label: 'Access Matrix', icon: 'grid_view', badge: '2 Orphaned' },
        { path: 'privileged-accounts-monitoring', href: 'PrivilegedAccounts.html', label: 'Privileged Accounts', icon: 'admin_panel_settings', badge: '7 Active' },
        { path: 'audit-logs-and-event-streams', href: 'AuditLogs.html', label: 'Audit Logs', icon: 'terminal', badge: 'LIVE' },
        { path: 'ingestion-bridges', href: 'IngestionBridges.html', label: 'Ingestion Bridges', icon: 'cable', badge: '01-10' },
        { path: 'emergency-break-glass', href: 'Break-GlassAccess.html', label: 'Break-Glass Access', icon: 'e911_emergency', badge: 'DEFCON-1', isEmergency: true },
        { path: 'enterprise-security-policies', href: 'SecurityPolicies.html', label: 'Security Policies', icon: 'policy', badge: 'DOC-001' },
        { path: 'board-risk-register', href: 'BoardRiskRegister.html', label: 'Board Risk Register', icon: 'balance', badge: 'DOC-015' },
        { path: 'compliance-and-incident-oversight', href: 'ComplianceOversight.html', label: 'Compliance Oversight', icon: 'gavel', badge: 'ST RK' },
        { path: 'emergency-lockdown', href: 'Emergency Lockdown.html', label: 'Emergency Lockdown', icon: 'lock', badge: 'QUARANTINE', isLockdown: true }
    ];

    function getCurrentFileName() {
        const path = window.location.pathname;
        const filename = decodeURIComponent(path.substring(path.lastIndexOf('/') + 1));
        return filename || 'mainDashborde.html';
    }

    function initNavigation() {
        const currentFile = getCurrentFileName();
        const navLinks = document.querySelectorAll('aside nav a');

        navLinks.forEach(link => {
            const dataPath = link.getAttribute('data-path');
            const mapItem = SYSTEM_NAV_MAP.find(item => item.path === dataPath);

            if (mapItem) {
                // Ensure href points to target file
                link.setAttribute('href', mapItem.href);

                // Detect if this is the active page
                const isCurrent = currentFile.toLowerCase() === mapItem.href.toLowerCase() ||
                    (currentFile === '' && mapItem.href === 'mainDashborde.html') ||
                    (currentFile.toLowerCase() === 'index.html' && mapItem.href === 'mainDashborde.html');

                if (isCurrent) {
                    link.setAttribute('aria-current', 'page');
                    if (mapItem.isEmergency) {
                        link.className = 'flex items-center justify-between px-space-sm py-space-xs rounded bg-error-container text-on-error-container font-semibold shadow-sm transition-all font-body-compact text-body-compact border-l-4 border-error';
                    } else {
                        link.className = 'flex items-center justify-between px-space-sm py-space-xs rounded transition-all bg-primary-container text-on-primary font-semibold border-l-4 border-secondary-fixed shadow-sm font-body-compact text-body-compact';
                    }
                } else {
                    link.removeAttribute('aria-current');
                    if (mapItem.isEmergency) {
                        link.className = 'flex items-center justify-between px-space-sm py-space-xs rounded text-error hover:bg-error-container hover:text-on-error-container transition-all font-body-compact text-body-compact';
                    } else {
                        link.className = 'flex items-center justify-between px-space-sm py-space-xs rounded text-on-primary-container hover:bg-primary-container hover:text-on-primary transition-all font-body-compact text-body-compact';
                    }
                }
            }
        });

        // Ensure logo click returns to main dashboard
        const brandHeaders = document.querySelectorAll('header .flex.items-center.gap-space-md:first-child');
        brandHeaders.forEach(el => {
            el.style.cursor = 'pointer';
            el.onclick = () => { window.location.href = 'mainDashborde.html'; };
        });
    }

    // ==========================================
    // 2. LIVE ALMATY STATION CLOCK (UTC+6)
    // ==========================================
    function initStationClock() {
        function updateClock() {
            const now = new Date();
            // Calculate UTC + 6 hours
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const almatyTime = new Date(utc + (3600000 * 6));
            
            const hours = String(almatyTime.getHours()).padStart(2, '0');
            const minutes = String(almatyTime.getMinutes()).padStart(2, '0');
            const seconds = String(almatyTime.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds} UTC+6`;

            document.querySelectorAll('.station-live-clock').forEach(el => {
                el.textContent = timeString;
            });
        }
        updateClock();
        setInterval(updateClock, 1000);
    }

    // ==========================================
    // 3. UNIVERSAL TOAST NOTIFICATION SYSTEM
    // ==========================================
    window.showToast = function (title, message, type = 'info', iconName = 'info') {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type === true ? 'success' : (type === false ? 'error' : type)}`;

        let resolvedIcon = iconName;
        if (type === 'success' || type === true) resolvedIcon = iconName || 'check_circle';
        if (type === 'error' || type === false) resolvedIcon = iconName || 'error';
        if (type === 'warn' || type === 'warning') resolvedIcon = iconName || 'warning';

        toast.innerHTML = `
            <span class="material-symbols-outlined text-[20px] shrink-0" style="color: ${type === 'error' || type === false ? '#ba1a1a' : '#98f0fb'}">${resolvedIcon}</span>
            <div class="flex flex-col flex-1">
                <span class="font-bold tracking-wider uppercase text-[11px] text-white">${title}</span>
                <span class="text-[#abc9f2] text-[11px] leading-relaxed mt-0.5">${message}</span>
            </div>
            <button class="text-[#87a4cc] hover:text-white p-0.5" onclick="this.parentElement.remove()">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.animation = 'toastSlideOut 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }
        }, 5500);
    };

    // ==========================================
    // 4. QUICK SEARCH & COMMAND PALETTE (CTRL + K)
    // ==========================================
    const SEARCH_ENTITIES = [
        { name: 'Main Dashboard (KPI & Threat Overview)', path: 'mainDashborde.html', type: 'View', cat: 'Navigation' },
        { name: 'Access Matrix & Role Entitlement Configurator', path: 'accessMatrix.html', type: 'View', cat: 'Identity' },
        { name: 'Privileged Accounts & Vault Session Control', path: 'PrivilegedAccounts.html', type: 'View', cat: 'Vault' },
        { name: 'Audit Logs & Streaming Event Inspection', path: 'AuditLogs.html', type: 'View', cat: 'Audit' },
        { name: 'Telemetry Ingestion Bridges (SYS 01-10)', path: 'IngestionBridges.html', type: 'View', cat: 'Pipelines' },
        { name: 'Break-Glass Emergency HSM Override', path: 'Break-GlassAccess.html', type: 'View', cat: 'Emergency' },
        { name: 'Enterprise Security Policies (DOC-2026-001)', path: 'SecurityPolicies.html', type: 'View', cat: 'Policies' },
        { name: 'Board Risk Register & Roadmap (DOC-2026-015)', path: 'BoardRiskRegister.html', type: 'View', cat: 'Board' },
        { name: 'Compliance Oversight & Statutory Register', path: 'ComplianceOversight.html', type: 'View', cat: 'Compliance' },
        { name: 'Emergency Lockdown Console (DEFCON-1)', path: 'Emergency Lockdown.html', type: 'View', cat: 'Quarantine' },
        // Baseline Key Personnel
        { name: 'Viktor Sokolov (EMP-1001) - CEO [L4 Clearance]', path: 'mainDashborde.html', type: 'EMP-ID', cat: 'Executive' },
        { name: 'Amina Karimova (EMP-1002) - COO [L4 Clearance]', path: 'mainDashborde.html', type: 'EMP-ID', cat: 'Executive' },
        { name: 'Elena Morozova (EMP-1004) - CTO [L4 Clearance]', path: 'mainDashborde.html', type: 'EMP-ID', cat: 'Executive' },
        { name: 'Timur Akhmetov (EMP-1005) - Chief Governance Officer [L4]', path: 'mainDashborde.html', type: 'EMP-ID', cat: 'Governance' },
        { name: 'Leonid Volkov (EMP-1018) - Systems Engineer [L3 Clearance]', path: 'mainDashborde.html', type: 'EMP-ID', cat: 'Engineering' },
        { name: 'Farida Iskakova (EMP-1019) - Project Manager & Lead Custodian [L3]', path: '../File Center/index.html', type: 'EMP-ID', cat: 'Engineering' },
        { name: 'Jonas Richter (EMP-1020) - Lead Developer [L3 Clearance]', path: 'mainDashborde.html', type: 'EMP-ID', cat: 'Engineering' },
        { name: 'File Center / Document Hub (System 09 // Graphite)', path: '../File Center/index.html', type: 'External', cat: 'File Center' },
        { name: 'DOC-2026-004 BaltNord Integration Spec (System 09)', path: '../File Center/approvals.html', type: 'Doc', cat: 'File Center' },
        { name: 'Developer & API Portal (System 10 // Cyan)', path: '../Developer/index.html', type: 'External', cat: 'Developer' },
        { name: 'DOC-2026-010 API Integration Guide (System 10)', path: '../Developer/guides.html', type: 'Doc', cat: 'Developer' },
        // Statutory Baseline Documents
        { name: 'DOC-2026-001 Corporate Information Security Policy', path: 'SecurityPolicies.html', type: 'Doc', cat: 'Classified' },
        { name: 'DOC-2026-007 Employee Access Matrix (Attestation)', path: 'mainDashborde.html', type: 'Doc', cat: 'Classified' },
        { name: 'DOC-2026-015 Board Risk Register 2026', path: 'BoardRiskRegister.html', type: 'Doc', cat: 'Classified' }
    ];

    function createSearchModal() {
        if (document.getElementById('quickSearchModal')) return;

        const modal = document.createElement('div');
        modal.id = 'quickSearchModal';
        modal.className = 'fixed inset-0 z-50 hidden bg-primary/70 backdrop-blur-sm flex items-start justify-center pt-24 p-4';
        modal.innerHTML = `
            <div class="quick-search-dialog w-full max-w-xl bg-surface-container-lowest border-2 border-primary shadow-2xl rounded overflow-hidden flex flex-col font-telemetry-micro">
                <div class="flex items-center px-4 py-3 bg-primary text-on-primary gap-2">
                    <span class="material-symbols-outlined text-[20px] text-secondary-fixed">search</span>
                    <input id="quickSearchInput" type="text" placeholder="Search systems, EMP-IDs, audit hashes, or policies (Esc to close)..."
                        class="w-full bg-transparent text-on-primary placeholder:text-on-primary-container outline-none font-telemetry-micro text-[13px]" autocomplete="off" />
                    <span class="text-[10px] px-1.5 py-0.5 bg-primary-container text-on-primary-container font-mono rounded">ESC</span>
                </div>
                <div id="quickSearchResults" class="max-h-80 overflow-y-auto p-2 divide-y divide-outline-variant/30"></div>
                <div class="px-4 py-2 bg-surface-container flex items-center justify-between text-[11px] text-on-surface-variant font-mono">
                    <span>Use ↑ ↓ to navigate</span>
                    <span>VOSTOKPRIBOR SYSTEM 11 // JURISDICTION INDEX</span>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        const input = document.getElementById('quickSearchInput');
        const resultsBox = document.getElementById('quickSearchResults');

        function renderResults(query) {
            const q = query.toLowerCase().trim();
            resultsBox.innerHTML = '';
            const filtered = SEARCH_ENTITIES.filter(item => 
                q === '' || item.name.toLowerCase().includes(q) || item.cat.toLowerCase().includes(q) || item.type.toLowerCase().includes(q)
            );

            if (filtered.length === 0) {
                resultsBox.innerHTML = '<div class="p-4 text-center text-on-surface-variant">No matching governance entities found.</div>';
                return;
            }

            filtered.forEach(item => {
                const row = document.createElement('a');
                row.href = item.path;
                row.className = 'flex items-center justify-between p-2 hover:bg-primary-container/20 rounded transition-colors cursor-pointer text-on-surface group';
                row.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded ${item.type === 'EMP-ID' ? 'bg-secondary-container text-on-secondary-container' : (item.type === 'Doc' ? 'bg-error-container text-on-error-container' : 'bg-primary-container text-on-primary-container')}">${item.type}</span>
                        <span class="font-medium text-[12px] group-hover:text-primary">${item.name}</span>
                    </div>
                    <span class="text-[10px] text-outline-variant font-mono">${item.cat}</span>
                `;
                resultsBox.appendChild(row);
            });
        }

        input.addEventListener('input', (e) => renderResults(e.target.value));

        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });

        window.openSearchModal = function () {
            modal.classList.remove('hidden');
            renderResults('');
            input.value = '';
            input.focus();
        };

        window.closeSearchModal = function () {
            modal.classList.add('hidden');
        };
    }

    // Attach search trigger to header search bars and keyboard shortcuts
    function initSearchTriggers() {
        createSearchModal();

        // Keyboard shortcut Ctrl + K or /
        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                window.openSearchModal();
            }
            if (e.key === 'Escape') {
                window.closeSearchModal();
            }
        });

        // Header search inputs
        document.querySelectorAll('header input[type="text"]').forEach(input => {
            input.addEventListener('click', () => window.openSearchModal());
            input.addEventListener('focus', () => window.openSearchModal());
        });
    }

    // ==========================================
    // INITIALIZATION ON DOM READY
    // ==========================================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initNavigation();
            initStationClock();
            initSearchTriggers();
        });
    } else {
        initNavigation();
        initStationClock();
        initSearchTriggers();
    }
})();
