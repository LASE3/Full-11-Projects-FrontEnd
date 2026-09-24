/**
 * VOSTOKPRIBOR IT Helpdesk & Support Operations System (System 09)
 * Subdomain: helpdesk.vostokpribor.local
 */

(function () {
  'use strict';

  const hdApp = {
    // Ticket Dataset
    tickets: [
      {
        id: 'TICK-8819',
        requester: {
          name: 'Dr. Elena Rostova',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM',
          role: 'Chief Optical Calibration Architect'
        },
        system: 'SCADA Modbus Gateway #3 (Lipetsk Bay)',
        priority: 'Critical', // Critical | High | Medium | Low
        tech: {
          name: 'Alexey Ivanov',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg',
          tier: 'Tier 3 SCADA Engineer'
        },
        status: 'In Progress', // Open | In Progress | Escalated | Resolved
        created: 'Today · 08:30 MSK',
        slaRemaining: '01:42:15',
        slaPercent: 78,
        description: 'Telemetry frame drop on RS-485 bus #3 connecting high-temp pyrometer array. Packet loss exceeding 14.8% during hot blast cycle.'
      },
      {
        id: 'TICK-8820',
        requester: {
          name: 'Dr. Mikhail Abramov',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg',
          role: 'Principal Semiconductor Physicist'
        },
        system: 'Cleanroom Biometric Scanner Bay B',
        priority: 'Critical',
        tech: {
          name: 'Alexey Ivanov',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg',
          tier: 'Tier 3 SCADA Engineer'
        },
        status: 'In Progress',
        created: 'Today · 09:12 MSK',
        slaRemaining: '00:48:30',
        slaPercent: 90,
        description: 'Class 4 Cleanroom airlock interlock rejecting authenticated Level 3 smartcard RFID credentials.'
      },
      {
        id: 'TICK-8821',
        requester: {
          name: 'Viktor Morozov',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg',
          role: 'Lead SCADA Gateway Specialist'
        },
        system: 'FAT Triangulation Laser Calibration Server',
        priority: 'High',
        tech: {
          name: 'Dmitry Popov',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV',
          tier: 'Tier 2 Infrastructure'
        },
        status: 'Open',
        created: 'Today · 10:05 MSK',
        slaRemaining: '02:15:00',
        slaPercent: 55,
        description: 'Automated calibration routine crashing on 64-bit floating point matrix overflow during high-speed profile tests.'
      },
      {
        id: 'TICK-8822',
        requester: {
          name: 'Anna Belova',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM',
          role: 'Head of Quality Assurance'
        },
        system: 'ISO 9001 Electronic Certificate Signer',
        priority: 'Medium',
        tech: {
          name: 'Sofia Volkova',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM',
          tier: 'Tier 1 Support'
        },
        status: 'In Progress',
        created: 'Yesterday · 16:40 MSK',
        slaRemaining: '05:30:00',
        slaPercent: 40,
        description: 'Cryptographic smartcard PKI token renewal required for electronic FAT test report signing.'
      },
      {
        id: 'TICK-8823',
        requester: {
          name: 'Svetlana Petrova',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM',
          role: 'Strategic Component Buyer'
        },
        system: 'ERP Procurement Signing Authority Module',
        priority: 'Low',
        tech: {
          name: 'Sofia Volkova',
          avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM',
          tier: 'Tier 1 Support'
        },
        status: 'Open',
        created: 'Yesterday · 14:15 MSK',
        slaRemaining: '18:45:00',
        slaPercent: 20,
        description: 'Request for secondary approval delegation during scheduled annual leave.'
      }
    ],

    showToast: function (title, message, type = 'orange') {
      const container = document.getElementById('toast-container');
      if (!container) return;

      const toast = document.createElement('div');
      let icon = '⚡';
      if (type === 'red' || type === 'critical') icon = '🚨';
      if (type === 'green' || type === 'success') icon = '✓';
      if (type === 'amber') icon = '⚠️';

      toast.className = 'hd-toast';
      if (type === 'red' || type === 'critical') toast.style.borderLeftColor = 'var(--hd-priority-critical)';
      if (type === 'green' || type === 'success') toast.style.borderLeftColor = 'var(--hd-success)';
      if (type === 'amber') toast.style.borderLeftColor = 'var(--hd-amber)';

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

    // Filter Ticket Queue Table
    filterTickets: function () {
      const search = document.getElementById('ticket-search');
      const priority = document.getElementById('filter-priority');
      const system = document.getElementById('filter-system');
      const status = document.getElementById('filter-status');
      const tech = document.getElementById('filter-tech');

      const q = (search ? search.value : '').toLowerCase();
      const p = priority ? priority.value : 'all';
      const s = system ? system.value : 'all';
      const st = status ? status.value : 'all';
      const t = tech ? tech.value : 'all';

      const rows = document.querySelectorAll('.ticket-table-body tr');
      rows.forEach(row => {
        const rowId = (row.getAttribute('data-id') || '').toLowerCase();
        const rowReq = (row.getAttribute('data-requester') || '').toLowerCase();
        const rowSys = row.getAttribute('data-system') || '';
        const rowPrio = row.getAttribute('data-priority') || '';
        const rowStat = row.getAttribute('data-status') || '';
        const rowTech = row.getAttribute('data-tech') || '';

        const matchSearch = !q || rowId.includes(q) || rowReq.includes(q) || rowSys.toLowerCase().includes(q);
        const matchPrio = p === 'all' || rowPrio === p;
        const matchSys = s === 'all' || rowSys === s;
        const matchStat = st === 'all' || rowStat === st;
        const matchTech = t === 'all' || rowTech === t;

        if (matchSearch && matchPrio && matchSys && matchStat && matchTech) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    },

    // Bulk Assign Action
    bulkAssign: function () {
      this.showToast('Bulk Triage', '4 unassigned tickets assigned to On-Duty Tier 2 Techs.', 'orange');
    },

    // Live SLA Timer Ticker
    startSLATimer: function () {
      const timerEl = document.getElementById('live-sla-timer');
      if (!timerEl) return;

      let totalSeconds = 1 * 3600 + 42 * 60 + 15;

      setInterval(() => {
        if (totalSeconds > 0) {
          totalSeconds--;
          const hrs = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
          const mins = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
          const secs = String(totalSeconds % 60).padStart(2, '0');
          timerEl.textContent = `${hrs}:${mins}:${secs}`;
        }
      }, 1000);
    },

    // Send Message in Ticket Conversation
    sendMessage: function () {
      const input = document.getElementById('chat-reply-input');
      const thread = document.getElementById('chat-conversation-thread');
      if (!input || !thread || !input.value.trim()) return;

      const text = input.value.trim();
      const msgRow = document.createElement('div');
      msgRow.className = 'chat-msg-row tech-msg';
      msgRow.innerHTML = `
        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg" alt="Alexey" class="chat-avatar" />
        <div class="chat-bubble">
          <div class="chat-msg-header">
            <strong>Alexey Ivanov (Tier 3 IT Tech)</strong>
            <span>Just now</span>
          </div>
          <p>${text}</p>
        </div>
      `;

      thread.appendChild(msgRow);
      input.value = '';
      this.showToast('Message Dispatched', 'Response transmitted to requester and logged in ticket audit ledger.', 'green');
    },

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
              hdApp.showToast('Helpdesk Search', `Searching tickets and knowledge base for "${val}"...`);
              setTimeout(() => {
                window.location.href = 'TicketQueue.php';
              }, 600);
            }
          }
        });
      }

      this.startSLATimer();

      // Initialize responsive multi-device layout controls
      this.initResponsiveLayout();
    },

    initResponsiveLayout: function () {
      const brandSection = document.querySelector('.brand-section') || document.querySelector('.top-nav__content');
      let toggleBtn = document.getElementById('hd-sidebar-toggle');
      if (!toggleBtn && brandSection) {
        toggleBtn = document.createElement('button');
        toggleBtn.id = 'hd-sidebar-toggle';
        toggleBtn.className = 'mobile-nav-toggle';
        toggleBtn.setAttribute('aria-label', 'Toggle Navigation Menu');
        toggleBtn.innerHTML = '☰';
        brandSection.insertBefore(toggleBtn, brandSection.firstChild);
      }

      let backdrop = document.querySelector('.sidebar-backdrop');
      if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
      }

      if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          document.body.classList.toggle('sidebar-open');
          toggleBtn.innerHTML = document.body.classList.contains('sidebar-open') ? '✕' : '☰';
        });
      }

      backdrop.addEventListener('click', () => {
        document.body.classList.remove('sidebar-open');
        if (toggleBtn) toggleBtn.innerHTML = '☰';
      });

      document.querySelectorAll('.sidebar-nav-item, .sidebar a').forEach(link => {
        link.addEventListener('click', () => {
          if (window.innerWidth <= 1024) {
            document.body.classList.remove('sidebar-open');
            if (toggleBtn) toggleBtn.innerHTML = '☰';
          }
        });
      });

      document.querySelectorAll('table.hd-table, table.data-table, table').forEach(table => {
        if (!table.parentElement.classList.contains('table-responsive') && !table.parentElement.classList.contains('hd-table-container')) {
          const wrapper = document.createElement('div');
          wrapper.className = 'table-responsive';
          table.parentNode.insertBefore(wrapper, table);
          wrapper.appendChild(table);
        }
      });

      window.addEventListener('resize', () => {
        if (window.innerWidth > 1024 && document.body.classList.contains('sidebar-open')) {
          document.body.classList.remove('sidebar-open');
          if (toggleBtn) toggleBtn.innerHTML = '☰';
        }
      });
    }
  };

  window.hdApp = hdApp;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => hdApp.init());
  } else {
    hdApp.init();
  }
})();
