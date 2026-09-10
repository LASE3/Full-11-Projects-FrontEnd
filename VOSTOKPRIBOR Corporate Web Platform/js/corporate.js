/**
 * VOSTOKPRIBOR Corporate Web Platform (System 01)
 * Controller & Interactive Logic
 * Est. 1968 • Almaty, Kazakhstan • www.vostokpribor.local
 */

(function () {
    'use strict';

    /* ==========================================================================
       1. Baseline Data & Product Catalog (PROD-1001 – PROD-1010)
       ========================================================================== */
    const PRODUCT_CATALOG = {
        'PROD-1001': {
            id: 'PROD-1001',
            category: 'optics',
            name: 'Industrial Optical Sensor Package',
            model: 'VP-OPT-8800 Series',
            billing: 'Per Unit / OEM Skid',
            classification: 'public',
            leadTime: '7 Business Days',
            description: 'Multi-spectral pyrometric and optical inspection sensors designed for severe metallurgical furnaces, hot strip mills, and chemical processing skids.',
            specs: {
                'Spectral Range': '0.85 µm to 1.70 µm InGaAs',
                'Response Time': '< 1.2 ms (95% step)',
                'Operating Temp': '-40°C to +85°C (Chilled housing to +250°C)',
                'Interface Protocols': 'Modbus TCP, PROFINET, 4-20mA HART',
                'Ingress Protection': 'IP67 / NEMA 4X Hermetic Seal',
                'Certification': 'GOST R 8.568, ISO/IEC 17025 Metrology Verified'
            },
            primaryApplication: 'Severstal Blast Furnace #4, Steppe Mining Smelter'
        },
        'PROD-1002': {
            id: 'PROD-1002',
            category: 'geodesy',
            name: 'Precision Geodetic Measurement Kit',
            model: 'VP-GEO-TK4',
            billing: 'Per Unit',
            classification: 'public',
            leadTime: '10 Business Days',
            description: 'Sub-millimeter total station and RTK GNSS bundle for large-scale geodetic infrastructure surveys, dam deformation monitoring, and railway track alignment.',
            specs: {
                'Angle Measurement Accuracy': '0.5" (0.15 mgon)',
                'Distance Range (Prism)': 'Up to 5,000 m (1.0 mm + 1 ppm)',
                'Internal Storage': '32 GB Ruggedized Solid-State',
                'Battery Autonomy': '16 Hours Continuous Telemetry',
                'Operating Temp': '-35°C to +60°C Extreme Climate',
                'Accreditation': 'Rosstandart Federal Register #82014-21'
            },
            primaryApplication: 'Aral Geomatics Group, CentralRail Track Audits'
        },
        'PROD-1003': {
            id: 'PROD-1003',
            category: 'metrology',
            name: 'Automated Calibration Station',
            model: 'VP-CAL-X9',
            billing: 'Per Project / Turnkey',
            classification: 'public',
            leadTime: '4 Weeks Build & Certify',
            description: 'Robotic multi-parameter sensor calibration bench with automated certificate generation and Crypto-Pro EDS digital signing compliance.',
            specs: {
                'Thermal Chamber Range': '-50°C to +300°C (±0.02°C stability)',
                'Pressure Standard': '0 to 700 bar (0.01% FS accuracy)',
                'Throughput': 'Up to 24 sensors simultaneously',
                'Compliance Engine': 'Automated Rosstandart & ISO 17025 report generator',
                'Safety Interlocks': 'SIL 2 Dual-Channel Emergency Shutdown'
            },
            primaryApplication: 'Vostokpribor Metrology Labs, Central Plant Quality Labs'
        },
        'PROD-1004': {
            id: 'PROD-1004',
            category: 'automation',
            name: 'Industrial PLC Integration Skid',
            model: 'VP-PLC-4000',
            billing: 'Per Project',
            classification: 'internal',
            leadTime: '3 Weeks Custom Staging',
            description: 'Custom programmable automation rack designed for deterministic telemetry loop control, emergency safety sequencing, and SCADA uplink.',
            specs: {
                'Processor Architecture': 'Dual-Core Industrial ARM Cortex-M7',
                'Cycle Time': '< 500 µs deterministic scan',
                'Fieldbus Uplink': 'OPC UA, Modbus TCP, EtherCAT, Profinet',
                'Redundancy': 'Hot-Standby Dual Processor Failover < 10ms',
                'Cybersecurity': 'IEC 62443-4-2 Level 3 Secure Boot'
            },
            primaryApplication: 'Tashkent Precision Controls, BaltNord Process Systems'
        },
        'PROD-1005': {
            id: 'PROD-1005',
            category: 'automation',
            name: 'Remote Monitoring Gateway (IoT/SCADA)',
            model: 'VP-GW-500',
            billing: 'Per Unit / Volume Tier',
            classification: 'public',
            leadTime: '3 Business Days',
            description: 'Rugged DIN-rail edge computing unit bridging localized RS-485/Modbus field nodes to the centralized Vostokpribor Customer Telemetry Cloud.',
            specs: {
                'Cellular Band': '5G / LTE-M / NB-IoT with Dual SIM Failover',
                'Edge Buffer': '64 GB eMMC (stores 90 days offline data)',
                'Encryption': 'TLS 1.3 / AES-256 GCM Hardware Crypto Engine',
                'Power Input': '9-36 VDC with 10-minute Supercapacitor Backup',
                'Certifications': 'CE, EAC, GOST R, FCC Industrial Class A'
            },
            primaryApplication: 'Eurasia Water Automation, Steppe Remote Pumping'
        },
        'PROD-1006': {
            id: 'PROD-1006',
            category: 'optics',
            name: 'Optical Inspection System',
            model: 'VP-VIS-200',
            billing: 'Per Project',
            classification: 'public',
            leadTime: '2 Weeks Engineering Review',
            description: 'High-speed line-scan camera array with automated machine vision AI for micro-defect detection in cold-rolled steel and aluminum extrusion lines.',
            specs: {
                'Line Rate': 'Up to 140 kHz Continuous Scan',
                'Resolution': '16,384 Pixels per Line Camera',
                'Defect Detection Threshold': 'Down to 15 µm at 12 m/s line speed',
                'Classification Engine': 'On-board FPGA Neural Inference Model',
                'Lighting Subsystem': 'Custom High-Flux Uniform LED Strobes (>1M Lux)'
            },
            primaryApplication: 'Severstal Metallurgy Plant #4, Daugava Optical'
        },
        'PROD-1007': {
            id: 'PROD-1007',
            category: 'metrology',
            name: 'Industrial Lifecycle Support & SLA',
            model: 'SLA Tier 1-3',
            billing: 'Annual Subscription',
            classification: 'public',
            leadTime: 'Immediate Activation',
            description: 'Comprehensive 24/7 engineering warranty, guaranteed 2-hour technician dispatch, emergency parts hot-swapping, and quarterly metrology audit.',
            specs: {
                'SLA Response Time': 'Under 15 Minutes to First Engineering Triage',
                'On-Site Mezzanine Support': 'Guaranteed 4-hour on-site industrial tech',
                'Firmware Maintenance': 'Over-the-air hotfixes and patch certifications',
                'Dedicated Account Rep': 'Assigned Senior Systems Engineer (e.g. Viktor Morozov)'
            },
            primaryApplication: 'Tier-1 Industrial Accounts (Severstal, Steppe Mining)'
        },
        'PROD-1008': {
            id: 'PROD-1008',
            category: 'automation',
            name: 'Automation Software & Protocol Integration',
            model: 'VP-SW-SUITE',
            billing: 'Per Project / Site License',
            classification: 'public',
            leadTime: 'Custom Staging',
            description: 'Modular enterprise middleware connecting legacy SCADA installations (Siemens S7, Rockwell, Schneider) directly to ERP, CRM, and Billing.',
            specs: {
                'API Standard': 'REST / GraphQL / MQTT Industrial Broker',
                'Throughput': '120,000 telemetry events / second per cluster',
                'Supported Formats': 'JSON, Protocol Buffers, OPC UA DA/HDA',
                'Platform Compatibility': 'RHEL 9, Rocky Linux, Windows Server 2022'
            },
            primaryApplication: 'BaltNord Process Systems, Tashkent Precision'
        },
        'PROD-1009': {
            id: 'PROD-1009',
            category: 'metrology',
            name: 'Enterprise Logistics & Supply Chain',
            model: 'VP-LOG-GLOBAL',
            billing: 'Monthly Retainer',
            classification: 'public',
            leadTime: 'Immediate Staging',
            description: 'Specialized bonded warehousing, temperature-monitored sensitive instrument transit, and customs brokerage across Central Asia and Europe.',
            specs: {
                'Bonded Hubs': 'Almaty (Main), Astana, Tashkent, Rotterdam Port',
                'Tracking Telemetry': 'Active shock, tilt, temperature & GPS tracking',
                'Customs Clearance': 'Eurasian Economic Union (EAEU) & EU T1 Passports'
            },
            primaryApplication: 'All 10 Enterprise Client Consortia'
        },
        'PROD-1010': {
            id: 'PROD-1010',
            category: 'metrology',
            name: 'Preventive Instrument Maintenance',
            model: 'VP-MAINT-ANNUAL',
            billing: 'Annual Contract',
            classification: 'public',
            leadTime: 'Scheduled Windows',
            description: 'Annual scheduled optical alignment, sensor zero-drift calibration, laser diode replacement, and state metrological recertification.',
            specs: {
                'Inspection Frequency': 'Semi-Annual / Annual Calibration Cycles',
                'Traceability': 'Directly traceable to VNIIFTRI / BIPM Standards',
                'Documentation': 'Electronic Passport issuance with Crypto-Pro signature'
            },
            primaryApplication: 'Daugava Optical Research, Caspian Industrial Robotics'
        }
    };

    /* ==========================================================================
       2. Projects & Case Studies Database (PRJ-2026-001 – PRJ-2026-015)
       ========================================================================== */
    const ENTERPRISE_PROJECTS = [
        {
            id: 'PRJ-2026-001',
            client: 'Aral Geomatics Group',
            sector: 'Geomatics & Earth Observation',
            title: 'Sub-Millimeter Geodetic Sensor Array',
            budget: '€185,000',
            status: 'In Execution',
            statusClass: 'in-progress',
            manager: 'Farida Iskakova (EMP-1019)',
            classification: 'confidential'
        },
        {
            id: 'PRJ-2026-002',
            client: 'BaltNord Process Systems',
            sector: 'Industrial Automation',
            title: 'SCADA Telemetry Loop Integration & Gateway Skid',
            budget: '€240,000',
            status: 'Integration Phase',
            statusClass: 'in-progress',
            manager: 'Farida Iskakova (EMP-1019)',
            classification: 'restricted'
        },
        {
            id: 'PRJ-2026-003',
            client: 'Steppe Mining Technologies',
            sector: 'Mining & Metallurgy',
            title: 'Heavy Slag Smelter Pyrometry & Optical Bundle',
            budget: '€410,000',
            status: 'Procurement',
            statusClass: 'pending',
            manager: 'Erik Hansen (EMP-1016)',
            classification: 'confidential'
        },
        {
            id: 'PRJ-2026-004',
            client: 'RheinWerk Instrumentation',
            sector: 'Precision Metrology',
            title: 'Automated Sensor Recalibration Chamber Setup',
            budget: '€165,000',
            status: 'In Execution',
            statusClass: 'in-progress',
            manager: 'Farida Iskakova (EMP-1019)',
            classification: 'internal'
        },
        {
            id: 'PRJ-2026-007',
            client: 'Caspian Industrial Robotics',
            sector: 'Advanced Robotics',
            title: 'Autonomous Mobile Inspection Vision Pods',
            budget: '€315,000',
            status: 'Integration Phase',
            statusClass: 'in-progress',
            manager: 'Farida Iskakova (EMP-1019)',
            classification: 'confidential'
        },
        {
            id: 'PRJ-2026-010',
            client: 'CentralRail Diagnostics',
            sector: 'Rail Infrastructure',
            title: 'High-Speed Track Vibration & Geometry Telemetry',
            budget: '€275,000',
            status: 'Procurement',
            statusClass: 'pending',
            manager: 'Farida Iskakova (EMP-1019)',
            classification: 'confidential'
        }
    ];

    /* ==========================================================================
       3. Navigation & Ecosystem Switcher
       ========================================================================== */
    function initNavigation() {
        const ecosystemBtn = document.getElementById('ecosystemSwitcherBtn');
        const ecosystemDropdown = document.getElementById('ecosystemDropdown');
        const mobileToggleBtn = document.getElementById('mobileMenuToggleBtn');
        const mobileMenuDrawer = document.getElementById('mobileMenuDrawer');

        // Toggle Ecosystem Dropdown
        if (ecosystemBtn && ecosystemDropdown) {
            ecosystemBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                ecosystemDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function (e) {
                if (!ecosystemDropdown.contains(e.target) && e.target !== ecosystemBtn) {
                    ecosystemDropdown.classList.remove('show');
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    ecosystemDropdown.classList.remove('show');
                }
            });
        }

        // Mobile Menu Drawer
        if (mobileToggleBtn && mobileMenuDrawer) {
            mobileToggleBtn.addEventListener('click', function () {
                mobileMenuDrawer.classList.toggle('hidden');
            });
        }
    }

    /* ==========================================================================
       4. Product Catalog Filtering & Category Selector
       ========================================================================== */
    function initProductFilters() {
        const filterBtns = document.querySelectorAll('.product-filter-btn');
        const productGrid = document.getElementById('equipmentGridContainer');

        if (!productGrid) return;

        // Render initial products
        renderProductCards('all');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => {
                    b.classList.remove('active', 'bg-brand-primary', 'text-white');
                    b.classList.add('bg-white', 'text-neutral-600');
                });
                this.classList.add('active', 'bg-brand-primary', 'text-white');
                this.classList.remove('bg-white', 'text-neutral-600');

                const selectedCategory = this.getAttribute('data-category');
                renderProductCards(selectedCategory);
            });
        });
    }

    function renderProductCards(category) {
        const container = document.getElementById('equipmentGridContainer');
        if (!container) return;

        const filtered = Object.values(PRODUCT_CATALOG).filter(item => {
            if (category === 'all') return true;
            return item.category === category;
        });

        container.innerHTML = '';

        filtered.forEach(item => {
            const card = document.createElement('div');
            card.className = 'equipment-card';

            const classificationLabel = item.classification === 'internal' ? 'Internal' : 'Public';
            const classificationClass = item.classification === 'internal' ? 'internal' : 'public';

            card.innerHTML = `
                <div class="equipment-image-box">
                    <span class="equipment-code-tag">${item.id}</span>
                    <span class="badge-classification ${classificationClass} absolute top-3 right-3 shadow-sm">${classificationLabel}</span>
                    <div class="text-center p-4">
                        <span class="material-symbols-outlined text-4xl text-teal-300 opacity-85">precision_manufacturing</span>
                        <div class="font-mono text-xs text-slate-300 mt-2 tracking-wider">${item.model}</div>
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs text-neutral-500 font-mono mb-1.5">
                            <span>${item.billing}</span>
                            <span class="text-teal-700 font-medium">Lead Time: ${item.leadTime}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-neutral-900 leading-snug mb-2">${item.name}</h3>
                        <p class="text-xs text-neutral-600 line-clamp-3 mb-4 leading-relaxed">${item.description}</p>
                    </div>
                    <div>
                        <div class="pt-3 border-t border-neutral-200 flex items-center justify-between">
                            <button class="view-specs-btn text-xs font-semibold text-brand-primary hover:text-brand-secondary flex items-center gap-1 transition-colors cursor-pointer" data-id="${item.id}">
                                <span>Technical Dossier</span>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </button>
                            <button class="open-rfq-for-item-btn px-2.5 py-1 text-xs font-medium bg-neutral-100 hover:bg-neutral-200 text-neutral-800 rounded transition-colors cursor-pointer" data-id="${item.id}" data-name="${item.name}">
                                Request Quote
                            </button>
                        </div>
                    </div>
                `;

            container.appendChild(card);
        });

        // Wire click handlers for technical dossier and quote buttons
        container.querySelectorAll('.view-specs-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const prodId = this.getAttribute('data-id');
                openSpecModal(prodId);
            });
        });

        container.querySelectorAll('.open-rfq-for-item-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const prodName = this.getAttribute('data-name');
                openRfqModalWithPreload(prodName);
            });
        });
    }

    /* ==========================================================================
       5. Technical Dossier Spec Modal
       ========================================================================== */
    function openSpecModal(productId) {
        const product = PRODUCT_CATALOG[productId];
        if (!product) return;

        const modalBackdrop = document.getElementById('specModalBackdrop');
        const modalTitle = document.getElementById('specModalTitle');
        const modalSubtitle = document.getElementById('specModalSubtitle');
        const modalClassBadge = document.getElementById('specModalClassBadge');
        const modalSpecsTable = document.getElementById('specModalSpecsTable');
        const modalDesc = document.getElementById('specModalDescription');
        const modalDeployments = document.getElementById('specModalDeployments');

        if (!modalBackdrop) return;

        modalTitle.textContent = `${product.id} • ${product.name}`;
        modalSubtitle.textContent = `Model: ${product.model} | Commercial Model: ${product.billing}`;
        modalDesc.textContent = product.description;
        modalDeployments.textContent = product.primaryApplication;

        modalClassBadge.className = `badge-classification ${product.classification}`;
        modalClassBadge.textContent = product.classification === 'internal' ? 'Internal' : 'Public';

        modalSpecsTable.innerHTML = '';
        Object.entries(product.specs).forEach(([key, val]) => {
            const row = document.createElement('tr');
            row.className = 'border-b border-neutral-200 text-xs';
            row.innerHTML = `
                <td class="py-2.5 pr-4 font-mono font-medium text-neutral-600 w-1/3">${key}</td>
                <td class="py-2.5 font-sans font-semibold text-neutral-900">${val}</td>
            `;
            modalSpecsTable.appendChild(row);
        });

        modalBackdrop.classList.add('show');
    }

    function initSpecModal() {
        const modalBackdrop = document.getElementById('specModalBackdrop');
        const closeBtn = document.getElementById('closeSpecModalBtn');

        if (modalBackdrop && closeBtn) {
            closeBtn.addEventListener('click', () => modalBackdrop.classList.remove('show'));
            modalBackdrop.addEventListener('click', (e) => {
                if (e.target === modalBackdrop) modalBackdrop.classList.remove('show');
            });
        }
    }

    /* ==========================================================================
       6. Commercial RFQ & Lead Intake Form (SOP-01)
       ========================================================================== */
    function openRfqModalWithPreload(productName) {
        const rfqSection = document.getElementById('contact-rfq');
        if (rfqSection) {
            rfqSection.scrollIntoView({ behavior: 'smooth' });
            const interestSelect = document.getElementById('rfqInterestSelect');
            if (interestSelect) {
                for (let i = 0; i < interestSelect.options.length; i++) {
                    if (interestSelect.options[i].text.includes(productName)) {
                        interestSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        }
    }

    function initRfqForm() {
        const form = document.getElementById('corporateRfqForm');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const companyInput = document.getElementById('rfqCompanyName');
            const contactInput = document.getElementById('rfqContactName');
            const emailInput = document.getElementById('rfqEmail');
            const sectorSelect = document.getElementById('rfqSector');
            const interestSelect = document.getElementById('rfqInterestSelect');
            const notesInput = document.getElementById('rfqNotes');

            if (!companyInput.value.trim() || !contactInput.value.trim() || !emailInput.value.trim()) {
                showCorporateToast('Required Information Missing', 'Please fill in your company, contact name, and business email.', 'error');
                return;
            }

            // Generate Lead Reference ID conforming to Baseline SOP-01
            const randomSuffix = Math.floor(1000 + Math.random() * 9000);
            const leadId = `LEAD-2026-${randomSuffix}`;
            const timestamp = new Date().toLocaleTimeString('en-US', { hour12: false });

            // Render Confirmation Overlay Modal
            const confirmationBackdrop = document.getElementById('rfqConfirmationBackdrop');
            const leadIdDisplay = document.getElementById('confirmedLeadId');
            const leadRoutingDisplay = document.getElementById('confirmedRouting');

            if (confirmationBackdrop && leadIdDisplay) {
                leadIdDisplay.textContent = leadId;
                if (leadRoutingDisplay) {
                    leadRoutingDisplay.textContent = `Routed via SOP-01 to CRM (crm.vostokpribor.local). Assigned Account Lead: Pavel Orlov (EMP-1006) / Sara Lindholm (EMP-1007).`;
                }
                confirmationBackdrop.classList.add('show');
            }

            showCorporateToast(
                `Commercial Lead Generated [${leadId}]`,
                `Inquiry from ${companyInput.value} dispatched to CRM queue at ${timestamp}.`,
                'success'
            );

            // Reset form fields
            form.reset();
        });

        // Confirmation modal close button
        const closeConfirmBtn = document.getElementById('closeConfirmationBtn');
        const confirmBackdrop = document.getElementById('rfqConfirmationBackdrop');
        if (closeConfirmBtn && confirmBackdrop) {
            closeConfirmBtn.addEventListener('click', () => confirmBackdrop.classList.remove('show'));
            confirmBackdrop.addEventListener('click', (e) => {
                if (e.target === confirmBackdrop) confirmBackdrop.classList.remove('show');
            });
        }
    }

    /* ==========================================================================
       7. Case Studies / Projects Table Rendering
       ========================================================================== */
    function initProjectsTable() {
        const tbody = document.getElementById('projectsTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';
        ENTERPRISE_PROJECTS.forEach(proj => {
            const tr = document.createElement('tr');
            tr.setAttribute('data-classification', proj.classification);
            tr.className = 'text-xs hover:bg-neutral-100 transition-colors';

            tr.innerHTML = `
                <td class="py-3 px-4 classified-strip font-mono font-bold text-brand-primary">${proj.id}</td>
                <td class="py-3 px-4">
                    <div class="font-semibold text-neutral-900">${proj.client}</div>
                    <div class="text-[11px] text-neutral-500">${proj.sector}</div>
                </td>
                <td class="py-3 px-4 font-medium text-neutral-800">${proj.title}</td>
                <td class="py-3 px-4 font-mono font-semibold text-neutral-900">${proj.budget}</td>
                <td class="py-3 px-4">
                    <span class="status-badge ${proj.statusClass}">${proj.status}</span>
                </td>
                <td class="py-3 px-4 text-neutral-600">${proj.manager}</td>
                <td class="py-3 px-4 text-right">
                    <span class="badge-classification ${proj.classification}">${proj.classification}</span>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    /* ==========================================================================
       8. Toast Alert Notification Engine
       ========================================================================== */
    function showCorporateToast(title, message, type = 'info') {
        let container = document.getElementById('corporate-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'corporate-toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'corporate-toast';

        let iconName = 'info';
        let borderColor = 'var(--brand-secondary)';

        if (type === 'success') {
            iconName = 'check_circle';
            borderColor = '#137333';
        } else if (type === 'error') {
            iconName = 'error';
            borderColor = '#C5221F';
        } else if (type === 'warning') {
            iconName = 'warning';
            borderColor = 'var(--brand-accent)';
        }

        toast.style.borderLeftColor = borderColor;

        toast.innerHTML = `
            <span class="material-symbols-outlined text-lg shrink-0 mt-0.5" style="color:${borderColor}">${iconName}</span>
            <div class="flex-1">
                <div class="font-semibold text-xs text-white leading-tight">${title}</div>
                <div class="text-[11px] text-neutral-300 mt-0.5 leading-snug">${message}</div>
            </div>
            <button class="toast-close-btn text-neutral-400 hover:text-white transition-colors cursor-pointer text-xs ml-2">✕</button>
        `;

        toast.querySelector('.toast-close-btn').addEventListener('click', () => {
            toast.remove();
        });

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(20px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 5500);
    }

    // Export global helper
    window.showCorporateToast = showCorporateToast;
    window.openSpecModal = openSpecModal;

    /* ==========================================================================
       9. Initialization on DOM Load
       ========================================================================== */
    document.addEventListener('DOMContentLoaded', function () {
        initNavigation();
        initProductFilters();
        initSpecModal();
        initRfqForm();
        initProjectsTable();
    });

})();
