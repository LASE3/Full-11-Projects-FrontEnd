/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Developer & API Portal (developer.vostokpribor.local)
 * Common Interactivity, Navigation Router & Toast Utility
 */

(function () {
    'use strict';

    // 1. Navigation Mapping & Active Tab Detection
    function initNav() {
        const path = window.location.pathname;
        const currentFile = decodeURIComponent(path.substring(path.lastIndexOf('/') + 1)) || 'index.php';

        const navLinks = document.querySelectorAll('.vk-nav-item');
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href) {
                const linkFile = href.split('#')[0];
                if (linkFile === currentFile || (currentFile === '' && linkFile === 'index.php')) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            }
        });
    }

    // 2. Almaty Station Live Clock (UTC+6)
    function initStationClock() {
        function updateClock() {
            const now = new Date();
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const almatyTime = new Date(utc + (3600000 * 6));

            const hours = String(almatyTime.getHours()).padStart(2, '0');
            const minutes = String(almatyTime.getMinutes()).padStart(2, '0');
            const seconds = String(almatyTime.getSeconds()).padStart(2, '0');
            const str = `${hours}:${minutes}:${seconds} UTC+6`;

            document.querySelectorAll('.station-live-clock').forEach(el => {
                el.textContent = str;
            });
        }
        updateClock();
        setInterval(updateClock, 1000);
    }

    // 3. Universal Toast Notification
    window.showToast = function (title, message, type = 'info', iconName = 'info') {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'toast-item';
        if (type === 'success' || type === true) {
            toast.style.borderLeftColor = 'var(--vk-secondary)';
            iconName = 'check_circle';
        } else if (type === 'error' || type === false) {
            toast.style.borderLeftColor = 'var(--vk-class-high-confidential)';
            iconName = 'error';
        } else if (type === 'warn') {
            toast.style.borderLeftColor = 'var(--vk-class-confidential)';
            iconName = 'warning';
        } else {
            toast.style.borderLeftColor = 'var(--vk-sys-accent)';
            iconName = 'info';
        }

        toast.innerHTML = `
            <span class="material-symbols-outlined text-[20px] shrink-0" style="color: ${type === 'error' ? '#B23A32' : '#38BDF8'}">${iconName}</span>
            <div style="flex: 1;">
                <div style="font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 0.06em; color: #FFFFFF;">${title}</div>
                <div style="color: #94A3B8; font-size: 11px; margin-top: 2px; line-height: 1.4;">${message}</div>
            </div>
            <button style="background: transparent; border: none; color: #64748B; cursor: pointer;" onclick="this.parentElement.remove()">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.animation = 'toastSlideOut 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }
        }, 5000);
    };

    // 4. Clipboard Copy Utility
    window.copyText = function (text, label = 'Copied to clipboard') {
        navigator.clipboard.writeText(text).then(() => {
            window.showToast('CLIPBOARD COPIED', label, 'success', 'content_copy');
        }).catch(() => {
            window.showToast('CLIPBOARD FAILED', 'Manual copy required', 'error');
        });
    };

    // 5. Global Command Palette / Search (Ctrl + K)
    const SEARCH_ITEMS = [
        { name: 'GET /v1/sensors/optical/telemetry', cat: 'API Endpoint', href: 'index.php#endpoint-optical' },
        { name: 'GET /v1/devices/geodetic/measurements', cat: 'API Endpoint', href: 'index.php#endpoint-geodetic' },
        { name: 'POST /v1/scada/ingest/frames', cat: 'API Endpoint', href: 'index.php#endpoint-scada' },
        { name: 'POST /v1/b2b/orders/create', cat: 'API Endpoint', href: 'index.php#endpoint-orders' },
        { name: 'DOC-2026-010 API Integration Guide', cat: 'Documentation', href: 'guides.php' },
        { name: 'ERP Integration Standard (SAP / 1C)', cat: 'Guide', href: 'guides.php#erp' },
        { name: 'API Key Management & Vault', cat: 'Credentials', href: 'credentials.php' },
        { name: 'Interactive Request Simulator', cat: 'Sandbox', href: 'sandbox.php' },
        { name: 'Enterprise Partner Onboarding', cat: 'Registration', href: 'partner-registration.php' },
        { name: 'Corporate Web Platform (SYS-01)', cat: 'Ecosystem', href: '../VOSTOKPRIBOR Corporate Web Platform/index.php' },
        { name: 'B2B Online Shop (SYS-02)', cat: 'Ecosystem', href: '../Online Shop B2B/index.php' },
        { name: 'Customer Portal (SYS-03)', cat: 'Ecosystem', href: '../Customer Portal/Dashboard.php' },
        { name: 'Employee Intranet (SYS-04)', cat: 'Ecosystem', href: '../Employee Intranet/index.php' },
        { name: 'CRM Platform (SYS-05)', cat: 'Ecosystem', href: '../CRM/index.php' },
        { name: 'HR System (SYS-06)', cat: 'Ecosystem', href: '../HR System/index.php' },
        { name: 'Finance & Billing (SYS-07)', cat: 'Ecosystem', href: '../Finance & Billing/index.php' },
        { name: 'IT Helpdesk & Service (SYS-08)', cat: 'Ecosystem', href: '../IT Helpdesk/index.php' },
        { name: 'File Center / Document Hub (SYS-09)', cat: 'Ecosystem', href: '../File Center/index.php' },
        { name: 'Admin & Governance Portal (SYS-11)', cat: 'Ecosystem', href: '../Admin & Governance Portal/index.php' }
    ];

    function initSearch() {
        let modal = document.getElementById('searchModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'searchModal';
            modal.className = 'vk-modal-overlay';
            modal.style.display = 'none';
            modal.innerHTML = `
                <div class="vk-modal-dialog" style="max-width: 540px;">
                    <div style="background-color: var(--vk-primary-dark); padding: 14px 18px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <span class="material-symbols-outlined text-[20px]" style="color: var(--vk-sys-accent);">search</span>
                        <input id="quickSearchInput" type="text" placeholder="Search API endpoints, schemas, guides (Esc to close)..."
                            style="flex: 1; background: transparent; border: none; outline: none; font-family: var(--font-mono); font-size: 13px; color: #ffffff;" autocomplete="off" />
                        <span style="font-family: var(--font-mono); font-size: 10px; background: rgba(255,255,255,0.15); color: #94A3B8; padding: 2px 6px; border-radius: 2px;">ESC</span>
                    </div>
                    <div id="quickSearchResults" style="max-height: 320px; overflow-y: auto; padding: 8px;"></div>
                    <div style="background-color: #F8FAFC; padding: 10px 16px; border-top: 1px solid var(--vk-neutral-200); font-family: var(--font-mono); font-size: 11px; color: var(--vk-neutral-600); display: flex; justify-content: space-between;">
                        <span>VOSTOKPRIBOR API REGISTRY</span>
                        <span>Use ↑ ↓ to navigate</span>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            const input = document.getElementById('quickSearchInput');
            const results = document.getElementById('quickSearchResults');

            function renderResults(q) {
                const query = q.toLowerCase().trim();
                results.innerHTML = '';
                const filtered = SEARCH_ITEMS.filter(i => query === '' || i.name.toLowerCase().includes(query) || i.cat.toLowerCase().includes(query));

                if (filtered.length === 0) {
                    results.innerHTML = '<div style="padding: 16px; text-align: center; color: var(--vk-neutral-600); font-size: 12px;">No matching API endpoints or documents.</div>';
                    return;
                }

                filtered.forEach(i => {
                    const row = document.createElement('a');
                    row.href = i.href;
                    row.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: var(--radius-sm); text-decoration: none; color: var(--vk-neutral-900); font-size: 12px; transition: background 0.15s; cursor: pointer;';
                    row.onmouseenter = () => row.style.backgroundColor = 'var(--vk-neutral-50)';
                    row.onmouseleave = () => row.style.backgroundColor = 'transparent';
                    row.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined text-[16px]" style="color: var(--vk-sys-accent);">terminal</span>
                            <span style="font-family: var(--font-mono); font-weight: 500;">${i.name}</span>
                        </div>
                        <span style="font-family: var(--font-mono); font-size: 10px; color: var(--vk-neutral-600);">${i.cat}</span>
                    `;
                    results.appendChild(row);
                });
            }

            input.addEventListener('input', (e) => renderResults(e.target.value));

            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.style.display = 'none';
            });

            window.openSearch = function () {
                modal.style.display = 'flex';
                renderResults('');
                input.value = '';
                input.focus();
            };

            window.closeSearch = function () {
                modal.style.display = 'none';
            };
        }

        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                window.openSearch ? window.openSearch() : null;
            }
            if (e.key === 'Escape') {
                window.closeSearch ? window.closeSearch() : null;
            }
        });

        document.querySelectorAll('.search-trigger-input').forEach(input => {
            input.addEventListener('click', () => window.openSearch());
            input.addEventListener('focus', () => window.openSearch());
        });
    }

    // Initialize on DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initNav();
            initStationClock();
            initSearch();
        });
    } else {
        initNav();
        initStationClock();
        initSearch();
    }
})();
