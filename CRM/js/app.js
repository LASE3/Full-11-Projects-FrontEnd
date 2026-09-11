/**
 * VOSTOKPRIBOR Enterprise CRM Platform - Core Application Logic (System 04)
 * Subdomain: crm.vostokpribor.local
 */

(function () {
  'use strict';

  // Global App Registry
  const crmApp = {
    // Current active deal dataset for Kanban
    opportunities: [
      {
        id: 'OPP-2024-9101',
        client: 'Severstal Metallurgy PJSC',
        title: 'Blast Furnace #5 Automation & Gas Analysis',
        value: 1850000,
        stage: 'negotiation', // qualification | proposal | negotiation | contract | won
        probability: 85,
        closeDate: 'Nov 28, 2024',
        rep: { name: 'Elena Rostova', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM' },
        confidential: true,
        priority: 'high'
      },
      {
        id: 'OPP-2024-9102',
        client: 'NLMK Group Lipetsk',
        title: 'Coke Oven Battery Temperature Profiling & IR Cameras',
        value: 920000,
        stage: 'proposal',
        probability: 60,
        closeDate: 'Dec 05, 2024',
        rep: { name: 'Mikhail Sorokin', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg' },
        confidential: false,
        priority: 'medium'
      },
      {
        id: 'OPP-2024-9103',
        client: 'Norilsk Nickel Mining',
        title: 'Talnakh Concentrator Flotation Telemetry Grid',
        value: 2400000,
        stage: 'negotiation',
        probability: 80,
        closeDate: 'Dec 18, 2024',
        rep: { name: 'Mikhail Sorokin', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg' },
        confidential: true,
        priority: 'high'
      },
      {
        id: 'OPP-2024-9104',
        client: 'Severstal Hot Strip Mill #2',
        title: 'Hydraulic Pressure Sensor Telemetry Retrofit',
        value: 640000,
        stage: 'contract',
        probability: 95,
        closeDate: 'Nov 20, 2024',
        rep: { name: 'Viktor Morozov', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg' },
        confidential: false,
        priority: 'high'
      },
      {
        id: 'OPP-2024-9105',
        client: 'EVRAZ Consolidated',
        title: 'Rail Mill Laser Profiler & Flaw Detection Array',
        value: 1650000,
        stage: 'qualification',
        probability: 40,
        closeDate: 'Jan 15, 2025',
        rep: { name: 'Denis Sokolov', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV' },
        confidential: true,
        priority: 'medium'
      },
      {
        id: 'OPP-2024-9106',
        client: 'PhosAgro Chemical',
        title: 'High-Pressure Flowmeter HPF-900X Replacement Batch',
        value: 418200,
        stage: 'won',
        probability: 100,
        closeDate: 'Nov 12, 2024',
        rep: { name: 'Elena Rostova', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM' },
        confidential: false,
        priority: 'low'
      },
      {
        id: 'OPP-2024-9107',
        client: 'Gazprom Neft Omsk',
        title: 'Refinery Catalytic Cracking Gas Analysis Skid',
        value: 3100000,
        stage: 'qualification',
        probability: 35,
        closeDate: 'Feb 10, 2025',
        rep: { name: 'Mikhail Sorokin', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg' },
        confidential: true,
        priority: 'high'
      },
      {
        id: 'OPP-2024-9108',
        client: 'Severstal Metallurgy PJSC',
        title: 'Continuous Casting Machine #3 Optical Thickness Gauges',
        value: 410000,
        stage: 'proposal',
        probability: 65,
        closeDate: 'Dec 02, 2024',
        rep: { name: 'Elena Rostova', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM' },
        confidential: false,
        priority: 'medium'
      },
      {
        id: 'OPP-2024-9109',
        client: 'NLMK Group Lipetsk',
        title: 'Turbine Bearing Vibration Transducers (x16)',
        value: 396400,
        stage: 'won',
        probability: 100,
        closeDate: 'Nov 08, 2024',
        rep: { name: 'Viktor Morozov', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg' },
        confidential: false,
        priority: 'medium'
      }
    ],

    // Format currency USD
    formatUSD: function(num) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(num);
    },

    // Show Toast Notification
    showToast: function (title, message, type = 'info') {
      const container = document.getElementById('toast-container');
      if (!container) return;

      const toast = document.createElement('div');
      let typeClass = '';
      let icon = 'ℹ️';
      if (type === 'amber' || type === 'warning') {
        typeClass = 'toast-amber';
        icon = '⚡';
      } else if (type === 'success') {
        typeClass = 'toast-success';
        icon = '✓';
      } else if (type === 'danger' || type === 'confidential') {
        typeClass = 'toast-danger';
        icon = '🔒';
      }

      toast.className = `crm-toast ${typeClass}`;
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

    // Modals
    openModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('active');
        const firstInput = modal.querySelector('input, select');
        if (firstInput) firstInput.focus();
      }
    },

    closeModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('active');
      }
    },

    // Switch Customer Detail Tabs (Overview, Projects, Documents, Contracts, Support)
    switchCustomerTab: function (tabName) {
      const tabButtons = document.querySelectorAll('.customer-tab-btn');
      tabButtons.forEach(btn => {
        if (btn.getAttribute('data-tab') === tabName) {
          btn.classList.add('active');
        } else {
          btn.classList.remove('active');
        }
      });

      const panels = document.querySelectorAll('.tab-content-panel');
      panels.forEach(p => {
        p.classList.remove('active');
      });

      const activePanel = document.getElementById(`tab-panel-${tabName}`);
      if (activePanel) {
        activePanel.classList.add('active');
      }
    },

    // Convert Lead to Customer Interactive Action
    convertLeadToCustomer: function(leadName, leadCompany, leadValue) {
      const modal = document.getElementById('modal-convert-customer');
      if (modal) {
        document.getElementById('convert-lead-name').textContent = leadName;
        document.getElementById('convert-lead-company').textContent = leadCompany;
        document.getElementById('convert-lead-val').textContent = leadValue;
        this.openModal('modal-convert-customer');
      } else {
        this.showToast('Lead Converted', `${leadCompany} (${leadName}) converted to active enterprise customer account.`, 'success');
      }
    },

    // Render Kanban Cards (for Opportunities page)
    renderKanban: function () {
      const stages = ['qualification', 'proposal', 'negotiation', 'contract', 'won'];
      
      stages.forEach(stage => {
        const wrapper = document.getElementById(`kanban-cards-${stage}`);
        const countEl = document.getElementById(`col-count-${stage}`);
        const valEl = document.getElementById(`col-val-${stage}`);

        if (!wrapper) return;

        wrapper.innerHTML = '';
        const items = this.opportunities.filter(o => o.stage === stage);
        const stageTotalVal = items.reduce((acc, curr) => acc + curr.value, 0);

        if (countEl) countEl.textContent = items.length;
        if (valEl) valEl.textContent = this.formatUSD(stageTotalVal);

        items.forEach(opp => {
          const card = document.createElement('div');
          card.className = 'kanban-deal-card';
          card.draggable = true;
          card.setAttribute('data-id', opp.id);

          // Card Drag Events
          card.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', opp.id);
            card.style.opacity = '0.4';
          });

          card.addEventListener('dragend', () => {
            card.style.opacity = '1';
          });

          // Card Click to Inspect
          card.addEventListener('click', () => {
            crmApp.inspectOpportunity(opp.id);
          });

          const confidentialBadge = opp.confidential 
            ? `<span class="confidential-pill" style="font-size: 9px; padding: 1px 5px;">🔒 Confid.</span>`
            : '';

          card.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <span class="card-company-name">${opp.client}</span>
              ${confidentialBadge}
            </div>
            <div class="card-deal-title">${opp.title}</div>
            <div style="display: flex; align-items: baseline; justify-content: space-between;">
              <span class="card-value-badge">${crmApp.formatUSD(opp.value)}</span>
              <span style="font-size: 10px; font-family: var(--crm-font-mono); color: var(--crm-indigo); font-weight: 700;">${opp.probability}% Prob.</span>
            </div>
            <div class="card-footer-row">
              <span class="card-close-date">
                <span>📅</span>
                <span>${opp.closeDate}</span>
              </span>
              <img src="${opp.rep.avatar}" alt="${opp.rep.name}" class="card-rep-avatar" title="Rep: ${opp.rep.name}" />
            </div>
          `;

          wrapper.appendChild(card);
        });
      });

      // Attach Column Drag/Drop Listeners
      stages.forEach(stage => {
        const col = document.getElementById(`kanban-col-${stage}`);
        if (!col) return;

        col.addEventListener('dragover', (e) => {
          e.preventDefault();
          col.style.backgroundColor = '#E5EBF2';
        });

        col.addEventListener('dragleave', () => {
          col.style.backgroundColor = '';
        });

        col.addEventListener('drop', (e) => {
          e.preventDefault();
          col.style.backgroundColor = '';
          const oppId = e.dataTransfer.getData('text/plain');
          const opp = crmApp.opportunities.find(o => o.id === oppId);
          if (opp && opp.stage !== stage) {
            opp.stage = stage;
            crmApp.renderKanban();
            crmApp.showToast('Stage Gating Updated', `${opp.client} moved to stage: ${stage.toUpperCase()}`, 'amber');
          }
        });
      });
    },

    // Inspect Opportunity Drawer/Modal
    inspectOpportunity: function (oppId) {
      const opp = this.opportunities.find(o => o.id === oppId);
      if (!opp) return;

      const titleEl = document.getElementById('inspect-opp-title');
      const clientEl = document.getElementById('inspect-opp-client');
      const valEl = document.getElementById('inspect-opp-value');
      const stageSelect = document.getElementById('inspect-opp-stage');
      const dateEl = document.getElementById('inspect-opp-date');
      const repEl = document.getElementById('inspect-opp-rep');

      if (titleEl) titleEl.textContent = opp.title;
      if (clientEl) clientEl.textContent = opp.client;
      if (valEl) valEl.textContent = this.formatUSD(opp.value);
      if (stageSelect) stageSelect.value = opp.stage;
      if (dateEl) dateEl.textContent = `${opp.closeDate} (${opp.probability}% win probability)`;
      if (repEl) repEl.textContent = opp.rep.name;

      this.openModal('modal-inspect-opportunity');
    },

    // Initialize Page
    init: function () {
      // Auto-highlight sidebar active link based on current filename
      const currentPath = window.location.pathname.toLowerCase();
      const sidebarLinks = document.querySelectorAll('.sidebar-nav-item');
      
      sidebarLinks.forEach(link => {
        const href = (link.getAttribute('href') || '').toLowerCase();
        if (href && (currentPath.endsWith(href) || (currentPath.endsWith('/') && href === 'dashboard.html') || (currentPath.endsWith('index.html') && href === 'dashboard.html'))) {
          link.classList.add('active');
        } else if (href && currentPath.includes(href.replace('.html', ''))) {
          link.classList.add('active');
        } else if (!href && link.classList.contains('active')) {
          // Keep active if explicitly rendered
        }
      });

      // Global Omni Search Listener
      const omniSearch = document.getElementById('global-omni-search');
      if (omniSearch) {
        omniSearch.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            const query = omniSearch.value.trim();
            if (query) {
              crmApp.showToast('Omni-Search Query', `Found 6 matched records for "${query}". Redirecting to Customer Ledger...`);
              setTimeout(() => {
                window.location.href = 'CustomerDetail.html';
              }, 600);
            }
          }
        });
      }

      // Shortcut: Ctrl+K / Cmd+K to focus search
      document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
          e.preventDefault();
          if (omniSearch) {
            omniSearch.focus();
            omniSearch.select();
          }
        }
      });

      // New Opportunity Form Handler
      const newOppForm = document.getElementById('form-new-opportunity');
      if (newOppForm) {
        newOppForm.addEventListener('submit', (e) => {
          e.preventDefault();
          const title = document.getElementById('new-opp-title').value;
          const client = document.getElementById('new-opp-client').value;
          const value = parseFloat(document.getElementById('new-opp-value').value) || 250000;
          const stage = document.getElementById('new-opp-stage').value;
          const closeDate = document.getElementById('new-opp-date').value || 'Dec 31, 2024';
          const confidential = document.getElementById('new-opp-confidential') ? document.getElementById('new-opp-confidential').checked : true;

          const newOpp = {
            id: `OPP-2024-${Math.floor(1000 + Math.random() * 9000)}`,
            client: client,
            title: title,
            value: value,
            stage: stage,
            probability: 70,
            closeDate: closeDate,
            rep: { name: 'Mikhail Sorokin', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg' },
            confidential: confidential,
            priority: 'high'
          };

          crmApp.opportunities.unshift(newOpp);
          crmApp.closeModal('modal-new-opportunity');
          newOppForm.reset();
          
          if (document.getElementById('kanban-cards-qualification')) {
            crmApp.renderKanban();
          }

          crmApp.showToast('Opportunity Created', `${newOpp.client} - ${crmApp.formatUSD(newOpp.value)} logged to CRM ledger.`, 'amber');
        });
      }

      // Initial Kanban Render if present
      if (document.getElementById('kanban-cards-qualification')) {
        this.renderKanban();
      }
    }
  };

  // Expose globally
  window.crmApp = crmApp;

  // Auto-run on DOM Ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => crmApp.init());
  } else {
    crmApp.init();
  }
})();
