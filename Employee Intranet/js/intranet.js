/**
 * VOSTOKPRIBOR Employee Intranet (System 04)
 * Consolidated Master JavaScript Engine (Single JS File)
 * FQDN: intranet.vostokpribor.local • Est. 1968 Almaty
 */

(function () {
    'use strict';

    /* ==========================================================================
       1. Baseline Data Repositories (Baseline Cyber Range Standard)
       ========================================================================== */

    const EMPLOYEES_DATA = [
        {
            id: 'EMP-1001',
            name: 'Viktor Sokolov',
            title: 'Chief Executive Officer',
            dept: 'EXE',
            deptName: 'Executive Leadership',
            email: 'viktor.sokolov@vostokpribor.kz',
            phone: '+7 (727) 390-1001',
            clearance: 'L4 Restricted',
            clearanceClass: 'restricted',
            office: 'Almaty HQ, Floor 4, Suite 401',
            status: 'busy',
            avatar: 'VS',
            avatarBg: '#0F2438',
            joined: '1998-04-12',
            projects: ['Strategic Vision 2030', 'Almaty Grid Expansion']
        },
        {
            id: 'EMP-1002',
            name: 'Amina Karimova',
            title: 'Chief Operating Officer',
            dept: 'OPS',
            deptName: 'Operations & Logistics',
            email: 'amina.karimova@vostokpribor.kz',
            phone: '+7 (727) 390-1002',
            clearance: 'L4 Restricted',
            clearanceClass: 'restricted',
            office: 'Almaty HQ, Floor 4, Suite 402',
            status: 'online',
            avatar: 'AK',
            avatarBg: '#0E7C86',
            joined: '2005-09-01',
            projects: ['Supply Chain Automation', 'Assembly Plant Modernization']
        },
        {
            id: 'EMP-1003',
            name: 'Daniel Weber',
            title: 'Chief Financial Officer',
            dept: 'FIN',
            deptName: 'Corporate Finance',
            email: 'daniel.weber@vostokpribor.kz',
            phone: '+7 (727) 390-1003',
            clearance: 'L4 Restricted',
            clearanceClass: 'restricted',
            office: 'Almaty HQ, Floor 3, Suite 301',
            status: 'online',
            avatar: 'DW',
            avatarBg: '#2E6E4E',
            joined: '2012-02-15',
            projects: ['Fiscal Audit 2026', 'ERP SAP Consolidation']
        },
        {
            id: 'EMP-1004',
            name: 'Elena Morozova',
            title: 'Chief Technology Officer & Intranet Sponsor',
            dept: 'ITD',
            deptName: 'Information Technology',
            email: 'elena.morozova@vostokpribor.kz',
            phone: '+7 (727) 390-1004',
            clearance: 'L4 Restricted',
            clearanceClass: 'restricted',
            office: 'Tech Center Alpha, Suite 201',
            status: 'online',
            avatar: 'EM',
            avatarBg: '#1A73E8',
            joined: '2010-06-20',
            projects: ['Industrial Cyber Shield', 'Unified Web Ecosystem']
        },
        {
            id: 'EMP-1005',
            name: 'Timur Akhmetov',
            title: 'Director of Compliance & Governance',
            dept: 'GOV',
            deptName: 'Corporate Governance',
            email: 'timur.akhmetov@vostokpribor.kz',
            phone: '+7 (727) 390-1005',
            clearance: 'L3 Confidential',
            clearanceClass: 'confidential',
            office: 'Almaty HQ, Floor 3, Suite 305',
            status: 'busy',
            avatar: 'TA',
            avatarBg: '#D84315',
            joined: '2014-11-10',
            projects: ['ISO 27001 Certification', 'Export Control Compliance']
        },
        {
            id: 'EMP-1006',
            name: 'Pavel Orlov',
            title: 'Director of Global Sales',
            dept: 'SAL',
            deptName: 'Sales & Business Dev',
            email: 'pavel.orlov@vostokpribor.kz',
            phone: '+7 (727) 390-1006',
            clearance: 'L3 Confidential',
            clearanceClass: 'confidential',
            office: 'Commercial Wing, Suite 101',
            status: 'offline',
            avatar: 'PO',
            avatarBg: '#3949AB',
            joined: '2015-03-01',
            projects: ['Central Asian Rail Tender', 'Pipeline Flow Meters Deal']
        },
        {
            id: 'EMP-1007',
            name: 'Gulnara Kassymova',
            title: 'Head of People & Human Resources',
            dept: 'HRA',
            deptName: 'Human Resources',
            email: 'gulnara.kassymova@vostokpribor.kz',
            phone: '+7 (727) 390-1007',
            clearance: 'L3 Confidential',
            clearanceClass: 'confidential',
            office: 'HR Annex, Suite 102',
            status: 'online',
            avatar: 'GK',
            avatarBg: '#6E4C7C',
            joined: '2013-08-14',
            projects: ['Leadership Mentorship 2026', 'Health & Safety Induction']
        },
        {
            id: 'EMP-1008',
            name: 'Arman Zhumabayev',
            title: 'Chief Automation Engineer',
            dept: 'ENG',
            deptName: 'Engineering & R&D',
            email: 'arman.zhumabayev@vostokpribor.kz',
            phone: '+7 (727) 390-1008',
            clearance: 'L3 Confidential',
            clearanceClass: 'confidential',
            office: 'Engineering Lab 1, Bay C',
            status: 'online',
            avatar: 'AZ',
            avatarBg: '#137333',
            joined: '2016-01-18',
            projects: ['VP-900 PLC Firmware Rev 4', 'RTU Wireless Diagnostics']
        },
        {
            id: 'EMP-1009',
            name: 'Sofia Lindqvist',
            title: 'Procurement & Supply Chain Lead',
            dept: 'OPS',
            deptName: 'Operations & Logistics',
            email: 'sofia.lindqvist@vostokpribor.kz',
            phone: '+7 (727) 390-1009',
            clearance: 'L2 Internal',
            clearanceClass: 'internal',
            office: 'Logistics Hub B, Dispatch Office',
            status: 'online',
            avatar: 'SL',
            avatarBg: '#00796B',
            joined: '2018-05-22',
            projects: ['Rare Earth Metals Sourcing', 'Raw Silicon Buffer']
        },
        {
            id: 'EMP-1010',
            name: 'Ruslan Kim',
            title: 'Senior Network Infrastructure Architect',
            dept: 'ITD',
            deptName: 'Information Technology',
            email: 'ruslan.kim@vostokpribor.kz',
            phone: '+7 (727) 390-1010',
            clearance: 'L3 Confidential',
            clearanceClass: 'confidential',
            office: 'Tech Center Alpha, Server Room Alpha',
            status: 'online',
            avatar: 'RK',
            avatarBg: '#1A73E8',
            joined: '2017-09-04',
            projects: ['Almaty Fiber Ring', 'Zero Trust Network Architecture']
        },
        {
            id: 'EMP-1011',
            name: 'Dmitry Belov',
            title: 'Senior Treasury & Tax Analyst',
            dept: 'FIN',
            deptName: 'Corporate Finance',
            email: 'dmitry.belov@vostokpribor.kz',
            phone: '+7 (727) 390-1011',
            clearance: 'L2 Internal',
            clearanceClass: 'internal',
            office: 'Finance Hub, Suite 302',
            status: 'offline',
            avatar: 'DB',
            avatarBg: '#2E6E4E',
            joined: '2019-10-12',
            projects: ['Q3 Corporate Tax Filing', 'Hedging FX Strategy']
        },
        {
            id: 'EMP-1012',
            name: 'Aigerim Sadykova',
            title: 'Talent Acquisition & Employee Experience Specialist',
            dept: 'HRA',
            deptName: 'Human Resources',
            email: 'aigerim.sadykova@vostokpribor.kz',
            phone: '+7 (727) 390-1012',
            clearance: 'L2 Internal',
            clearanceClass: 'internal',
            office: 'HR Annex, Suite 104',
            status: 'online',
            avatar: 'AS',
            avatarBg: '#6E4C7C',
            joined: '2021-04-05',
            projects: ['Engineering Campus Drive 2026', 'Graduate Apprenticeship']
        },
        {
            id: 'EMP-1013',
            name: 'Marcus Vance',
            title: 'Key Industrial Accounts Manager (Europe/CIS)',
            dept: 'SAL',
            deptName: 'Sales & Business Dev',
            email: 'marcus.vance@vostokpribor.kz',
            phone: '+7 (727) 390-1013',
            clearance: 'L2 Internal',
            clearanceClass: 'internal',
            office: 'Commercial Wing, Suite 104',
            status: 'busy',
            avatar: 'MV',
            avatarBg: '#3949AB',
            joined: '2020-07-15',
            projects: ['PetroKazakhstan Expansion', 'Baku Refinery Transmitters']
        },
        {
            id: 'EMP-1014',
            name: 'Olga Voronova',
            title: 'Quality Assurance & Metrology Lead',
            dept: 'ENG',
            deptName: 'Engineering & R&D',
            email: 'olga.voronova@vostokpribor.kz',
            phone: '+7 (727) 390-1014',
            clearance: 'L2 Internal',
            clearanceClass: 'internal',
            office: 'Calibration Lab 2, Cleanroom',
            status: 'online',
            avatar: 'OV',
            avatarBg: '#137333',
            joined: '2017-02-28',
            projects: ['Pressure Transducer Metrology', 'Atex Safety Verification']
        },
        {
            id: 'EMP-1015',
            name: 'Nurlan Baizhanov',
            title: 'Field Service & Commissioning Supervisor',
            dept: 'OPS',
            deptName: 'Operations & Logistics',
            email: 'nurlan.baizhanov@vostokpribor.kz',
            phone: '+7 (727) 390-1015',
            clearance: 'L2 Internal',
            clearanceClass: 'internal',
            office: 'Plant 3 Workshop, Dispatch 12',
            status: 'online',
            avatar: 'NB',
            avatarBg: '#00796B',
            joined: '2015-11-20',
            projects: ['Karachaganak Field Overhaul', 'Compressor Station Sensors']
        },
        {
            id: 'EMP-1016',
            name: 'Erik Hansen',
            title: 'Senior Automation & SCADA Specialist',
            dept: 'ENG',
            deptName: 'Engineering & R&D',
            email: 'erik.hansen@vostokpribor.kz',
            phone: '+7 (727) 390-1016',
            clearance: 'L3 Confidential',
            clearanceClass: 'confidential',
            office: 'Engineering Lab 4, SCADA Suite',
            status: 'online',
            avatar: 'EH',
            avatarBg: '#137333',
            joined: '2018-08-01',
            projects: ['Modbus TCP Cryptographic Gateway', 'HMI UI Refactor']
        },
        {
            id: 'EMP-1017',
            name: 'Dariya Tulegenova',
            title: 'Internal Communications Coordinator',
            dept: 'HRA',
            deptName: 'Human Resources',
            email: 'dariya.tulegenova@vostokpribor.kz',
            phone: '+7 (727) 390-1017',
            clearance: 'L1 Public',
            clearanceClass: 'public',
            office: 'HR Annex, Suite 101',
            status: 'online',
            avatar: 'DT',
            avatarBg: '#6E4C7C',
            joined: '2022-01-10',
            projects: ['Intranet Newsletter', 'Annual Townhall 2026']
        },
        {
            id: 'EMP-1018',
            name: 'Leonid Volkov',
            title: 'Enterprise Systems & DevOps Administrator',
            dept: 'ITD',
            deptName: 'Information Technology',
            email: 'leonid.volkov@vostokpribor.kz',
            phone: '+7 (727) 390-1018',
            clearance: 'L3 Confidential',
            clearanceClass: 'confidential',
            office: 'Tech Center Alpha, Suite 204',
            status: 'busy',
            avatar: 'LV',
            avatarBg: '#1A73E8',
            joined: '2019-06-17',
            projects: ['LDAP SSO Federation', 'Vostokpribor Container Registry']
        },
        {
            id: 'EMP-1019',
            name: 'Farida Iskakova',
            title: 'Digital Transformation Program Manager',
            dept: 'ITD',
            deptName: 'Information Technology',
            email: 'farida.iskakova@vostokpribor.kz',
            phone: '+7 (727) 390-1019',
            clearance: 'L3 Confidential',
            clearanceClass: 'confidential',
            office: 'Tech Center Alpha, Suite 202',
            status: 'online',
            avatar: 'FI',
            avatarBg: '#1A73E8',
            joined: '2020-11-02',
            projects: ['Customer Portal Rollout', 'Intranet V2 Deployment']
        },
        {
            id: 'EMP-1020',
            name: 'Jonas Richter',
            title: 'Senior Full-Stack & Industrial IoT Developer',
            dept: 'ENG',
            deptName: 'Engineering & R&D',
            email: 'jonas.richter@vostokpribor.kz',
            phone: '+7 (727) 390-1020',
            clearance: 'L3 Confidential',
            clearanceClass: 'confidential',
            office: 'Software Lab 3, Workstation 08',
            status: 'online',
            avatar: 'JR',
            avatarBg: '#137333',
            joined: '2021-09-15',
            projects: ['Telemetry InfluxDB Pipeline', 'WebAssembly Modbus Engine']
        }
    ];

    const POLICIES_DATA = [
        {
            id: 'DOC-2026-001',
            title: 'Enterprise Information Security Policy & Data Governance Rules',
            category: 'Security Policies',
            categoryKey: 'security',
            classification: 'Confidential',
            classCode: 'confidential',
            version: 'v4.2',
            updated: '2026-08-14',
            size: '2.4 MB',
            format: 'PDF',
            author: 'E. Morozova (CTO)'
        },
        {
            id: 'DOC-2026-002',
            title: 'Standard Operating Procedure: Technical Personnel Onboarding',
            category: 'HR Policies',
            categoryKey: 'hr',
            classification: 'Internal',
            classCode: 'internal',
            version: 'v3.1',
            updated: '2026-07-28',
            size: '1.1 MB',
            format: 'PDF',
            author: 'G. Kassymova (HR)'
        },
        {
            id: 'DOC-2026-003',
            title: 'Telecommuting & Remote Network Access Agreement Form',
            category: 'HR Policies',
            categoryKey: 'hr',
            classification: 'Internal',
            classCode: 'internal',
            version: 'v2.0',
            updated: '2026-06-15',
            size: '480 KB',
            format: 'DOCX',
            author: 'G. Kassymova (HR)'
        },
        {
            id: 'DOC-2026-004',
            title: 'Corporate Travel, Per Diem & Commercial Expense Guideline',
            category: 'Expense Forms',
            categoryKey: 'expense',
            classification: 'Internal',
            classCode: 'internal',
            version: 'v5.0',
            updated: '2026-08-01',
            size: '860 KB',
            format: 'PDF',
            author: 'D. Weber (CFO)'
        },
        {
            id: 'DOC-2026-005',
            title: 'Standard Business Expense Claim & Currency Reconcile Sheet',
            category: 'Expense Forms',
            categoryKey: 'expense',
            classification: 'Internal',
            classCode: 'internal',
            version: 'v2.4',
            updated: '2026-05-10',
            size: '320 KB',
            format: 'XLSX',
            author: 'D. Belov (Finance)'
        },
        {
            id: 'DOC-2026-006',
            title: 'Hardware Asset Requisition & Encryption Verification Form',
            category: 'IT Standards',
            categoryKey: 'it',
            classification: 'Internal',
            classCode: 'internal',
            version: 'v1.8',
            updated: '2026-07-19',
            size: '410 KB',
            format: 'PDF',
            author: 'R. Kim (IT)'
        },
        {
            id: 'DOC-2026-007',
            title: 'Enterprise Data Classification System & Cryptographic Key Matrix',
            category: 'Security Policies',
            categoryKey: 'security',
            classification: 'Confidential',
            classCode: 'confidential',
            version: 'v3.0',
            updated: '2026-08-20',
            size: '1.8 MB',
            format: 'PDF',
            author: 'T. Akhmetov (Gov)'
        },
        {
            id: 'DOC-2026-008',
            title: 'Almaty Main Facility Fire & Hazardous Material Protocol',
            category: 'Operations',
            categoryKey: 'templates',
            classification: 'Public',
            classCode: 'public',
            version: 'v6.2',
            updated: '2026-04-12',
            size: '3.2 MB',
            format: 'PDF',
            author: 'A. Karimova (COO)'
        },
        {
            id: 'DOC-2026-009',
            title: 'Annual Paid Leave Requisition & Handover Authorization Form',
            category: 'HR Policies',
            categoryKey: 'hr',
            classification: 'Internal',
            classCode: 'internal',
            version: 'v1.5',
            updated: '2026-03-01',
            size: '290 KB',
            format: 'DOCX',
            author: 'A. Sadykova (HR)'
        },
        {
            id: 'DOC-2026-010',
            title: 'SCADA & Industrial Automation API Integration Specification',
            category: 'IT Standards',
            categoryKey: 'it',
            classification: 'Confidential',
            classCode: 'confidential',
            version: 'v2.2',
            updated: '2026-08-11',
            size: '4.5 MB',
            format: 'PDF',
            author: 'A. Zhumabayev (Eng)'
        },
        {
            id: 'DOC-2026-011',
            title: 'Business Continuity & Disaster Recovery Master Architecture',
            category: 'Security Policies',
            categoryKey: 'security',
            classification: 'Confidential',
            classCode: 'confidential',
            version: 'v4.0',
            updated: '2026-06-30',
            size: '5.1 MB',
            format: 'PDF',
            author: 'E. Morozova (CTO)'
        },
        {
            id: 'DOC-2026-012',
            title: 'VOSTOKPRIBOR Master Corporate Presentation Template (16:9)',
            category: 'Templates',
            categoryKey: 'templates',
            classification: 'Public',
            classCode: 'public',
            version: 'v2026.1',
            updated: '2026-01-15',
            size: '12.4 MB',
            format: 'PPTX',
            author: 'D. Tulegenova (Comms)'
        },
        {
            id: 'DOC-2026-013',
            title: 'Industrial Customer Support Service Level Agreement (SLA Tier 1-3)',
            category: 'Operations',
            categoryKey: 'templates',
            classification: 'Internal',
            classCode: 'internal',
            version: 'v3.3',
            updated: '2026-07-05',
            size: '940 KB',
            format: 'PDF',
            author: 'P. Orlov (Sales)'
        },
        {
            id: 'DOC-2026-014',
            title: 'Corporate Ethics Hotline & Whistleblower Protection Code',
            category: 'Security Policies',
            categoryKey: 'security',
            classification: 'Confidential',
            classCode: 'confidential',
            version: 'v2.1',
            updated: '2026-02-18',
            size: '620 KB',
            format: 'PDF',
            author: 'T. Akhmetov (Gov)'
        },
        {
            id: 'DOC-2026-015',
            title: 'Employee Non-Disclosure & Intellectual Property Assignment Deed',
            category: 'Templates',
            categoryKey: 'templates',
            classification: 'Confidential',
            classCode: 'confidential',
            version: 'v4.1',
            updated: '2026-03-22',
            size: '540 KB',
            format: 'DOCX',
            author: 'T. Akhmetov (Gov)'
        }
    ];

    const CAROUSEL_SLIDES = [
        {
            badge: 'CORPORATE STRATEGY',
            title: 'Q3 Enterprise Assembly Modernization: Plant 2 Transition',
            desc: 'Beginning September 15, Assembly Plant 2 in Almaty will undergo planned calibration robotics retrofits. Field engineering operations will reroute through Hub Alpha.',
            author: 'Amina Karimova • COO',
            date: 'September 08, 2026',
            actionText: 'Read Directive',
            actionDocId: 'DOC-2026-008'
        },
        {
            badge: 'INFORMATION SECURITY',
            title: 'Mandatory Multi-Factor Hardware Token Migration by Sept 30',
            desc: 'All Engineering, Finance, and IT staff holding L3/L4 clearance must complete registration for the new YubiKey 5-Series authentication modules at IT Annex 204.',
            author: 'Elena Morozova • CTO',
            date: 'September 05, 2026',
            actionText: 'Token Schedule',
            actionDocId: 'DOC-2026-001'
        },
        {
            badge: 'ENGINEERING MILESTONE',
            title: 'VP-900 Ultra-Precise Industrial Flow Transmitter Receives CE Mark',
            desc: 'Our flagship telemetry sensor has passed all ATEX and CE European safety benchmarks, enabling distribution in Eurasian and EU heavy oil sectors.',
            author: 'Arman Zhumabayev • Chief Eng',
            date: 'August 31, 2026',
            actionText: 'View Tech Brief',
            actionDocId: 'DOC-2026-010'
        }
    ];

    /* ==========================================================================
       2. Toast Notification Helper
       ========================================================================== */
    function showToast(title, message, type = 'info') {
        let container = document.getElementById('intranet-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'intranet-toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'intranet-toast';

        let iconName = 'info';
        let accentColor = 'var(--system-accent)';
        if (type === 'success') {
            iconName = 'check_circle';
            accentColor = '#137333';
        } else if (type === 'warning') {
            iconName = 'warning';
            accentColor = '#D9822B';
        } else if (type === 'error') {
            iconName = 'error';
            accentColor = '#B23A32';
        }

        toast.style.borderLeftColor = accentColor;
        toast.innerHTML = `
            <span class="material-symbols-outlined" style="color: ${accentColor}; font-size: 1.25rem;">${iconName}</span>
            <div style="flex: 1;">
                <div style="font-weight: 600; font-size: 0.875rem; color: #FFFFFF; margin-bottom: 2px;">${title}</div>
                <div style="font-size: 0.8125rem; color: #BDC6CF; line-height: 1.35;">${message}</div>
            </div>
            <button type="button" style="background: transparent; border: none; color: #939FA8; cursor: pointer; padding: 0; line-height: 1;" onclick="this.parentElement.remove()">
                <span class="material-symbols-outlined" style="font-size: 1.1rem;">close</span>
            </button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.transition = 'opacity 0.3s, transform 0.3s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                setTimeout(() => toast.remove(), 300);
            }
        }, 4000);
    }

    // Expose toast globally
    window.showIntranetToast = showToast;

    /* ==========================================================================
       3. Sidebar Collapsible & Mobile Navigation Logic (Hover-to-Open Enabled)
       ========================================================================== */
    function initSidebar() {
        const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
        const sidebar = document.getElementById('intranet-sidebar');
        const contentWrapper = document.getElementById('intranet-content-wrapper');

        if (!sidebar) return;

        let hoverCloseTimer = null;

        // Restore state from localStorage if available
        const isCollapsed = localStorage.getItem('vostok_intranet_sidebar_collapsed') === 'true';
        if (isCollapsed && window.innerWidth >= 1024) {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
            if (contentWrapper) contentWrapper.classList.add('compact-rail');
        }

        function handleHoverEnter() {
            if (window.innerWidth < 1024) return;
            // Expand on hover if collapsed or rail
            const isCurrentlyCollapsed = sidebar.classList.contains('collapsed') || document.body.classList.contains('sidebar-collapsed');
            if (!isCurrentlyCollapsed) return;

            if (hoverCloseTimer) {
                clearTimeout(hoverCloseTimer);
                hoverCloseTimer = null;
            }
            document.body.classList.add('sidebar-hover-open');
        }

        function handleHoverLeave() {
            if (window.innerWidth < 1024) return;
            if (hoverCloseTimer) clearTimeout(hoverCloseTimer);
            hoverCloseTimer = setTimeout(() => {
                document.body.classList.remove('sidebar-hover-open');
            }, 240);
        }

        // Wire hover events to menu button, sidebar, and left edge trigger
        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('mouseenter', handleHoverEnter);
            sidebarToggleBtn.addEventListener('mouseleave', handleHoverLeave);

            sidebarToggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (hoverCloseTimer) {
                    clearTimeout(hoverCloseTimer);
                    hoverCloseTimer = null;
                }
                document.body.classList.remove('sidebar-hover-open');

                if (window.innerWidth >= 1024) {
                    const willCollapse = !sidebar.classList.contains('collapsed');
                    sidebar.classList.toggle('collapsed', willCollapse);
                    document.body.classList.toggle('sidebar-collapsed', willCollapse);
                    if (contentWrapper) {
                        contentWrapper.classList.toggle('compact-rail', willCollapse);
                    }
                    localStorage.setItem('vostok_intranet_sidebar_collapsed', willCollapse ? 'true' : 'false');
                } else {
                    // Mobile overlay toggle
                    sidebar.classList.toggle('show-mobile');
                    document.body.classList.toggle('sidebar-mobile-open');
                }
            });
        }

        sidebar.addEventListener('mouseenter', handleHoverEnter);
        sidebar.addEventListener('mouseleave', handleHoverLeave);

        // Edge hover trigger strip
        let hoverTrigger = document.getElementById('sidebar-hover-trigger');
        if (!hoverTrigger) {
            hoverTrigger = document.createElement('div');
            hoverTrigger.id = 'sidebar-hover-trigger';
            document.body.appendChild(hoverTrigger);
        }
        hoverTrigger.addEventListener('mouseenter', handleHoverEnter);
        hoverTrigger.addEventListener('mouseleave', handleHoverLeave);

        // Global hotkey Ctrl+B to toggle sidebar
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                if (sidebarToggleBtn) sidebarToggleBtn.click();
            }
        });
    }

    /* ==========================================================================
       4. Top Bar Popovers (Ecosystem & Notifications)
       ========================================================================== */
    function initTopBarPopovers() {
        const ecoBtn = document.getElementById('ecosystem-toggle-btn');
        const ecoDropdown = document.getElementById('ecosystem-dropdown');
        const notifBtn = document.getElementById('notifications-toggle-btn');
        const notifPopover = document.getElementById('notifications-popover');
        const clearNotifsBtn = document.getElementById('clear-notifications-btn');
        const notifBadge = document.getElementById('notif-unread-count');

        if (ecoBtn && ecoDropdown) {
            ecoBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (notifPopover) notifPopover.classList.remove('show');
                ecoDropdown.classList.toggle('show');
            });
        }

        if (notifBtn && notifPopover) {
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (ecoDropdown) ecoDropdown.classList.remove('show');
                notifPopover.classList.toggle('show');
            });
        }

        if (clearNotifsBtn && notifBadge) {
            clearNotifsBtn.addEventListener('click', () => {
                notifBadge.style.display = 'none';
                showToast('Notifications Cleared', 'All pending intranet alerts marked as read.', 'info');
            });
        }

        // Click outside closes popovers
        document.addEventListener('click', (e) => {
            if (ecoDropdown && !ecoDropdown.contains(e.target) && e.target !== ecoBtn) {
                ecoDropdown.classList.remove('show');
            }
            if (notifPopover && !notifPopover.contains(e.target) && e.target !== notifBtn) {
                notifPopover.classList.remove('show');
            }
        });
    }

    /* ==========================================================================
       5. Global Command Palette & Quick Search (Ctrl+K)
       ========================================================================== */
    function initCommandPalette() {
        const modal = document.getElementById('command-palette-modal');
        const searchInput = document.getElementById('command-palette-input');
        const resultsContainer = document.getElementById('command-palette-results');
        const headerSearchInput = document.getElementById('header-search-input');

        if (!modal || !searchInput) return;

        function openPalette(query = '') {
            modal.classList.add('show');
            searchInput.value = query;
            searchInput.focus();
            renderPaletteResults(query);
        }

        function closePalette() {
            modal.classList.remove('show');
        }

        // Shortcut Ctrl+K
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                openPalette();
            } else if (e.key === 'Escape' && modal.classList.contains('show')) {
                closePalette();
            }
        });

        if (headerSearchInput) {
            headerSearchInput.addEventListener('focus', () => {
                openPalette(headerSearchInput.value);
                headerSearchInput.blur();
            });
        }

        const closeBtn = document.getElementById('close-command-palette-btn');
        if (closeBtn) closeBtn.addEventListener('click', closePalette);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closePalette();
        });

        searchInput.addEventListener('input', () => {
            renderPaletteResults(searchInput.value.trim());
        });

        function renderPaletteResults(query) {
            if (!resultsContainer) return;
            const q = query.toLowerCase();

            if (!q) {
                resultsContainer.innerHTML = `
                    <div style="padding: 1.5rem; text-align: center; color: var(--neutral-500); font-size: 0.8125rem;">
                        <span class="material-symbols-outlined" style="font-size: 2rem; color: var(--system-accent); display: block; margin-bottom: 0.5rem;">hub</span>
                        Type to search personnel, policies, departments, and corporate forms...
                    </div>
                `;
                return;
            }

            // Search employees
            const matchingEmployees = EMPLOYEES_DATA.filter(emp =>
                emp.name.toLowerCase().includes(q) ||
                emp.title.toLowerCase().includes(q) ||
                emp.deptName.toLowerCase().includes(q) ||
                emp.id.toLowerCase().includes(q)
            ).slice(0, 4);

            // Search policies
            const matchingPolicies = POLICIES_DATA.filter(doc =>
                doc.title.toLowerCase().includes(q) ||
                doc.id.toLowerCase().includes(q) ||
                doc.category.toLowerCase().includes(q)
            ).slice(0, 4);

            if (matchingEmployees.length === 0 && matchingPolicies.length === 0) {
                resultsContainer.innerHTML = `
                    <div style="padding: 2rem; text-align: center; color: var(--neutral-500); font-size: 0.875rem;">
                        No results found for "<strong>${escapeHtml(query)}</strong>"
                    </div>
                `;
                return;
            }

            let html = '';

            if (matchingEmployees.length > 0) {
                html += `<div style="padding: 0.5rem 1rem; font-size: 0.6875rem; font-family: var(--font-mono); color: var(--neutral-500); font-weight: 600; text-transform: uppercase;">Personnel Directory (${matchingEmployees.length})</div>`;
                matchingEmployees.forEach(emp => {
                    html += `
                        <div class="command-palette-item" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 1rem; cursor: pointer; border-radius: var(--radius-sm); transition: background-color var(--transition-fast);" onclick="window.viewEmployeeCard('${emp.id}')">
                            <div class="avatar-circle" style="background-color: ${emp.avatarBg}; width: 32px; height: 32px; font-size: 0.75rem;">${emp.avatar}</div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-900); display: flex; align-items: center; gap: 0.5rem;">
                                    ${escapeHtml(emp.name)}
                                    <span class="dept-badge ${emp.dept.toLowerCase()}">${emp.dept}</span>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-600);">${escapeHtml(emp.title)}</div>
                            </div>
                            <span class="material-symbols-outlined" style="color: var(--neutral-400); font-size: 1rem;">chevron_right</span>
                        </div>
                    `;
                });
            }

            if (matchingPolicies.length > 0) {
                html += `<div style="padding: 0.5rem 1rem; font-size: 0.6875rem; font-family: var(--font-mono); color: var(--neutral-500); font-weight: 600; text-transform: uppercase; margin-top: 0.5rem;">Policies & Standard Documents (${matchingPolicies.length})</div>`;
                matchingPolicies.forEach(doc => {
                    html += `
                        <div class="command-palette-item" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 1rem; cursor: pointer; border-radius: var(--radius-sm); transition: background-color var(--transition-fast);" onclick="window.downloadDocSimulation('${doc.id}')">
                            <span class="material-symbols-outlined" style="color: var(--system-accent); font-size: 1.25rem;">description</span>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    ${escapeHtml(doc.title)}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--neutral-600); display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-family: var(--font-mono);">${doc.id}</span> • 
                                    <span>${escapeHtml(doc.category)}</span> • 
                                    <span class="badge-classification ${doc.classCode}">${doc.classification}</span>
                                </div>
                            </div>
                            <span class="material-symbols-outlined" style="color: var(--system-accent); font-size: 1rem;">download</span>
                        </div>
                    `;
                });
            }

            resultsContainer.innerHTML = html;
        }
    }

    /* ==========================================================================
       6. Screen 1: Home Feed Logic (Carousel & Dynamic Feed)
       ========================================================================== */
    let currentSlideIdx = 0;
    let carouselTimer = null;

    function initHomeFeed() {
        const carouselContainer = document.getElementById('home-announcement-carousel');
        if (!carouselContainer) return;

        function renderSlide(idx) {
            const slide = CAROUSEL_SLIDES[idx];
            if (!slide) return;

            const badgeEl = document.getElementById('carousel-slide-badge');
            const titleEl = document.getElementById('carousel-slide-title');
            const descEl = document.getElementById('carousel-slide-desc');
            const authorEl = document.getElementById('carousel-slide-author');
            const dateEl = document.getElementById('carousel-slide-date');
            const btnEl = document.getElementById('carousel-slide-btn');

            if (badgeEl) badgeEl.textContent = slide.badge;
            if (titleEl) titleEl.textContent = slide.title;
            if (descEl) descEl.textContent = slide.desc;
            if (authorEl) authorEl.textContent = slide.author;
            if (dateEl) dateEl.textContent = slide.date;
            if (btnEl) {
                btnEl.innerHTML = `${slide.actionText} <span class="material-symbols-outlined" style="font-size: 1.1rem; margin-left: 0.25rem;">arrow_forward</span>`;
                btnEl.onclick = () => window.downloadDocSimulation(slide.actionDocId);
            }

            // Update dots
            const dots = document.querySelectorAll('.carousel-dot');
            dots.forEach((dot, dIdx) => {
                if (dIdx === idx) {
                    dot.style.backgroundColor = '#FFFFFF';
                    dot.style.width = '24px';
                } else {
                    dot.style.backgroundColor = 'rgba(255, 255, 255, 0.4)';
                    dot.style.width = '8px';
                }
            });
        }

        function nextSlide() {
            currentSlideIdx = (currentSlideIdx + 1) % CAROUSEL_SLIDES.length;
            renderSlide(currentSlideIdx);
        }

        function prevSlide() {
            currentSlideIdx = (currentSlideIdx - 1 + CAROUSEL_SLIDES.length) % CAROUSEL_SLIDES.length;
            renderSlide(currentSlideIdx);
        }

        const nextBtn = document.getElementById('carousel-next-btn');
        const prevBtn = document.getElementById('carousel-prev-btn');

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                clearInterval(carouselTimer);
                nextSlide();
                startCarouselTimer();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                clearInterval(carouselTimer);
                prevSlide();
                startCarouselTimer();
            });
        }

        // Dot click handlers
        const dots = document.querySelectorAll('.carousel-dot');
        dots.forEach((dot, dIdx) => {
            dot.addEventListener('click', () => {
                clearInterval(carouselTimer);
                currentSlideIdx = dIdx;
                renderSlide(currentSlideIdx);
                startCarouselTimer();
            });
        });

        function startCarouselTimer() {
            carouselTimer = setInterval(nextSlide, 7000);
        }

        renderSlide(0);
        startCarouselTimer();
    }

    /* ==========================================================================
       7. Screen 2: Employee Directory Logic
       ========================================================================== */
    function initEmployeeDirectory() {
        const grid = document.getElementById('employee-directory-grid');
        if (!grid) return;

        const searchInput = document.getElementById('dir-search-input');
        const deptSelect = document.getElementById('dir-dept-filter');
        const clearanceSelect = document.getElementById('dir-clearance-filter');
        const countBadge = document.getElementById('dir-results-count');

        function filterAndRender() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const dept = deptSelect ? deptSelect.value : 'ALL';
            const clearance = clearanceSelect ? clearanceSelect.value : 'ALL';

            const filtered = EMPLOYEES_DATA.filter(emp => {
                const matchesQuery = !query ||
                    emp.name.toLowerCase().includes(query) ||
                    emp.title.toLowerCase().includes(query) ||
                    emp.email.toLowerCase().includes(query) ||
                    emp.office.toLowerCase().includes(query) ||
                    emp.id.toLowerCase().includes(query);

                const matchesDept = (dept === 'ALL') || (emp.dept === dept);
                const matchesClearance = (clearance === 'ALL') || (emp.clearanceClass === clearance);

                return matchesQuery && matchesDept && matchesClearance;
            });

            if (countBadge) {
                countBadge.textContent = `${filtered.length} of ${EMPLOYEES_DATA.length} Personnel`;
            }

            if (filtered.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; padding: 3rem; text-align: center; background: #FFFFFF; border: 1px dashed var(--neutral-300); border-radius: var(--radius-md);">
                        <span class="material-symbols-outlined" style="font-size: 2.5rem; color: var(--neutral-400); margin-bottom: 0.75rem;">person_search</span>
                        <h4 style="margin: 0 0 0.5rem; font-size: 1rem; color: var(--neutral-800);">No Personnel Found</h4>
                        <p style="margin: 0; color: var(--neutral-600); font-size: 0.8125rem;">Try adjusting your department filter, security clearance, or search term.</p>
                        <button type="button" class="btn btn-secondary" style="margin-top: 1rem;" onclick="window.resetDirectoryFilters()">Reset Filters</button>
                    </div>
                `;
                return;
            }

            grid.innerHTML = filtered.map(emp => `
                <div class="employee-card" data-emp-id="${emp.id}">
                    <div>
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
                            <div class="avatar-circle" style="background-color: ${emp.avatarBg};">
                                ${emp.avatar}
                                <span class="avatar-status-dot ${emp.status}" title="Status: ${emp.status}"></span>
                            </div>
                            <span class="dept-badge ${emp.dept.toLowerCase()}">${emp.dept}</span>
                        </div>

                        <h3 style="margin: 0 0 0.25rem; font-size: 0.9375rem; font-weight: 600; color: var(--neutral-900); line-height: 1.3;">
                            ${escapeHtml(emp.name)}
                        </h3>
                        <p style="margin: 0 0 0.75rem; font-size: 0.75rem; color: var(--neutral-600); line-height: 1.35; min-height: 2rem;">
                            ${escapeHtml(emp.title)}
                        </p>

                        <div style="display: flex; flex-direction: column; gap: 0.375rem; padding-top: 0.75rem; border-top: 1px solid var(--neutral-200); font-size: 0.75rem; color: var(--neutral-700);">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="material-symbols-outlined" style="font-size: 0.95rem; color: var(--neutral-500);">badge</span>
                                <span style="font-family: var(--font-mono); font-weight: 500;">${emp.id}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="material-symbols-outlined" style="font-size: 0.95rem; color: var(--neutral-500);">mail</span>
                                <a href="mailto:${emp.email}" style="color: var(--brand-primary); text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${emp.email}</a>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="material-symbols-outlined" style="font-size: 0.95rem; color: var(--neutral-500);">location_on</span>
                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(emp.office)}</span>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--neutral-200); display: flex; align-items: center; justify-content: space-between;">
                        <span class="badge-classification ${emp.clearanceClass}">${emp.clearance}</span>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="window.viewEmployeeCard('${emp.id}')" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
                            Profile
                        </button>
                    </div>
                </div>
            `).join('');
        }

        if (searchInput) searchInput.addEventListener('input', filterAndRender);
        if (deptSelect) deptSelect.addEventListener('change', filterAndRender);
        if (clearanceSelect) clearanceSelect.addEventListener('change', filterAndRender);

        window.resetDirectoryFilters = function () {
            if (searchInput) searchInput.value = '';
            if (deptSelect) deptSelect.value = 'ALL';
            if (clearanceSelect) clearanceSelect.value = 'ALL';
            filterAndRender();
        };

        filterAndRender();
    }

    /* ==========================================================================
       8. Screen 3: Policies & Forms Library Logic
       ========================================================================== */
    function initPoliciesLibrary() {
        const tableBody = document.getElementById('policies-table-body');
        if (!tableBody) return;

        const searchInput = document.getElementById('policy-search-input');
        const countEl = document.getElementById('policies-count-label');
        const categoryItems = document.querySelectorAll('.policy-category-item');

        let activeCategory = 'all';

        function filterAndRender() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

            const filtered = POLICIES_DATA.filter(doc => {
                const matchesCategory = (activeCategory === 'all') || (doc.categoryKey === activeCategory);
                const matchesQuery = !query ||
                    doc.title.toLowerCase().includes(query) ||
                    doc.id.toLowerCase().includes(query) ||
                    doc.category.toLowerCase().includes(query) ||
                    doc.author.toLowerCase().includes(query);

                return matchesCategory && matchesQuery;
            });

            if (countEl) {
                countEl.textContent = `${filtered.length} Documents Available`;
            }

            if (filtered.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="padding: 3rem; text-align: center; color: var(--neutral-600);">
                            <span class="material-symbols-outlined" style="font-size: 2.5rem; color: var(--neutral-400); margin-bottom: 0.5rem;">folder_off</span>
                            <div style="font-weight: 600; font-size: 0.9375rem; color: var(--neutral-800);">No Matching Documents Found</div>
                            <div style="font-size: 0.8125rem; margin-top: 0.25rem;">Try selecting another category or refining your search.</div>
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = filtered.map(doc => `
                <tr data-classification="${doc.classCode}">
                    <td class="classified-strip" style="font-family: var(--font-mono); font-size: 0.75rem; font-weight: 600; color: var(--neutral-800); white-space: nowrap;">
                        ${doc.id}
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--neutral-900); font-size: 0.8125rem; margin-bottom: 2px;">
                            ${escapeHtml(doc.title)}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--neutral-600);">
                            Author: ${escapeHtml(doc.author)} • Rev: ${doc.version}
                        </div>
                    </td>
                    <td style="font-size: 0.75rem; color: var(--neutral-700); white-space: nowrap;">
                        ${escapeHtml(doc.category)}
                    </td>
                    <td>
                        <span class="badge-classification ${doc.classCode}">${doc.classification}</span>
                    </td>
                    <td style="font-family: var(--font-mono); font-size: 0.75rem; color: var(--neutral-600); white-space: nowrap;">
                        ${doc.updated}
                    </td>
                    <td style="font-family: var(--font-mono); font-size: 0.75rem; color: var(--neutral-600); white-space: nowrap;">
                        ${doc.size}
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="window.downloadDocSimulation('${doc.id}')" title="Download Document">
                            <span class="material-symbols-outlined" style="font-size: 0.9rem; margin-right: 0.25rem;">download</span>
                            ${doc.format}
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        categoryItems.forEach(item => {
            item.addEventListener('click', () => {
                categoryItems.forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                activeCategory = item.getAttribute('data-category') || 'all';
                filterAndRender();
            });
        });

        if (searchInput) searchInput.addEventListener('input', filterAndRender);

        filterAndRender();
    }

    /* ==========================================================================
       9. Employee Card Details Modal Handler
       ========================================================================== */
    window.viewEmployeeCard = function (empId) {
        const emp = EMPLOYEES_DATA.find(e => e.id === empId);
        if (!emp) return;

        let modal = document.getElementById('employee-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'employee-modal';
            modal.className = 'intranet-modal-backdrop';
            document.body.appendChild(modal);
        }

        modal.innerHTML = `
            <div class="intranet-modal-container">
                <div style="background-color: var(--brand-primary-dark); padding: 1.5rem; color: #FFFFFF; position: relative; border-bottom: 3px solid var(--system-accent);">
                    <button type="button" onclick="document.getElementById('employee-modal').classList.remove('show')" style="position: absolute; top: 1.25rem; right: 1.25rem; background: transparent; border: none; color: #BDC6CF; cursor: pointer; padding: 0;">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div class="avatar-circle" style="background-color: ${emp.avatarBg}; width: 56px; height: 56px; font-size: 1.25rem;">
                            ${emp.avatar}
                            <span class="avatar-status-dot ${emp.status}" style="width: 14px; height: 14px;"></span>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: #FFFFFF;">${escapeHtml(emp.name)}</h3>
                                <span class="dept-badge ${emp.dept.toLowerCase()}">${emp.dept}</span>
                            </div>
                            <p style="margin: 0; font-size: 0.8125rem; color: #BDC6CF;">${escapeHtml(emp.title)}</p>
                        </div>
                    </div>
                </div>

                <div style="padding: 1.5rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; font-size: 0.8125rem;">
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.75rem; margin-bottom: 2px;">EMPLOYEE ID</span>
                            <span style="font-family: var(--font-mono); font-weight: 600; color: var(--neutral-800);">${emp.id}</span>
                        </div>
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.75rem; margin-bottom: 2px;">SECURITY CLEARANCE</span>
                            <span class="badge-classification ${emp.clearanceClass}">${emp.clearance}</span>
                        </div>
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.75rem; margin-bottom: 2px;">DEPARTMENT</span>
                            <span style="font-weight: 500; color: var(--neutral-800);">${escapeHtml(emp.deptName)}</span>
                        </div>
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.75rem; margin-bottom: 2px;">LOCATION</span>
                            <span style="font-weight: 500; color: var(--neutral-800);">${escapeHtml(emp.office)}</span>
                        </div>
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.75rem; margin-bottom: 2px;">CORPORATE EMAIL</span>
                            <a href="mailto:${emp.email}" style="color: var(--brand-primary); font-weight: 500; text-decoration: none;">${emp.email}</a>
                        </div>
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.75rem; margin-bottom: 2px;">INTERNAL EXTENSION</span>
                            <span style="font-family: var(--font-mono); font-weight: 600; color: var(--neutral-800);">${emp.phone}</span>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--neutral-200); padding-top: 1rem; margin-bottom: 1.25rem;">
                        <span style="color: var(--neutral-500); display: block; font-size: 0.75rem; margin-bottom: 0.5rem; font-weight: 600;">ACTIVE INITIATIVES & RESPONSIBILITIES</span>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            ${emp.projects.map(p => `<span style="background: var(--neutral-100); border: 1px solid var(--neutral-200); padding: 0.25rem 0.6rem; border-radius: var(--radius-sm); font-size: 0.75rem; color: var(--neutral-700);">${escapeHtml(p)}</span>`).join('')}
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('employee-modal').classList.remove('show')">Close</button>
                        <a href="mailto:${emp.email}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.375rem; text-decoration: none;">
                            <span class="material-symbols-outlined" style="font-size: 1rem;">send</span>
                            Send Email
                        </a>
                    </div>
                </div>
            </div>
        `;

        modal.classList.add('show');
        modal.onclick = (e) => {
            if (e.target === modal) modal.classList.remove('show');
        };
    };

    /* ==========================================================================
       10. Simulation Action Helpers (Leave Request, IT Ticket, Doc Download)
       ========================================================================== */
    window.downloadDocSimulation = function (docId) {
        const doc = POLICIES_DATA.find(d => d.id === docId);
        const name = doc ? doc.title : docId;
        showToast('Document Download Initiated', `Downloading "${name}" (${doc ? doc.size : 'PDF'}). Secure audit log registered.`, 'success');
    };

    window.openQuickRequestModal = function (type) {
        let modal = document.getElementById('request-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'request-modal';
            modal.className = 'intranet-modal-backdrop';
            document.body.appendChild(modal);
        }

        const isLeave = type === 'leave';

        if (isLeave) {
            modal.innerHTML = `
                <div class="intranet-modal-container">
                    <div style="background-color: var(--brand-primary-dark); padding: 1.25rem 1.5rem; color: #FFFFFF; position: relative; border-bottom: 3px solid var(--system-accent);">
                        <button type="button" onclick="document.getElementById('request-modal').classList.remove('show')" style="position: absolute; top: 1.25rem; right: 1.25rem; background: transparent; border: none; color: #BDC6CF; cursor: pointer; padding: 0;">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span class="material-symbols-outlined" style="font-size: 1.75rem; color: var(--brand-accent);">event_available</span>
                            <div>
                                <h3 style="margin: 0 0 0.15rem; font-size: 1.1rem; font-weight: 600; color: #FFFFFF;">Leave & Absence Requisition</h3>
                                <p style="margin: 0; font-size: 0.75rem; color: #BDC6CF;">HR Standard Form DOC-2026-009 • Direct supervisor approval workflow</p>
                            </div>
                        </div>
                    </div>

                    <form id="quick-action-form" style="padding: 1.5rem;" onsubmit="event.preventDefault(); window.submitQuickAction('leave')">
                        <div class="form-group">
                            <label class="form-label">Absence Category</label>
                            <select class="form-select" required>
                                <option value="annual">Annual Paid Vacation (Accrual balance: 18.5 days)</option>
                                <option value="field">Field Engineering / Commissioning Assignment</option>
                                <option value="medical">Medical / Health Absence</option>
                                <option value="compassionate">Compassionate Leave</option>
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="form-group">
                            <div>
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-input" required value="2026-09-18" />
                            </div>
                            <div>
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-input" required value="2026-09-25" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Operational Handover Personnel</label>
                            <input type="text" class="form-input" placeholder="e.g. Farida Iskakova (ITD) - Systems PM" value="Farida Iskakova (ITD)" required />
                            <div class="form-hint">Designated colleague to cover urgent incidents during your absence.</div>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--neutral-200); padding-top: 1.25rem; margin-top: 0.5rem;">
                            <span style="font-size: 0.75rem; color: var(--neutral-500); font-family: var(--font-mono);">Routing: HRA & Exec Review</span>
                            <div style="display: flex; gap: 0.75rem;">
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('request-modal').classList.remove('show')">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="material-symbols-outlined" style="font-size: 1rem;">send</span>
                                    Submit Absence Requisition
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            `;
        } else {
            // Enterprise IT Helpdesk & Support Hub
            modal.innerHTML = `
                <div class="intranet-modal-container modal-lg">
                    
                    <!-- Header -->
                    <div style="background-color: var(--brand-primary-dark); padding: 1.25rem 1.75rem; color: #FFFFFF; position: relative; border-bottom: 3px solid var(--system-accent);">
                        <button type="button" onclick="document.getElementById('request-modal').classList.remove('show')" style="position: absolute; top: 1.25rem; right: 1.25rem; background: transparent; border: none; color: #BDC6CF; cursor: pointer; padding: 0;">
                            <span class="material-symbols-outlined">close</span>
                        </button>

                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; padding-right: 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.875rem;">
                                <div style="width: 42px; height: 42px; border-radius: var(--radius-sm); background: rgba(92,114,144,0.25); border: 1px solid rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; color: var(--system-accent);">
                                    <span class="material-symbols-outlined" style="font-size: 1.75rem;">support_agent</span>
                                </div>
                                <div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #FFFFFF; letter-spacing: -0.01em;">Enterprise IT Service Desk & Support Hub</h3>
                                        <span class="badge-classification internal" style="font-size: 0.625rem;">Tier 1 - 3</span>
                                    </div>
                                    <p style="margin: 0.2rem 0 0; font-size: 0.75rem; color: #BDC6CF;">VOSTOKPRIBOR Internal Infrastructure • Almaty Tech Center Alpha</p>
                                </div>
                            </div>

                            <!-- Live Support Status Pill -->
                            <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.6875rem;">
                                <span class="pulse-dot"></span>
                                <span style="color: #FFFFFF; font-weight: 500;">Engineers Active:</span>
                                <span style="color: #93C5FD; font-family: var(--font-mono);">R. Kim & L. Volkov</span>
                                <span style="color: #687482;">•</span>
                                <span style="color: var(--brand-accent); font-weight: 600;">SLA: &lt;15m</span>
                            </div>
                        </div>

                        <!-- Hub Tabs -->
                        <div style="display: flex; gap: 1rem; margin-top: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0;">
                            <button type="button" class="helpdesk-tab-btn active" id="tab-btn-create" onclick="window.switchHelpdeskTab('create')">
                                <span class="material-symbols-outlined" style="font-size: 1.1rem;">add_circle</span>
                                Log Incident / Request
                            </button>
                            <button type="button" class="helpdesk-tab-btn" id="tab-btn-history" onclick="window.switchHelpdeskTab('history')">
                                <span class="material-symbols-outlined" style="font-size: 1.1rem;">confirmation_number</span>
                                Active Incidents (2)
                            </button>
                            <button type="button" class="helpdesk-tab-btn" id="tab-btn-status" onclick="window.switchHelpdeskTab('status')">
                                <span class="material-symbols-outlined" style="font-size: 1.1rem;">dns</span>
                                Network Telemetry & Docs
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: CREATE TICKET FORM -->
                    <div id="helpdesk-pane-create" style="padding: 1.5rem;">
                        <form id="quick-action-form" onsubmit="event.preventDefault(); window.submitQuickAction('it')">
                            
                            <!-- Category Chips -->
                            <div style="margin-bottom: 1.25rem;">
                                <label class="form-label">Incident Category</label>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.5rem;" id="it-category-selector">
                                    <div class="it-category-chip active" onclick="window.selectItCategory(this, 'workstation')">
                                        <span class="material-symbols-outlined" style="font-size: 1.1rem; color: #1B3A5C;">laptop_mac</span>
                                        <span>Workstation / OS</span>
                                    </div>
                                    <div class="it-category-chip" onclick="window.selectItCategory(this, 'vpn')">
                                        <span class="material-symbols-outlined" style="font-size: 1.1rem; color: #0E7C86;">vpn_lock</span>
                                        <span>VPN & Fiber</span>
                                    </div>
                                    <div class="it-category-chip" onclick="window.selectItCategory(this, 'scada')">
                                        <span class="material-symbols-outlined" style="font-size: 1.1rem; color: #137333;">precision_manufacturing</span>
                                        <span>SCADA / PLC</span>
                                    </div>
                                    <div class="it-category-chip" onclick="window.selectItCategory(this, 'access')">
                                        <span class="material-symbols-outlined" style="font-size: 1.1rem; color: #D9822B;">password</span>
                                        <span>MFA & Access</span>
                                    </div>
                                    <div class="it-category-chip" onclick="window.selectItCategory(this, 'hardware')">
                                        <span class="material-symbols-outlined" style="font-size: 1.1rem; color: #6E4C7C;">memory</span>
                                        <span>Hardware Asset</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Two Column Form Grid -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                                
                                <!-- Left Column -->
                                <div>
                                    <!-- Severity Selector -->
                                    <div class="form-group">
                                        <label class="form-label">Incident Severity</label>
                                        <div style="display: flex; gap: 0.375rem;" id="severity-selector-group">
                                            <div class="severity-card" data-severity="low" onclick="window.selectSeverity(this, 'low')">
                                                Low
                                                <span style="font-size: 0.625rem; font-weight: 400; opacity: 0.8;">Inquiry</span>
                                            </div>
                                            <div class="severity-card active" data-severity="medium" onclick="window.selectSeverity(this, 'medium')">
                                                Medium
                                                <span style="font-size: 0.625rem; font-weight: 400; opacity: 0.8;">Normal</span>
                                            </div>
                                            <div class="severity-card" data-severity="high" onclick="window.selectSeverity(this, 'high')">
                                                High
                                                <span style="font-size: 0.625rem; font-weight: 400; opacity: 0.8;">Degraded</span>
                                            </div>
                                            <div class="severity-card" data-severity="critical" onclick="window.selectSeverity(this, 'critical')">
                                                Critical
                                                <span style="font-size: 0.625rem; font-weight: 400; opacity: 0.8;">Stoppage</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Asset / System Tag with Quick Chips -->
                                    <div class="form-group">
                                        <label class="form-label">
                                            <span>Affected Asset / System</span>
                                            <span style="font-size: 0.625rem; font-family: var(--font-mono); color: var(--neutral-500); text-transform: none;">Click tag to insert</span>
                                        </label>
                                        <input type="text" id="it-asset-input" class="form-input" placeholder="e.g. Workstation WS-204-ALM or LDAP login" value="WS-204-ALM" required />
                                        <div style="display: flex; gap: 0.35rem; margin-top: 0.35rem; flex-wrap: wrap;">
                                            <span class="quick-asset-tag" onclick="document.getElementById('it-asset-input').value='WS-204-ALM'">WS-204-ALM</span>
                                            <span class="quick-asset-tag" onclick="document.getElementById('it-asset-input').value='VPN-ALMATY-01'">VPN-ALMATY-01</span>
                                            <span class="quick-asset-tag" onclick="document.getElementById('it-asset-input').value='SCADA-RTU-8'">SCADA-RTU-8</span>
                                            <span class="quick-asset-tag" onclick="document.getElementById('it-asset-input').value='LDAP-SSO'">LDAP-SSO</span>
                                        </div>
                                    </div>

                                    <!-- Location Info -->
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label">Contact Station / Callout</label>
                                        <input type="text" class="form-input" value="Elena Morozova (CTO) • Tech Center Alpha, Suite 201 • Ext. 104" readonly style="background: var(--neutral-50); color: var(--neutral-700); font-size: 0.75rem;" />
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div>
                                    <!-- Incident Brief -->
                                    <div class="form-group">
                                        <label class="form-label">Incident Brief / Title</label>
                                        <input type="text" class="form-input" placeholder="e.g. WireGuard handshake failure after sleep mode" value="WireGuard VPN tunnel reset on VLAN 4" required />
                                    </div>

                                    <!-- Detailed Description -->
                                    <div class="form-group">
                                        <label class="form-label">Symptom Description & Steps</label>
                                        <textarea class="form-textarea" rows="4" placeholder="Detail error codes, telemetry logs, or application crash dumps..." required>Client handshake times out with TLS peer certificate verification error code 0x800B0109. Local adapter reboot does not resolve routing.</textarea>
                                    </div>

                                    <!-- Diagnostic Attachment Drop Zone -->
                                    <div class="file-drop-zone" onclick="window.showIntranetToast('Diagnostics Log', 'Log collector attached: /var/log/wireguard_dump.pcap (1.2 MB)', 'info')">
                                        <span class="material-symbols-outlined" style="color: var(--system-accent); font-size: 1.5rem;">attach_file</span>
                                        <div style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800);">Attach Error Logs or Screenshot</div>
                                        <div style="font-size: 0.6875rem; color: var(--neutral-500);">Drag file here or click to browse (Max 25MB)</div>
                                    </div>
                                </div>

                            </div>

                            <!-- Footer Actions -->
                            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--neutral-200); padding-top: 1.25rem; margin-top: 1.25rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--neutral-600);">
                                    <span class="material-symbols-outlined" style="color: #137333; font-size: 1rem;">verified_user</span>
                                    <span>Encrypted & logged under ISO-27001 ITIL compliance</span>
                                </div>
                                <div style="display: flex; gap: 0.75rem;">
                                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('request-modal').classList.remove('show')">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="material-symbols-outlined" style="font-size: 1.1rem;">send</span>
                                        Submit Incident Ticket
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>

                    <!-- TAB 2: ACTIVE TICKETS HISTORY -->
                    <div id="helpdesk-pane-history" style="padding: 1.5rem; display: none;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                            <span style="font-weight: 600; font-size: 0.875rem; color: var(--neutral-900);">Active IT Incidents & Hardware Requisitions</span>
                            <span class="badge-classification internal">2 Active Tickets</span>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            
                            <!-- Ticket 1 -->
                            <div class="ticket-row">
                                <div style="display: flex; align-items: flex-start; gap: 0.875rem;">
                                    <span class="material-symbols-outlined" style="color: #1A73E8; font-size: 1.5rem; margin-top: 2px;">vpn_key</span>
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2px;">
                                            <span style="font-family: var(--font-mono); font-weight: 700; font-size: 0.8125rem; color: var(--brand-primary);">INC-2026-8912</span>
                                            <span class="badge-classification internal" style="font-size: 0.625rem;">In Progress</span>
                                            <span class="severity-card active" data-severity="high" style="padding: 0 0.35rem; font-size: 0.625rem; flex: none;">High Priority</span>
                                        </div>
                                        <div style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-900);">Zero-Trust YubiKey 5-Series Hardware Token Provisioning</div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 2px;">Assigned to: Leonid Volkov (ITD) • Updated 12m ago • Hardware token configured and awaiting pickup at Annex 204.</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="window.showIntranetToast('Ticket Status', 'Leonid Volkov is ready for handoff in Annex 204.', 'info')">
                                    View Thread
                                </button>
                            </div>

                            <!-- Ticket 2 -->
                            <div class="ticket-row">
                                <div style="display: flex; align-items: flex-start; gap: 0.875rem;">
                                    <span class="material-symbols-outlined" style="color: #D9822B; font-size: 1.5rem; margin-top: 2px;">desktop_windows</span>
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2px;">
                                            <span style="font-family: var(--font-mono); font-weight: 700; font-size: 0.8125rem; color: var(--brand-primary);">INC-2026-8740</span>
                                            <span class="badge-classification confidential" style="font-size: 0.625rem;">Scheduled</span>
                                            <span class="severity-card active" data-severity="medium" style="padding: 0 0.35rem; font-size: 0.625rem; flex: none;">Medium</span>
                                        </div>
                                        <div style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-900);">Calibration Lab Bay C Dual-Monitor Arm & Display Replacement</div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 2px;">Assigned to: Ruslan Kim (ITD) • Scheduled for Friday 14:00 • Replacement panels staged.</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="window.showIntranetToast('Ticket Status', 'Field technician visit scheduled for Friday 14:00.', 'info')">
                                    View Thread
                                </button>
                            </div>

                        </div>
                    </div>

                    <!-- TAB 3: NETWORK TELEMETRY & KB -->
                    <div id="helpdesk-pane-status" style="padding: 1.5rem; display: none;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                            <div>
                                <h4 style="margin: 0 0 0.75rem; font-size: 0.875rem; color: var(--neutral-900);">Enterprise Infrastructure Health</h4>
                                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-50); border: 1px solid var(--neutral-200); border-radius: var(--radius-sm);">
                                        <span style="font-size: 0.8125rem; font-weight: 500;">Almaty HQ Fiber Ring</span>
                                        <span class="badge-classification internal" style="color: #137333; background: #E6F4EA;">Operational (99.98%)</span>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-50); border: 1px solid var(--neutral-200); border-radius: var(--radius-sm);">
                                        <span style="font-size: 0.8125rem; font-weight: 500;">Zero-Trust LDAP / Active Directory</span>
                                        <span class="badge-classification internal" style="color: #137333; background: #E6F4EA;">Operational</span>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-50); border: 1px solid var(--neutral-200); border-radius: var(--radius-sm);">
                                        <span style="font-size: 0.8125rem; font-weight: 500;">SCADA Modbus TCP Telemetry Stream</span>
                                        <span class="badge-classification internal" style="color: #137333; background: #E6F4EA;">Operational</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 style="margin: 0 0 0.75rem; font-size: 0.875rem; color: var(--neutral-900);">Technical Quick Guides</h4>
                                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div class="ticket-row" style="padding: 0.5rem 0.75rem; cursor: pointer;" onclick="window.downloadDocSimulation('DOC-2026-001')">
                                        <div style="font-size: 0.75rem; font-weight: 600;">DOC-2026-001: Security Policy & WireGuard VPN Setup</div>
                                        <span class="material-symbols-outlined" style="color: var(--system-accent); font-size: 1.1rem;">download</span>
                                    </div>
                                    <div class="ticket-row" style="padding: 0.5rem 0.75rem; cursor: pointer;" onclick="window.downloadDocSimulation('DOC-2026-006')">
                                        <div style="font-size: 0.75rem; font-weight: 600;">DOC-2026-006: Hardware Requisition & Encryption Standard</div>
                                        <span class="material-symbols-outlined" style="color: var(--system-accent); font-size: 1.1rem;">download</span>
                                    </div>
                                    <div class="ticket-row" style="padding: 0.5rem 0.75rem; cursor: pointer;" onclick="window.downloadDocSimulation('DOC-2026-010')">
                                        <div style="font-size: 0.75rem; font-weight: 600;">DOC-2026-010: SCADA API Integration Handbook</div>
                                        <span class="material-symbols-outlined" style="color: var(--system-accent); font-size: 1.1rem;">download</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            `;
        }

        modal.classList.add('show');
        modal.onclick = (e) => {
            if (e.target === modal) modal.classList.remove('show');
        };
    };

    window.switchHelpdeskTab = function (tabName) {
        const createPane = document.getElementById('helpdesk-pane-create');
        const historyPane = document.getElementById('helpdesk-pane-history');
        const statusPane = document.getElementById('helpdesk-pane-status');

        const btnCreate = document.getElementById('tab-btn-create');
        const btnHistory = document.getElementById('tab-btn-history');
        const btnStatus = document.getElementById('tab-btn-status');

        if (btnCreate) btnCreate.classList.toggle('active', tabName === 'create');
        if (btnHistory) btnHistory.classList.toggle('active', tabName === 'history');
        if (btnStatus) btnStatus.classList.toggle('active', tabName === 'status');

        if (createPane) createPane.style.display = tabName === 'create' ? 'block' : 'none';
        if (historyPane) historyPane.style.display = tabName === 'history' ? 'block' : 'none';
        if (statusPane) statusPane.style.display = tabName === 'status' ? 'block' : 'none';
    };

    window.selectItCategory = function (el, cat) {
        const container = document.getElementById('it-category-selector');
        if (container) {
            container.querySelectorAll('.it-category-chip').forEach(c => c.classList.remove('active'));
        }
        el.classList.add('active');
    };

    window.selectSeverity = function (el, sev) {
        const group = document.getElementById('severity-selector-group');
        if (group) {
            group.querySelectorAll('.severity-card').forEach(c => c.classList.remove('active'));
        }
        el.classList.add('active');
    };

    window.submitQuickAction = function (type) {
        const modal = document.getElementById('request-modal');
        if (modal) modal.classList.remove('show');

        if (type === 'leave') {
            showToast('Leave Request Submitted', 'Absence form transmitted to HRA and supervisor. Tracking ID: LR-2026-4481', 'success');
        } else {
            showToast('Support Incident Logged', 'IT Ticket INC-2026-9044 assigned to Leonid Volkov (ITD). Resolution SLA: 15 mins.', 'success');
        }
    };

    /* Utility escape */
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /* ==========================================================================
       11. Master Initialization
       ========================================================================== */
    document.addEventListener('DOMContentLoaded', () => {
        initSidebar();
        initTopBarPopovers();
        initCommandPalette();
        initHomeFeed();
        initEmployeeDirectory();
        initPoliciesLibrary();
    });

})();
