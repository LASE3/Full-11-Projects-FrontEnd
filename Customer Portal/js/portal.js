/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Shared Architecture & Navigation Controller
 * Handles universal sidebar navigation, active page states, global Ctrl+K search palette,
 * notifications center, user profile menu, deep-linking, and interactive toasts/modals.
 */

(function () {
    'use strict';

    // Route configuration mapping data-path keys to physical files
    const ROUTES = {
        'dashboard': 'Dashboard.html',
        'orders': 'Orders.html',
        'projects': 'ProjectListAndDetail.html',
        'invoices': 'Invoices.html',
        'documents': 'Documents.html',
        'support': 'SupportTicketView.html',
        'account-settings': 'AccountSettings.html'
    };

    // Global Search Index across all portal assets
    const SEARCH_INDEX = [
        // Projects
        { type: 'Project', id: 'PRJ-VP-7721', title: 'Blast Furnace #5 Automation & Gas Analysis Suite', meta: '$1,850,000 • 72% Complete', url: 'ProjectListAndDetail.html?project=PRJ-VP-7721', icon: 'precision_manufacturing', badge: 'Execution' },
        { type: 'Project', id: 'PRJ-VP-6840', title: 'Hot Rolling Mill #2 Continuous Hydraulic Profiler', meta: '$920,000 • 45% Complete', url: 'ProjectListAndDetail.html?project=PRJ-VP-6840', icon: 'precision_manufacturing', badge: 'Integration' },
        { type: 'Project', id: 'PRJ-VP-5510', title: 'Sinter Plant Dust Filtration Optical Pyrometry Array', meta: '$640,000 • 90% Complete', url: 'ProjectListAndDetail.html?project=PRJ-VP-5510', icon: 'precision_manufacturing', badge: 'Commissioning' },

        // Orders
        { type: 'Order', id: 'ORD-2024-8812', title: 'Gas Chromatography Skid #4 Sensors & Manifolds', meta: '$418,200.00 • In Transit (RZD Express)', url: 'Orders.html?order=ORD-2024-8812', icon: 'local_shipping', badge: 'In Transit' },
        { type: 'Order', id: 'ORD-2024-8805', title: 'Blast Furnace #5 Spare Tuyere Pyrometer Sensor Assemblies', meta: '$189,400.00 • Manufacturing / FAT', url: 'Orders.html?order=ORD-2024-8805', icon: 'inventory_2', badge: 'Manufacturing' },
        { type: 'Order', id: 'ORD-2024-8790', title: 'Optical Pyrometer Fiber-Optic Replacement Harnesses', meta: '$64,500.00 • Delivered & Inspected', url: 'Orders.html?order=ORD-2024-8790', icon: 'check_circle', badge: 'Delivered' },

        // Invoices
        { type: 'Invoice', id: 'INV-2024-5890', title: 'Equipment Delivery & Sensor Fabrication Milestone (40%)', meta: '$740,000.00 • Status: Paid', url: 'Invoices.html?invoice=INV-2024-5890', icon: 'receipt_long', badge: 'Paid' },
        { type: 'Invoice', id: 'INV-2024-4411', title: 'Advance Mobilization Payment (30%) - Blast Furnace #5', meta: '$555,000.00 • Status: Paid', url: 'Invoices.html?invoice=INV-2024-4411', icon: 'receipt_long', badge: 'Paid' },
        { type: 'Invoice', id: 'INV-2024-6102', title: 'Cold Commissioning & FAT Signoff Milestone', meta: '$114,200.00 • Due Nov 28, 2024', url: 'Invoices.html?invoice=INV-2024-6102', icon: 'pending_actions', badge: 'Pending' },

        // Documents
        { type: 'Document', id: 'CERT-2024-HPF-0994', title: 'High-Pressure Flowmeter HPF-900X Calibration Certificate', meta: 'Rostest State Protocol #VP-CAL-0994 • SHA-256 Validated', url: 'Documents.html?doc=1', icon: 'verified', badge: 'Rostest Cert' },
        { type: 'Document', id: 'DWG-7721-PND-V3', title: 'Blast Furnace #5 Automation Wiring Schematic & P&ID Diagram', meta: 'CAD Rev 4.2 • 28.2 MB • PE Approved', url: 'Documents.html?doc=2', icon: 'schema', badge: 'P&ID Blueprint' },
        { type: 'Document', id: 'DOC-7721-FAT.pdf', title: 'Factory Acceptance Test (FAT) Protocol - Gas Skid #4', meta: 'Signed QA • 14.8 MB', url: 'Documents.html?doc=3', icon: 'description', badge: 'FAT Protocol' },
        { type: 'Document', id: 'BOL-8812.PDF', title: 'Bill of Lading & Waybill Manifest - ORD-2024-8812', meta: 'RZD Freight Express • Consignment #88192-RU', url: 'Documents.html?doc=4', icon: 'local_shipping', badge: 'Waybill' },

        // Support Tickets
        { type: 'Support Ticket', id: 'TCK-9482', title: 'Sensor Bank #2 Analog Loop Dropout (P1 Critical)', meta: 'Blast Furnace #5 • Viktor Morozov Assigned', url: 'SupportTicketView.html?ticket=TCK-9482', icon: 'warning', badge: 'P1 Critical' },
        { type: 'Support Ticket', id: 'TCK-9440', title: 'Optical Pyrometer Array Temperature Calibration Drift', meta: 'Sinter Plant #3 • Level 2 Investigation', url: 'SupportTicketView.html?ticket=TCK-9440', icon: 'headset_mic', badge: 'High Priority' },
        { type: 'Support Ticket', id: 'TCK-9399', title: 'Replacement Lens Assembly Shipping Tracking & Customs', meta: 'Raw Material Yard LP-400 • Resolved', url: 'SupportTicketView.html?ticket=TCK-9399', icon: 'check_circle', badge: 'Resolved' },
        { type: 'Support Ticket', id: 'TCK-9351', title: 'Hydraulic Pressure Array Firmware Patch Compatibility', meta: 'Hot Strip Mill #2 • Siemens S7-400 PLC', url: 'SupportTicketView.html?ticket=TCK-9351', icon: 'check_circle', badge: 'Resolved' },

        // Settings
        { type: 'Settings', id: 'SET-ORG', title: 'Organization Profile & Facility Identification', meta: 'Severstal Metallurgy Plant #4 • VP-90214-EU', url: 'AccountSettings.html?tab=panel-org', icon: 'corporate_fare', badge: 'Settings' },
        { type: 'Settings', id: 'SET-API', title: 'SCADA Telemetry & REST API Gateway Keys', meta: 'Active Key: vp_live_9941_chrp04_prod', url: 'AccountSettings.html?tab=panel-scada', icon: 'hub', badge: 'API Config' },
        { type: 'Settings', id: 'SET-ROSTER', title: 'User Roster & Security Clearance Credentials', meta: 'Alexey Danilov (Chief Eng.) • Dr. Elena Rostova', url: 'AccountSettings.html?tab=panel-user', icon: 'badge', badge: 'Team Roster' }
    ];

    // Notification Feed Data
    const NOTIFICATIONS = [
        {
            id: 'n1',
            title: 'Critical Incident Alert (P1)',
            desc: 'Sensor Bank #2 analog loop signal dropout on Blast Furnace #5.',
            time: '7 min ago',
            icon: 'warning',
            color: 'text-error',
            bg: 'bg-error/10',
            url: 'SupportTicketView.html?ticket=TCK-9482'
        },
        {
            id: 'n2',
            title: 'Invoice Due Soon',
            desc: 'INV-2024-6102 for Cold Commissioning ($114,200.00) due Nov 28.',
            time: '2 hours ago',
            icon: 'receipt_long',
            color: 'text-on-tertiary-container',
            bg: 'bg-tertiary-fixed/30',
            url: 'Invoices.html?invoice=INV-2024-6102'
        },
        {
            id: 'n3',
            title: 'Rostest Calibration Passport Validated',
            desc: 'High-Pressure Flowmeter HPF-900X metrology protocol cryptographically signed.',
            time: '5 hours ago',
            icon: 'verified',
            color: 'text-secondary',
            bg: 'bg-secondary/10',
            url: 'Documents.html?doc=1'
        },
        {
            id: 'n4',
            title: 'Waybill Transit Update',
            desc: 'ORD-2024-8812 passing Vologda corridor (Hwy A-114 Km 182). ETA tomorrow 14:00.',
            time: '11:42 AM',
            icon: 'local_shipping',
            color: 'text-primary',
            bg: 'bg-primary/10',
            url: 'Orders.html?order=ORD-2024-8812'
        }
    ];

    /**
     * Determine current active path based on URL pathname
     */
    function getCurrentPageKey() {
        const path = window.location.pathname.toLowerCase();
        for (const [key, filename] of Object.entries(ROUTES)) {
            if (path.endsWith(filename.toLowerCase())) {
                return key;
            }
        }
        return 'dashboard';
    }

    /**
     * Wire and standardize sidebar navigation
     */
    function setupSidebar() {
        const currentKey = getCurrentPageKey();
        const navLinks = document.querySelectorAll('aside nav a');

        navLinks.forEach(link => {
            const dataPath = link.getAttribute('data-path');
            if (dataPath && ROUTES[dataPath]) {
                link.setAttribute('href', ROUTES[dataPath]);

                // Reset and set active classes
                if (dataPath === currentKey) {
                    link.setAttribute('aria-current', 'page');
                    link.className = 'flex items-center gap-unit-md px-unit-base py-unit-sm transition-colors bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-tertiary-fixed';
                } else {
                    link.removeAttribute('aria-current');
                    link.className = 'flex items-center gap-unit-md px-unit-base py-unit-sm text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary transition-colors';
                }
            }
        });

        // Wire top logo to Dashboard (skip if toggle button clicked)
        const logoContainers = document.querySelectorAll('header > div:first-child');
        logoContainers.forEach(container => {
            container.addEventListener('click', (e) => {
                if (e.target.closest('#sidebar-toggle-btn')) return;
                if (e.target.tagName !== 'A') {
                    window.location.href = 'Dashboard.html';
                }
            });
        });

        // Wire header technical docs menu_book icon to Documents.html
        const docsIcon = document.querySelector('header a[title="Technical Documentation"]');
        if (docsIcon) {
            docsIcon.setAttribute('href', 'Documents.html');
        }

        // Wire Assigned Manager box to Support page
        const managerCard = document.querySelector('aside .portal-manager-card') || document.querySelector('aside div.p-unit-base');
        if (managerCard) {
            managerCard.style.cursor = 'pointer';
            managerCard.title = 'Click to contact Assigned Manager Viktor Morozov in Support';
            managerCard.addEventListener('click', (e) => {
                if (e.target.closest('a')) return;
                window.location.href = 'SupportTicketView.html?ticket=TCK-9482';
            });
        }

        // Setup Sliding Sidebar Menu (Open/Close & Hover-to-Open)
        setupSlidingSidebar();
    }

    /**
     * Sliding Sidebar Controller (Desktop Collapse, Mobile Off-Canvas Drawer, & Hover-to-Open)
     */
    function setupSlidingSidebar() {
        const sidebar = document.querySelector('aside') || document.getElementById('portal-sidebar');
        const mainWrapper = document.getElementById('portal-main-wrapper') || document.querySelector('.portal-content-wrapper') || document.querySelector('.pl-64');
        const headerLeft = document.querySelector('header > div:first-child');

        // Create mobile backdrop if not existing
        let backdrop = document.getElementById('sidebar-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.id = 'sidebar-backdrop';
            document.body.appendChild(backdrop);
        }

        // Create left-edge hover trigger strip if not existing
        let hoverTrigger = document.getElementById('sidebar-hover-trigger');
        if (!hoverTrigger) {
            hoverTrigger = document.createElement('div');
            hoverTrigger.id = 'sidebar-hover-trigger';
            hoverTrigger.title = 'Hover to reveal Navigation Menu';
            document.body.appendChild(hoverTrigger);
        }

        // If sidebar-toggle-btn doesn't exist in markup, inject it at start of headerLeft
        let toggleBtn = document.getElementById('sidebar-toggle-btn');
        if (!toggleBtn && headerLeft) {
            toggleBtn = document.createElement('button');
            toggleBtn.id = 'sidebar-toggle-btn';
            toggleBtn.className = 'p-1.5 -ml-1 mr-1 rounded text-on-primary-container hover:text-on-primary hover:bg-surface-container-high/10 transition-colors flex items-center justify-center cursor-pointer focus:outline-none';
            toggleBtn.title = 'Toggle Navigation Menu (Slide Open/Close)';
            toggleBtn.innerHTML = '<span class="material-symbols-outlined text-2xl" id="sidebar-toggle-icon">menu</span>';
            headerLeft.insertBefore(toggleBtn, headerLeft.firstChild);
        }

        // Collapse chevron button inside sidebar
        let collapseBtn = document.getElementById('sidebar-collapse-btn');
        if (!collapseBtn && sidebar) {
            const opNavHeader = sidebar.querySelector('div.font-label-caps') || sidebar.querySelector('.px-unit-base');
            if (opNavHeader) {
                opNavHeader.classList.add('flex', 'items-center', 'justify-between');
                collapseBtn = document.createElement('button');
                collapseBtn.id = 'sidebar-collapse-btn';
                collapseBtn.className = 'text-on-primary-container hover:text-on-primary p-0.5 rounded hover:bg-surface-container-high/10 transition-colors cursor-pointer';
                collapseBtn.title = 'Collapse Menu';
                collapseBtn.innerHTML = '<span class="material-symbols-outlined text-base">chevron_left</span>';
                opNavHeader.appendChild(collapseBtn);
            }
        }

        function updateToggleIcon() {
            const icon = document.getElementById('sidebar-toggle-icon');
            if (!icon) return;
            const isMobile = window.innerWidth < 1024;
            if (isMobile) {
                const isOpen = document.body.classList.contains('sidebar-mobile-open');
                icon.textContent = isOpen ? 'close' : 'menu';
            } else {
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                icon.textContent = isCollapsed ? 'menu' : 'menu_open';
            }
        }

        // ==========================================
        // Hover-to-Open Controller
        // ==========================================
        let hoverCloseTimer = null;

        function handleHoverEnter() {
            if (window.innerWidth < 1024) return;
            if (!document.body.classList.contains('sidebar-collapsed')) return;
            if (hoverCloseTimer) {
                clearTimeout(hoverCloseTimer);
                hoverCloseTimer = null;
            }
            document.body.classList.add('sidebar-hover-open');
        }

        function handleHoverLeave() {
            if (window.innerWidth < 1024) return;
            if (!document.body.classList.contains('sidebar-collapsed')) return;
            if (hoverCloseTimer) clearTimeout(hoverCloseTimer);
            hoverCloseTimer = setTimeout(() => {
                document.body.classList.remove('sidebar-hover-open');
            }, 220);
        }

        // Wire hover listeners to hamburger button, left edge trigger, and sidebar itself
        if (toggleBtn) {
            toggleBtn.addEventListener('mouseenter', handleHoverEnter);
            toggleBtn.addEventListener('mouseleave', handleHoverLeave);
        }

        if (hoverTrigger) {
            hoverTrigger.addEventListener('mouseenter', handleHoverEnter);
            hoverTrigger.addEventListener('mouseleave', handleHoverLeave);
        }

        if (sidebar) {
            sidebar.addEventListener('mouseenter', handleHoverEnter);
            sidebar.addEventListener('mouseleave', handleHoverLeave);
        }

        /**
         * Global toggle function
         */
        window.toggleSidebar = function (forceOpen) {
            if (hoverCloseTimer) {
                clearTimeout(hoverCloseTimer);
                hoverCloseTimer = null;
            }
            document.body.classList.remove('sidebar-hover-open');

            const isMobile = window.innerWidth < 1024;
            if (isMobile) {
                const willOpen = typeof forceOpen === 'boolean' ? forceOpen : !document.body.classList.contains('sidebar-mobile-open');
                if (willOpen) {
                    document.body.classList.add('sidebar-mobile-open');
                    backdrop.classList.add('active');
                } else {
                    document.body.classList.remove('sidebar-mobile-open');
                    backdrop.classList.remove('active');
                }
            } else {
                const willCollapse = typeof forceOpen === 'boolean' ? !forceOpen : !document.body.classList.contains('sidebar-collapsed');
                if (willCollapse) {
                    document.body.classList.add('sidebar-collapsed');
                    localStorage.setItem('vstk_sidebar_collapsed', 'true');
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                    localStorage.setItem('vstk_sidebar_collapsed', 'false');
                }
            }
            updateToggleIcon();
        };

        // Wire toggle button
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                window.toggleSidebar();
            });
        }

        // Wire collapse chevron button inside sidebar
        if (collapseBtn) {
            collapseBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (hoverCloseTimer) clearTimeout(hoverCloseTimer);
                document.body.classList.remove('sidebar-hover-open');
                window.toggleSidebar(false);
            });
        }

        // Clicking backdrop closes mobile drawer
        backdrop.addEventListener('click', () => {
            window.toggleSidebar(false);
        });

        // Clicking any nav link in mobile view closes mobile drawer
        if (sidebar) {
            sidebar.querySelectorAll('nav a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) {
                        window.toggleSidebar(false);
                    }
                });
            });
        }

        // Keyboard shortcut: Ctrl+B to toggle menu
        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                window.toggleSidebar();
            } else if (e.key === 'Escape') {
                if (document.body.classList.contains('sidebar-hover-open')) {
                    document.body.classList.remove('sidebar-hover-open');
                }
                if (document.body.classList.contains('sidebar-mobile-open')) {
                    window.toggleSidebar(false);
                }
            }
        });

        // Initialize state from localStorage (desktop)
        if (window.innerWidth >= 1024) {
            const savedCollapsed = localStorage.getItem('vstk_sidebar_collapsed');
            if (savedCollapsed === 'true') {
                document.body.classList.add('sidebar-collapsed');
            }
        }
        updateToggleIcon();

        // Handle resize events
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024 && document.body.classList.contains('sidebar-mobile-open')) {
                document.body.classList.remove('sidebar-mobile-open');
                backdrop.classList.remove('active');
            }
            updateToggleIcon();
        });
    }

    /**
     * Build and inject Notifications Popover
     */
    function setupNotifications() {
        // Find bell container reliably across all pages
        let bellIcon = document.getElementById('header-bell-btn');
        if (!bellIcon) {
            const iconSpan = Array.from(document.querySelectorAll('header span.material-symbols-outlined'))
                .find(el => el.textContent.trim() === 'notifications');
            bellIcon = iconSpan?.closest('div') || iconSpan?.parentElement;
        }
        if (!bellIcon) return;

        bellIcon.id = 'header-bell-btn';
        bellIcon.style.cursor = 'pointer';
        bellIcon.title = 'Operational Telemetry Alerts';

        const popover = document.createElement('div');
        popover.id = 'notifications-popover';
        popover.className = 'fixed right-12 sm:right-24 top-16 w-80 sm:w-96 bg-surface-container-lowest rounded-xl shadow-2xl border border-outline/30 z-[90] hidden flex-col overflow-hidden';
        popover.innerHTML = `
            <div class="flex items-center justify-between px-4 py-3 bg-primary-container text-on-primary">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-tertiary-fixed text-lg">notifications_active</span>
                    <span class="font-headline-sm text-sm font-semibold">Operational Telemetry Alerts</span>
                </div>
                <span id="notif-badge-count" class="px-1.5 py-0.5 rounded bg-tertiary-fixed text-primary font-mono text-[10px] font-bold">4 NEW</span>
            </div>
            <div class="max-h-80 overflow-y-auto divide-y divide-outline/10">
                ${NOTIFICATIONS.map(n => `
                    <a href="${n.url}" class="flex items-start gap-3 p-3.5 hover:bg-surface-container-low transition-colors group">
                        <div class="p-2 rounded ${n.bg} ${n.color} shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-base">${n.icon}</span>
                        </div>
                        <div class="flex flex-col flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-headline-sm text-xs font-semibold text-primary group-hover:text-secondary truncate">${n.title}</span>
                                <span class="text-[10px] text-outline font-mono">${n.time}</span>
                            </div>
                            <p class="text-xs text-on-surface-variant mt-0.5 line-clamp-2">${n.desc}</p>
                        </div>
                    </a>
                `).join('')}
            </div>
            <div class="p-2.5 bg-surface-container-low border-t border-outline/10 flex items-center justify-between text-xs">
                <button id="mark-all-read" class="text-secondary hover:underline font-medium cursor-pointer">Mark all acknowledged</button>
                <a href="SupportTicketView.html" class="text-primary font-semibold flex items-center gap-1 hover:underline">
                    View incident desk <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </a>
            </div>
        `;
        document.body.appendChild(popover);

        bellIcon.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = popover.classList.contains('hidden');
            if (isHidden) {
                popover.classList.remove('hidden');
                popover.classList.add('flex');
                if (profileMenu) profileMenu.classList.add('hidden');
            } else {
                popover.classList.add('hidden');
                popover.classList.remove('flex');
            }
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!popover.contains(e.target) && !bellIcon.contains(e.target)) {
                popover.classList.add('hidden');
                popover.classList.remove('flex');
            }
        });

        // Check session storage for acknowledged state
        if (sessionStorage.getItem('vstk_notifications_read') === 'true') {
            const badge = bellIcon.querySelector('.bg-tertiary-fixed') || document.getElementById('bell-unread-dot');
            if (badge) badge.style.display = 'none';
            const badgeCount = document.getElementById('notif-badge-count');
            if (badgeCount) badgeCount.textContent = '0 NEW';
        }

        const markReadBtn = document.getElementById('mark-all-read');
        if (markReadBtn) {
            markReadBtn.addEventListener('click', () => {
                const badge = bellIcon.querySelector('.bg-tertiary-fixed') || document.getElementById('bell-unread-dot');
                if (badge) badge.style.display = 'none';
                const badgeCount = document.getElementById('notif-badge-count');
                if (badgeCount) badgeCount.textContent = '0 NEW';
                sessionStorage.setItem('vstk_notifications_read', 'true');
                window.showToast('Notifications Acknowledged', 'All live telemetry notices marked as reviewed.', 'info');
                popover.classList.add('hidden');
                popover.classList.remove('flex');
            });
        }
    }

    /**
     * Build and inject Profile Dropdown Menu
     */
    let profileMenu = null;
    function setupProfileMenu() {
        const profileBox = document.querySelector('header .flex.items-center.gap-unit-sm:last-child');
        if (!profileBox) return;

        profileMenu = document.createElement('div');
        profileMenu.id = 'profile-dropdown-menu';
        profileMenu.className = 'fixed right-6 top-16 w-72 bg-surface-container-lowest rounded-xl shadow-2xl border border-outline/30 z-[90] hidden flex-col overflow-hidden';
        profileMenu.innerHTML = `
            <div class="p-4 bg-primary-container text-on-primary">
                <div class="flex items-center gap-3">
                    <img class="w-10 h-10 rounded-full object-cover ring-2 ring-tertiary-fixed/50" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV" alt="Alexey Danilov">
                    <div class="flex flex-col">
                        <span class="font-headline-sm text-sm font-bold text-on-primary leading-tight">Alexey R. Danilov</span>
                        <span class="text-xs text-tertiary-fixed font-mono">Chief Instrumentation Eng.</span>
                        <span class="text-[10px] text-on-primary-container mt-0.5">Severstal Plant #4 • VP-88204-EU</span>
                    </div>
                </div>
            </div>
            <div class="p-2 divide-y divide-outline/10 text-xs font-body-sm">
                <div class="py-1">
                    <a href="AccountSettings.html?tab=panel-org" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-surface-container-low text-primary transition-colors">
                        <span class="material-symbols-outlined text-base text-secondary">factory</span>
                        <span>Plant Cadastre & Org Profile</span>
                    </a>
                    <a href="AccountSettings.html?tab=panel-scada" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-surface-container-low text-primary transition-colors">
                        <span class="material-symbols-outlined text-base text-secondary">hub</span>
                        <span>SCADA & API Keys</span>
                    </a>
                    <a href="AccountSettings.html?tab=panel-user" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-surface-container-low text-primary transition-colors">
                        <span class="material-symbols-outlined text-base text-secondary">badge</span>
                        <span>Security Clearances & Roster</span>
                    </a>
                </div>
                <div class="py-1">
                    <button id="switch-facility-btn" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-surface-container-low text-primary transition-colors text-left">
                        <span class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-base text-secondary">swap_horiz</span>
                            <span>Switch Industrial Node</span>
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-surface-container text-[10px] font-mono">CHRP-04</span>
                    </button>
                    <button id="lock-console-btn" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-error/10 text-error transition-colors text-left">
                        <span class="material-symbols-outlined text-base">lock</span>
                        <span>Lock Security Console</span>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(profileMenu);

        profileBox.style.cursor = 'pointer';
        profileBox.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
            const popover = document.getElementById('notifications-popover');
            if (popover) popover.classList.add('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!profileMenu.contains(e.target) && !profileBox.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
        });

        document.getElementById('switch-facility-btn')?.addEventListener('click', () => {
            profileMenu.classList.add('hidden');
            window.showFacilitySwitchModal();
        });

        document.getElementById('lock-console-btn')?.addEventListener('click', () => {
            profileMenu.classList.add('hidden');
            window.showToast('Console Locked', 'Session locked for security audit. Re-authenticate via PKI card.', 'warning');
        });
    }

    /**
     * Toast notification system
     */
    window.showToast = function (title, message, type = 'success') {
        let container = document.getElementById('portal-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'portal-toast-container';
            container.className = 'fixed bottom-6 right-6 z-[200] flex flex-col gap-2 pointer-events-none';
            document.body.appendChild(container);
        }

        const icons = {
            success: 'check_circle',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        const borderColors = {
            success: 'border-l-4 border-l-secondary',
            error: 'border-l-4 border-l-error',
            warning: 'border-l-4 border-l-on-tertiary-container',
            info: 'border-l-4 border-l-primary'
        };

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-start gap-3 p-4 bg-surface-container-lowest text-primary rounded-lg shadow-xl border border-outline/20 ${borderColors[type] || borderColors.info} w-80 sm:w-96 transition-all duration-300 transform translate-y-4 opacity-0`;
        toast.innerHTML = `
            <span class="material-symbols-outlined text-xl ${type === 'error' ? 'text-error' : type === 'warning' ? 'text-on-tertiary-container' : 'text-secondary'} shrink-0">${icons[type] || 'info'}</span>
            <div class="flex-1 min-w-0">
                <div class="font-headline-sm text-sm font-bold text-primary leading-tight">${title}</div>
                <div class="text-xs text-on-surface-variant mt-1">${message}</div>
            </div>
            <button class="text-outline hover:text-primary transition-colors shrink-0" onclick="this.parentElement.remove()">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        `;

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        });

        // Auto remove
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

    /**
     * Modal dialog utility
     */
    window.openModal = function (htmlContent, onClose) {
        let modalOverlay = document.getElementById('portal-dynamic-modal');
        if (modalOverlay) modalOverlay.remove();

        modalOverlay = document.createElement('div');
        modalOverlay.id = 'portal-dynamic-modal';
        modalOverlay.className = 'fixed inset-0 z-[150] bg-primary/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200';
        modalOverlay.innerHTML = `
            <div class="bg-surface-container-lowest rounded-xl shadow-2xl border border-outline/30 max-w-xl w-full max-h-[90vh] overflow-y-auto p-6 relative">
                <button class="absolute top-4 right-4 text-outline hover:text-primary p-1 rounded-full hover:bg-surface-container transition-colors" id="close-modal-x-btn">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
                <div id="modal-body-content">
                    ${htmlContent}
                </div>
            </div>
        `;
        document.body.appendChild(modalOverlay);

        const closeBtn = document.getElementById('close-modal-x-btn');
        const closeModal = () => {
            modalOverlay.remove();
            if (typeof onClose === 'function') onClose();
        };

        closeBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });

        return closeModal;
    };

    /**
     * Facility switch modal helper
     */
    window.showFacilitySwitchModal = function () {
        window.openModal(`
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3 pb-3 border-b border-outline/20">
                    <div class="p-2.5 rounded bg-primary-container text-tertiary-fixed">
                        <span class="material-symbols-outlined text-xl">swap_horiz</span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-base font-bold text-primary">Switch Industrial Operational Facility</h3>
                        <p class="text-xs text-on-surface-variant">Active Entity: Severstal Metallurgy Division</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="p-3 rounded-lg border-2 border-secondary bg-surface-container-low flex items-center justify-between cursor-pointer">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="facility_choice" checked class="accent-secondary">
                            <div>
                                <div class="font-headline-sm text-sm font-semibold text-primary">Cherepovets Hot Rolling Mill #2 & Blast Furnace #5</div>
                                <div class="text-xs text-on-surface-variant font-mono">Node ID: VP-88204-EU • 14 Active Projects</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-secondary text-on-secondary font-mono text-[10px] font-bold">CONNECTED</span>
                    </label>
                    <label class="p-3 rounded-lg border border-outline/30 hover:bg-surface-container-low flex items-center justify-between cursor-pointer transition-colors">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="facility_choice" class="accent-secondary">
                            <div>
                                <div class="font-headline-sm text-sm font-semibold text-primary">Kolpino High-Precision Sheet Rolling Facility</div>
                                <div class="text-xs text-on-surface-variant font-mono">Node ID: VP-70412-RU • 6 Active Projects</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-surface-container text-outline font-mono text-[10px]">STANDBY</span>
                    </label>
                    <label class="p-3 rounded-lg border border-outline/30 hover:bg-surface-container-low flex items-center justify-between cursor-pointer transition-colors">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="facility_choice" class="accent-secondary">
                            <div>
                                <div class="font-headline-sm text-sm font-semibold text-primary">Cherepovets Sinter Sizing & Pelletizing Plant</div>
                                <div class="text-xs text-on-surface-variant font-mono">Node ID: VP-99104-RU • 3 Active Projects</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-surface-container text-outline font-mono text-[10px]">STANDBY</span>
                    </label>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline/20">
                    <button onclick="document.getElementById('portal-dynamic-modal').remove()" class="px-4 py-2 rounded bg-surface-container hover:bg-surface-container-high text-xs font-semibold">Cancel</button>
                    <button onclick="window.showToast('Node Reconnected', 'Switched telemetry stream context successfully.', 'success'); document.getElementById('portal-dynamic-modal').remove();" class="px-4 py-2 rounded bg-primary text-on-primary text-xs font-semibold">Apply Node Switch</button>
                </div>
            </div>
        `);
    };

    /**
     * Document preview modal helper
     */
    window.previewDocument = function (docId, title, badge) {
        window.openModal(`
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between pb-3 border-b border-outline/20">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-2xl">picture_as_pdf</span>
                        <div>
                            <div class="font-mono text-xs text-secondary font-bold">${docId}</div>
                            <h3 class="font-headline-sm text-sm font-bold text-primary">${title}</h3>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-secondary-fixed text-on-secondary-fixed font-mono text-[10px] font-bold">${badge || 'VALIDATED'}</span>
                </div>
                <div class="bg-surface-container-low p-6 rounded-lg border border-outline/20 flex flex-col items-center justify-center text-center gap-3 min-h-[220px]">
                    <span class="material-symbols-outlined text-5xl text-outline/60">description</span>
                    <div class="max-w-md">
                        <p class="font-headline-sm text-sm font-semibold text-primary">Official GOST / Rostest Cryptographic Asset</p>
                        <p class="text-xs text-on-surface-variant mt-1">SHA-256 Checksum: <code class="font-mono text-secondary bg-surface px-1 py-0.5 rounded">0x8F9A83BC902E4D2</code></p>
                        <p class="text-[11px] text-outline mt-1">Verified with Crypto-Pro CSP 5.0 R3 • State Cadastre Signature Attached</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-secondary"></span>
                        <span class="text-xs text-on-surface-variant">Signature Status: Fully Validated</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="window.showToast('Verification Hash Exported', 'SHA-256 hash copied to clipboard for audit check.', 'info')" class="px-3 py-1.5 rounded bg-surface-container hover:bg-surface-container-high text-xs font-semibold">Verify Hash</button>
                        <button onclick="window.showToast('Download Initiated', '${docId} saved to downloads folder.', 'success')" class="px-3 py-1.5 rounded bg-primary text-on-primary text-xs font-semibold flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">download</span>
                            <span>Download PDF</span>
                        </button>
                    </div>
                </div>
            </div>
        `);
    };

    /**
     * Equipment Dispatch Modal helper
     */
    window.showDispatchModal = function () {
        window.openModal(`
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3 pb-3 border-b border-outline/20">
                    <div class="p-2.5 rounded bg-tertiary-fixed text-primary">
                        <span class="material-symbols-outlined text-xl">local_shipping</span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-base font-bold text-primary">Request Urgent Equipment Dispatch</h3>
                        <p class="text-xs text-on-surface-variant">Tier-1 SLA Priority Supply for Severstal Plant #4</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 text-xs">
                    <div>
                        <label class="font-medium text-on-surface block mb-1">Target Plant Sector / Bay</label>
                        <select class="w-full p-2 rounded bg-surface-container border border-outline/30 text-primary font-body-sm">
                            <option>Cherepovets Plant #4 — Blast Furnace #5 (Mezzanine Bay C)</option>
                            <option>Hot Rolling Mill #2 — Hydraulic Profiler Unit</option>
                            <option>Sinter Sizing Plant #3 — Optical Pyrometer Array</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-medium text-on-surface block mb-1">Equipment / Spares Requirement</label>
                        <input type="text" class="w-full p-2 rounded bg-surface-container border border-outline/30 text-primary font-body-sm" value="Urgent Optical Pyrometer Lens Assembly & Flowmeter Replacement Gaskets">
                    </div>
                    <div>
                        <label class="font-medium text-on-surface block mb-1">Urgency & SLA Response</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="p-2 rounded border border-error bg-error/10 text-error font-medium text-center cursor-pointer flex flex-col items-center">
                                <input type="radio" name="dispatch_prio" checked class="mb-1">
                                <span>P1 Critical</span>
                                <span class="text-[10px]">&lt; 12 hrs Air</span>
                            </label>
                            <label class="p-2 rounded border border-outline/30 text-primary text-center cursor-pointer flex flex-col items-center">
                                <input type="radio" name="dispatch_prio" class="mb-1">
                                <span>High Express</span>
                                <span class="text-[10px]">24 hrs RZD</span>
                            </label>
                            <label class="p-2 rounded border border-outline/30 text-primary text-center cursor-pointer flex flex-col items-center">
                                <input type="radio" name="dispatch_prio" class="mb-1">
                                <span>Standard</span>
                                <span class="text-[10px]">48 hrs Scheduled</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium text-on-surface block mb-1">Dispatch Authorization Reference</label>
                        <input type="text" class="w-full p-2 rounded bg-surface-container border border-outline/30 text-primary font-body-sm" value="AUTH-SEV-DISP-2024-99">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline/20">
                    <button onclick="document.getElementById('portal-dynamic-modal').remove()" class="px-4 py-2 rounded bg-surface-container hover:bg-surface-container-high text-xs font-semibold">Cancel</button>
                    <button onclick="window.showToast('Dispatch Confirmed', 'Consignment scheduled for immediate priority air transit. Reference: DISP-2024-8841', 'success'); document.getElementById('portal-dynamic-modal').remove();" class="px-4 py-2 rounded bg-tertiary-fixed text-primary font-semibold text-xs flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">bolt</span>
                        <span>Authorize Priority Dispatch</span>
                    </button>
                </div>
            </div>
        `);
    };

    /**
     * Handle Deep Linking (query params or hash highlights)
     */
    function handleDeepLinking() {
        const params = new URLSearchParams(window.location.search);

        // Highlight any table row or element with matching ID
        const targetId = params.get('highlight') || params.get('order') || params.get('invoice') || params.get('project') || params.get('ticket');
        if (targetId) {
            setTimeout(() => {
                // Look for element containing targetId in text or attributes
                const el = Array.from(document.querySelectorAll('*')).find(node =>
                    node.children.length === 0 && node.textContent.includes(targetId)
                );
                if (el) {
                    const row = el.closest('tr') || el.closest('div.rounded') || el;
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    row.classList.add('ring-2', 'ring-tertiary-fixed', 'bg-tertiary-fixed/10');
                    setTimeout(() => {
                        row.classList.remove('ring-2', 'ring-tertiary-fixed', 'bg-tertiary-fixed/10');
                    }, 3500);
                }
            }, 300);
        }
    }

    // Initialize on DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        setupSidebar();
        setupCommandPalette();
        setupNotifications();
        setupProfileMenu();
        handleDeepLinking();
    }

})();
