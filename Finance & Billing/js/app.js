/**
 * VOSTOKPRIBOR Finance & Billing Platform - Client State & Controllers (System 08)
 * Subdomain: finance.vostokpribor.local
 */

(function () {
  'use strict';

  const finApp = {
    // Invoices Ledger
    invoices: [
      {
        id: 'INV-2024-8841',
        customer: 'Severstal Metallurgy PJSC',
        project: 'Blast Furnace #5 Telemetry Retrofit',
        projectId: 'PRJ-VP-7721',
        total: 1850000.00,
        status: 'Paid', // Paid | Pending | Overdue
        issueDate: 'Oct 01, 2024',
        dueDate: 'Oct 31, 2024',
        paidDate: 'Oct 28, 2024',
        items: [
          { desc: 'High-Temp IR Optical Pyrometer Probe Array', part: 'OPT-PYRO-900X', qty: 12, unit: 45000.00, total: 540000.00 },
          { desc: 'SCADA Industrial Telemetry Gateway Controller', part: 'SCADA-GT-400', qty: 4, unit: 85000.00, total: 340000.00 },
          { desc: 'On-Site Blast Furnace Sensor Integration & FAT Testing', part: 'SRV-FAT-01', qty: 1, unit: 970000.00, total: 970000.00 }
        ],
        timeline: [
          { milestone: 'Milestone 1: Advance Mobilization (30%)', amount: '$555,000.00', date: 'Oct 05, 2024', status: 'Settled ✓' },
          { milestone: 'Milestone 2: Hardware Delivery & FAT Pass (40%)', amount: '$740,000.00', date: 'Oct 20, 2024', status: 'Settled ✓' },
          { milestone: 'Milestone 3: Hot Testing & Final Commissioning (30%)', amount: '$555,000.00', date: 'Oct 28, 2024', status: 'Settled ✓' }
        ]
      },
      {
        id: 'INV-2024-8842',
        customer: 'NLMK Group Lipetsk',
        project: 'Coke Oven Battery Temperature Profiling',
        projectId: 'PRJ-VP-7722',
        total: 920000.00,
        status: 'Pending',
        issueDate: 'Oct 15, 2024',
        dueDate: 'Nov 15, 2024',
        paidDate: null,
        items: [
          { desc: 'Multi-Spectral Thermal Imaging Radiometer Matrix', part: 'RAD-MS-500', qty: 6, unit: 80000.00, total: 480000.00 },
          { desc: 'Fiber-Optic Sensor Interface & Harsh Environment Enclosures', part: 'ENC-FO-88', qty: 6, unit: 25000.00, total: 150000.00 },
          { desc: 'Engineering Commissioning & Calibration Certificate', part: 'SRV-CAL-OPT', qty: 1, unit: 290000.00, total: 290000.00 }
        ],
        timeline: [
          { milestone: 'Milestone 1: Project Initiation Advance (30%)', amount: '$276,000.00', date: 'Oct 18, 2024', status: 'Settled ✓' },
          { milestone: 'Milestone 2: Factory Acceptance Test Acceptance (40%)', amount: '$368,000.00', date: 'Nov 02, 2024', status: 'Awaiting Payment' },
          { milestone: 'Milestone 3: Final Industrial Sign-off (30%)', amount: '$276,000.00', date: 'Nov 15, 2024', status: 'Scheduled' }
        ]
      },
      {
        id: 'INV-2024-8843',
        customer: 'Norilsk Nickel Mining',
        project: 'Talnakh Concentrator Flotation Telemetry Grid',
        projectId: 'PRJ-VP-7723',
        total: 2400000.00,
        status: 'Paid',
        issueDate: 'Sep 10, 2024',
        dueDate: 'Oct 10, 2024',
        paidDate: 'Oct 08, 2024',
        items: [
          { desc: 'Slurry Flotation Optical Turbidity Sensors', part: 'TURB-FLOT-200', qty: 24, unit: 40000.00, total: 960000.00 },
          { desc: 'MODBUS TCP Telemetry Aggregator Nodes', part: 'AGGR-MOD-100', qty: 8, unit: 55000.00, total: 440000.00 },
          { desc: 'Arctic Field Installation & Sub-Zero Commissioning', part: 'SRV-ARC-09', qty: 1, unit: 1000000.00, total: 1000000.00 }
        ],
        timeline: [
          { milestone: 'Milestone 1: Mobilization & Procurement (40%)', amount: '$960,000.00', date: 'Sep 15, 2024', status: 'Settled ✓' },
          { milestone: 'Milestone 2: Arctic Delivery & Installation (40%)', amount: '$960,000.00', date: 'Oct 01, 2024', status: 'Settled ✓' },
          { milestone: 'Milestone 3: System Handover & Acceptance (20%)', amount: '$480,000.00', date: 'Oct 08, 2024', status: 'Settled ✓' }
        ]
      },
      {
        id: 'INV-2024-8844',
        customer: 'EVRAZ Consolidated',
        project: 'Rail Mill Laser Profiler & Flaw Detection',
        projectId: 'PRJ-VP-7724',
        total: 385000.00,
        status: 'Overdue',
        issueDate: 'Aug 20, 2024',
        dueDate: 'Sep 20, 2024',
        paidDate: null,
        items: [
          { desc: 'Triangulation Laser Dimension Gauges', part: 'LAS-TR-400X', qty: 4, unit: 65000.00, total: 260000.00 },
          { desc: 'High-Speed Signal Processor Rack & Firmware', part: 'SIG-RACK-02', qty: 1, unit: 125000.00, total: 125000.00 }
        ],
        timeline: [
          { milestone: 'Milestone 1: Advance Contract Deposit (50%)', amount: '$192,500.00', date: 'Aug 25, 2024', status: 'Settled ✓' },
          { milestone: 'Milestone 2: Final Acceptance Delivery (50%)', amount: '$192,500.00', date: 'Sep 20, 2024', status: 'OVERDUE (52 Days)' }
        ]
      },
      {
        id: 'INV-2024-8845',
        customer: 'PhosAgro Chemical',
        project: 'High-Pressure Flowmeter Replacement Batch',
        projectId: 'PRJ-VP-7725',
        total: 418200.00,
        status: 'Pending',
        issueDate: 'Oct 25, 2024',
        dueDate: 'Nov 25, 2024',
        paidDate: null,
        items: [
          { desc: 'Acid-Resistant Magnetic Flowmeter HPF-900X', part: 'FLOW-HPF-900X', qty: 8, unit: 38000.00, total: 304000.00 },
          { desc: 'Chemical Seal Diaphragms & Calibration Rig', part: 'SEAL-CHEM-04', qty: 8, unit: 14275.00, total: 114200.00 }
        ],
        timeline: [
          { milestone: 'Milestone 1: Full Supply Order Billing (100%)', amount: '$418,200.00', date: 'Nov 25, 2024', status: 'Net-30 Invoice Issued' }
        ]
      },
      {
        id: 'INV-2024-8846',
        customer: 'Gazprom Neft Omsk',
        project: 'Refinery Catalytic Cracking Gas Analysis Skid',
        projectId: 'PRJ-VP-7726',
        total: 1240000.00,
        status: 'Paid',
        issueDate: 'Sep 01, 2024',
        dueDate: 'Oct 01, 2024',
        paidDate: 'Sep 29, 2024',
        items: [
          { desc: 'NDIR Hydrocarbon Gas Analyzer Skid Unit', part: 'GAS-NDIR-700', qty: 2, unit: 450000.00, total: 900000.00 },
          { desc: 'Explosion-Proof ATEX Enclosures & Sample Line Heating', part: 'ATEX-ENC-01', qty: 2, unit: 170000.00, total: 340000.00 }
        ],
        timeline: [
          { milestone: 'Milestone 1: Factory Acceptance (60%)', amount: '$744,000.00', date: 'Sep 10, 2024', status: 'Settled ✓' },
          { milestone: 'Milestone 2: Site Integration & Sign-off (40%)', amount: '$496,000.00', date: 'Sep 29, 2024', status: 'Settled ✓' }
        ]
      }
    ],

    // Active Project Billing Registry
    projects: [
      {
        id: 'PRJ-VP-7721',
        name: 'Blast Furnace #5 Automation & Gas Analysis',
        customer: 'Severstal Metallurgy PJSC',
        totalBudget: 2850000.00,
        billedToDate: 1995000.00,
        nextMilestone: 'Nov 30, 2024 · Milestone 4: Hot Commissioning & FAT Pass',
        milestoneAmount: 855000.00
      },
      {
        id: 'PRJ-VP-7722',
        name: 'Coke Oven Battery Temperature Profiling',
        customer: 'NLMK Group Lipetsk',
        totalBudget: 1450000.00,
        billedToDate: 920000.00,
        nextMilestone: 'Dec 15, 2024 · Milestone 3: Final Acceptance Testing',
        milestoneAmount: 530000.00
      },
      {
        id: 'PRJ-VP-7723',
        name: 'Talnakh Concentrator Flotation Telemetry Grid',
        customer: 'Norilsk Nickel Mining',
        totalBudget: 3600000.00,
        billedToDate: 2400000.00,
        nextMilestone: 'Jan 20, 2025 · Milestone 4: Arctic Winter Operational Certification',
        milestoneAmount: 1200000.00
      },
      {
        id: 'PRJ-VP-7724',
        name: 'Rail Mill Laser Profiler & Flaw Detection Array',
        customer: 'EVRAZ Consolidated',
        totalBudget: 1650000.00,
        billedToDate: 770000.00,
        nextMilestone: 'Dec 05, 2024 · Milestone 3: High-Speed Profiling Handover',
        milestoneAmount: 880000.00
      },
      {
        id: 'PRJ-VP-7725',
        name: 'High-Pressure Flowmeter HPF-900X Replacement Batch',
        customer: 'PhosAgro Chemical',
        totalBudget: 418200.00,
        billedToDate: 418200.00,
        nextMilestone: 'Completed · 100% Billed & Delivered',
        milestoneAmount: 0.00
      }
    ],

    // Unmatched Payments Queue
    unmatchedPayments: [
      { id: 'TX-SPFS-9101', customer: 'Severstal PJSC', amount: 420000.00, date: 'Nov 09, 2024', ref: 'Ref: PO-SEV-88219' },
      { id: 'TX-SWIFT-9102', customer: 'NLMK Lipetsk', amount: 185000.00, date: 'Nov 08, 2024', ref: 'Ref: NLMK-FAT-INV-8842' },
      { id: 'TX-SPFS-9103', customer: 'Norilsk Nickel Mining', amount: 337300.00, date: 'Nov 06, 2024', ref: 'Ref: NN-ARC-TEL-99' }
    ],

    formatUSD: function (val) {
      return '$' + Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },

    showToast: function (title, message, type = 'green') {
      const container = document.getElementById('toast-container');
      if (!container) return;

      const toast = document.createElement('div');
      let icon = '💳';
      if (type === 'amber') icon = '⚡';
      if (type === 'red' || type === 'alert') icon = '🔒';
      if (type === 'success' || type === 'green') icon = '✓';

      toast.className = 'fin-toast';
      if (type === 'amber') toast.style.borderLeftColor = 'var(--fin-amber)';
      if (type === 'red' || type === 'alert') toast.style.borderLeftColor = 'var(--fin-confidential)';
      if (type === 'success' || type === 'green') toast.style.borderLeftColor = 'var(--fin-green)';

      toast.innerHTML = `
        <div style="font-size: 16px;">${icon}</div>
        <div style="flex: 1;">
          <div style="font-weight: 700; font-size: 12.5px; color: #FFFFFF;">${title}</div>
          <div style="font-size: 11px; color: rgba(255,255,255,0.8); margin-top: 2px;">${message}</div>
        </div>
        <button style="color: rgba(255,255,255,0.5); font-size: 14px;" onclick="this.parentElement.remove()">✕</button>
      `;

      container.appendChild(toast);

      setTimeout(() => {
        toast.style.transition = 'all 0.3s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
      }, 4200);
    },

    openModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('active');
        const input = modal.querySelector('input, select');
        if (input) input.focus();
      }
    },

    closeModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) modal.classList.remove('active');
    },

    // Inspect Invoice Details (Screen 2 Detail Drawer)
    inspectInvoice: function (invId) {
      const inv = this.invoices.find(i => i.id === invId);
      if (!inv) return;

      const idEl = document.getElementById('modal-inv-id');
      const custEl = document.getElementById('modal-inv-cust');
      const projEl = document.getElementById('modal-inv-proj');
      const totalEl = document.getElementById('modal-inv-total');
      const statusEl = document.getElementById('modal-inv-status');
      const issueEl = document.getElementById('modal-inv-issue');
      const dueEl = document.getElementById('modal-inv-due');
      const itemsBody = document.getElementById('modal-inv-items-body');
      const timelineBody = document.getElementById('modal-inv-timeline-body');

      if (idEl) idEl.textContent = inv.id;
      if (custEl) custEl.textContent = inv.customer;
      if (projEl) projEl.textContent = inv.project;
      if (totalEl) totalEl.textContent = this.formatUSD(inv.total);
      if (issueEl) issueEl.textContent = inv.issueDate;
      if (dueEl) dueEl.textContent = inv.dueDate;

      if (statusEl) {
        if (inv.status === 'Paid') {
          statusEl.className = 'status-badge-paid';
          statusEl.innerHTML = '✓ Paid';
        } else if (inv.status === 'Pending') {
          statusEl.className = 'status-badge-pending';
          statusEl.innerHTML = '⚡ Pending';
        } else {
          statusEl.className = 'status-badge-overdue';
          statusEl.innerHTML = '⚠️ Overdue';
        }
      }

      if (itemsBody) {
        itemsBody.innerHTML = '';
        inv.items.forEach(item => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>
              <div style="font-weight: 600; color: var(--fin-navy);">${item.desc}</div>
              <div style="font-family: var(--fin-font-mono); font-size: 10.5px; color: var(--fin-text-muted);">${item.part}</div>
            </td>
            <td style="text-align: center; font-family: var(--fin-font-mono); font-weight: 600;">${item.qty}</td>
            <td style="text-align: right; font-family: var(--fin-font-mono);">${this.formatUSD(item.unit)}</td>
            <td style="text-align: right; font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">${this.formatUSD(item.total)}</td>
          `;
          itemsBody.appendChild(row);
        });
      }

      if (timelineBody) {
        timelineBody.innerHTML = '';
        inv.timeline.forEach(step => {
          const stepDiv = document.createElement('div');
          stepDiv.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--fin-surface-dim); border-radius: var(--fin-radius-sm); border-left: 3px solid var(--fin-green);';
          stepDiv.innerHTML = `
            <div>
              <div style="font-weight: 600; color: var(--fin-navy); font-size: 12px;">${step.milestone}</div>
              <div style="font-size: 11px; color: var(--fin-text-muted);">${step.date}</div>
            </div>
            <div style="text-align: right;">
              <div style="font-family: var(--fin-font-mono); font-weight: 700; color: var(--fin-navy);">${step.amount}</div>
              <div style="font-size: 10.5px; font-weight: 600; color: var(--fin-green);">${step.status}</div>
            </div>
          `;
          timelineBody.appendChild(stepDiv);
        });
      }

      // Attach current ID to action buttons
      const btnMarkPaid = document.getElementById('btn-modal-mark-paid');
      const btnSendReminder = document.getElementById('btn-modal-send-reminder');

      if (btnMarkPaid) {
        btnMarkPaid.onclick = () => {
          inv.status = 'Paid';
          inv.paidDate = 'Nov 11, 2024';
          this.showToast('Payment Ratified', `Invoice ${inv.id} marked as PAID. Ledger hash updated.`, 'green');
          this.closeModal('modal-invoice-detail');
          this.filterInvoices();
        };
      }

      if (btnSendReminder) {
        btnSendReminder.onclick = () => {
          this.showToast('Payment Reminder Dispatched', `Automated SWIFT reminder sent to ${inv.customer} billing office.`, 'amber');
          this.closeModal('modal-invoice-detail');
        };
      }

      this.openModal('modal-invoice-detail');
    },

    // Search and Filter Invoices Table
    filterInvoices: function () {
      const searchInput = document.getElementById('invoice-search');
      const statusFilter = document.getElementById('invoice-status-filter');

      const query = (searchInput ? searchInput.value : '').toLowerCase();
      const selectedStatus = statusFilter ? statusFilter.value : 'all';

      const rows = document.querySelectorAll('.invoice-table-body tr');
      rows.forEach(row => {
        const id = (row.getAttribute('data-id') || '').toLowerCase();
        const customer = (row.getAttribute('data-customer') || '').toLowerCase();
        const project = (row.getAttribute('data-project') || '').toLowerCase();
        const status = row.getAttribute('data-status') || '';

        const matchQuery = !query || id.includes(query) || customer.includes(query) || project.includes(query);
        const matchStatus = selectedStatus === 'all' || status === selectedStatus;

        if (matchQuery && matchStatus) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    },

    // Reconcile Payment Action
    reconcilePayment: function (txId) {
      const item = this.unmatchedPayments.find(p => p.id === txId);
      if (item) {
        this.showToast('Reconciliation Executed', `Matched ${this.formatUSD(item.amount)} from ${item.customer} to ledger.`, 'green');
        const row = document.getElementById(`tx-row-${txId}`);
        if (row) {
          row.style.transition = 'all 0.3s ease';
          row.style.opacity = '0';
          setTimeout(() => row.remove(), 300);
        }
      }
    },

    // Initialize Navigation & Omni Search
    init: function () {
      const currentPath = window.location.pathname.toLowerCase();
      const sidebarLinks = document.querySelectorAll('.sidebar-nav-item');

      sidebarLinks.forEach(link => {
        const href = (link.getAttribute('href') || '').toLowerCase();
        if (href && (currentPath.endsWith(href) || (currentPath.endsWith('/') && href === 'dashboard.php') || (currentPath.endsWith('index.php') && href === 'dashboard.php'))) {
          link.classList.add('active');
        } else if (href && currentPath.includes(href.replace('.php', ''))) {
          link.classList.add('active');
        }
      });

      const omniSearch = document.getElementById('global-omni-search');
      if (omniSearch) {
        omniSearch.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            const val = omniSearch.value.trim();
            if (val) {
              finApp.showToast('Finance Ledger Query', `Locating transactions and invoices for "${val}"...`);
              setTimeout(() => {
                window.location.href = 'Invoices.php';
              }, 600);
            }
          }
        });
      }

      // Keyboard Shortcut Ctrl+K
      document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
          e.preventDefault();
          if (omniSearch) {
            omniSearch.focus();
            omniSearch.select();
          }
        }
      });
    }
  };

  window.finApp = finApp;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => finApp.init());
  } else {
    finApp.init();
  }
})();
