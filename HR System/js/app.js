/**
 * VOSTOKPRIBOR HR & Human Capital Management System (System 07)
 * Subdomain: hr.vostokpribor.local
 */

(function () {
  'use strict';

  const hrApp = {
    // Employee Records Store
    employees: [
      {
        id: 'EMP-VP-0142',
        name: 'Dr. Elena Rostova',
        department: 'Optical Sensors Engineering',
        title: 'Chief Optical Calibration Architect',
        clearance: 4, // Level 4: Top Secret / Executive
        status: 'Active',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM',
        phone: '+7 (812) 409-22-11 ext. 301',
        email: 'e.rostova@vostokpribor.local',
        hireDate: 'March 14, 2018',
        supervisor: 'Academician V. K. Morozov (VP R&D)',
        facility: 'St. Petersburg Central R&D Complex',
        docs: [
          { name: 'Labor Contract #LC-2018-0142.pdf', type: 'Employment Agreement', date: 'Mar 2018' },
          { name: 'NDA Special Addendum (GOST R 34.10).pdf', type: 'Classified NDA', date: 'Jan 2024' },
          { name: 'Rostest Metrology Pass (Optics).pdf', type: 'Certification', date: 'Aug 2024' },
          { name: 'Level 4 Security Clearance Dossier.pdf', type: 'Security Clearance', date: 'Verified' }
        ]
      },
      {
        id: 'EMP-VP-0188',
        name: 'Mikhail Sorokin',
        department: 'Field Operations & Metallurgy',
        title: 'Senior Field Systems Director',
        clearance: 4,
        status: 'Active',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg',
        phone: '+7 (812) 409-22-11 ext. 410',
        email: 'm.sorokin@vostokpribor.local',
        hireDate: 'November 05, 2019',
        supervisor: 'Director General',
        facility: 'Cherepovets Field Operations Center',
        docs: [
          { name: 'Labor Contract #LC-2019-0188.pdf', type: 'Employment Agreement', date: 'Nov 2019' },
          { name: 'Severstal On-Site Security Pass.pdf', type: 'Facility Clearance', date: 'Oct 2024' }
        ]
      },
      {
        id: 'EMP-VP-0219',
        name: 'Viktor Morozov',
        department: 'SCADA & Automation',
        title: 'Lead SCADA Gateway Specialist',
        clearance: 3, // Level 3: Secret SCADA
        status: 'Active',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg',
        phone: '+7 (812) 409-22-11 ext. 284',
        email: 'v.morozov@vostokpribor.local',
        hireDate: 'June 18, 2021',
        supervisor: 'Dr. Elena Rostova',
        facility: 'Lipetsk FAT Integration Bay',
        docs: [
          { name: 'Labor Contract #LC-2021-0219.pdf', type: 'Employment Agreement', date: 'Jun 2021' },
          { name: 'Level 3 Telemetry Security Clearance.pdf', type: 'Security Clearance', date: 'Verified' }
        ]
      },
      {
        id: 'EMP-VP-0310',
        name: 'Denis Sokolov',
        department: 'Blast Furnace Robotics',
        title: 'Hydraulic Actuator Specialist',
        clearance: 3,
        status: 'Active',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBxrM-O7aJYHYCDtkoA3WwbiOe6BxJ0vK7AcnogxwZN9MACsknTlpyGKyy-lWl2Hwn9IEZLPDCvVGrmxN2kvPEfzbJ5E4u5x6-38EP2exwXW8Dmm-7oMTzMG07_rmRLbT0xvZwQMFEwa4qJO5LcWbn58eWx3fSkVjAmSI3UWO8dCTgRg6GBgrY_MTUl-JF-JUf4K5CGPp0o4tvKoxbSqSysGT8r3j8de3w_sfk4F8p9ysiXXfbUkWPV',
        phone: '+7 (812) 409-22-11 ext. 190',
        email: 'd.sokolov@vostokpribor.local',
        hireDate: 'February 12, 2022',
        supervisor: 'Viktor Morozov',
        facility: 'Nizhny Tagil Heavy Bay #4',
        docs: [
          { name: 'Labor Contract #LC-2022-0310.pdf', type: 'Employment Agreement', date: 'Feb 2022' },
          { name: 'Industrial Safety Pass (Blast Furnace).pdf', type: 'Safety Certification', date: 'Sep 2024' }
        ]
      },
      {
        id: 'EMP-VP-0404',
        name: 'Anna Belova',
        department: 'Quality & FAT Testing',
        title: 'Head of Quality Assurance & Verification',
        clearance: 2, // Level 2: Confidential
        status: 'Active',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM',
        phone: '+7 (812) 409-22-11 ext. 115',
        email: 'a.belova@vostokpribor.local',
        hireDate: 'August 01, 2020',
        supervisor: 'Director of Manufacturing',
        facility: 'St. Petersburg Cleanroom Facility',
        docs: [
          { name: 'Labor Contract #LC-2020-0404.pdf', type: 'Employment Agreement', date: 'Aug 2020' },
          { name: 'ISO 9001 / GOST Lead Auditor Certificate.pdf', type: 'Certification', date: 'Jul 2024' }
        ]
      },
      {
        id: 'EMP-VP-0492',
        name: 'Dr. Mikhail Abramov',
        department: 'R&D Labs & Sensor Fabrication',
        title: 'Principal Semiconductor Physicist',
        clearance: 3,
        status: 'Probation',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg',
        phone: '+7 (812) 409-22-11 ext. 504',
        email: 'm.abramov@vostokpribor.local',
        hireDate: 'October 15, 2024',
        supervisor: 'Dr. Elena Rostova',
        facility: 'Nanofabrication Facility Bay B',
        docs: [
          { name: 'Candidate Onboarding Checklist.pdf', type: 'Onboarding Dossier', date: 'In Progress' },
          { name: 'Ph.D. State Dissertation Verification.pdf', type: 'Credentials', date: 'Oct 2024' }
        ]
      },
      {
        id: 'EMP-VP-0518',
        name: 'Svetlana Petrova',
        department: 'Procurement & Logistics',
        title: 'Strategic Component Buyer',
        clearance: 2,
        status: 'On Leave',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ9P2QbrRU2xObdFOu9aNA-iyUeUZ6UvBFT0l0KnTu1MKDRX0c84gVy9VkzAjtaXzw0JcEGYWbxd3RDqaIh7AyD6h4njnD-XTgLNnu6wa-UaOplKQCaWIDACINffaFufLMrEaDfvX7J3bqgPCT5b9oY66PI4s0dfAwRgA_V8p0oKzRnCAU0tihwWPq8xzU4FbT1iUoh0cBzt9pq7gPkXJVjA32ZA7kWXQvcLq090IXW-8Ihh_efhmM',
        phone: '+7 (812) 409-22-11 ext. 330',
        email: 's.petrova@vostokpribor.local',
        hireDate: 'January 10, 2023',
        supervisor: 'Head of Global Supply Chain',
        facility: 'St. Petersburg Central Hub',
        docs: [
          { name: 'Labor Contract #LC-2023-0518.pdf', type: 'Employment Agreement', date: 'Jan 2023' },
          { name: 'Annual Paid Leave Approval (Nov 10-24).pdf', type: 'Leave Record', date: 'Approved' }
        ]
      },
      {
        id: 'EMP-VP-0580',
        name: 'Yury Vasiliev',
        department: 'Corporate & Legal Governance',
        title: 'Senior Corporate Counsel',
        clearance: 4,
        status: 'Active',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDoVYMImYMOrFG-GImEjxCUij3YIwCjbxiUVg9-84NgNQUnx44rwhCbh4EVKLngwn6R5_hzNhRQkfTglEUz1jtP83GRGR8WbDdiIQblwg1fLV0mqc04y19GGKO27NGBpanqADz4vwO3ANY9KcZiOXBusZHAE_PU_FuuwKqChSLXXJsGo289bHOL3MFrKWoXXMoxnqoUIglg-NYsM99jg8cA3e1CeWhqlY0x7isLHdQfGbcFE_XiNNJg',
        phone: '+7 (812) 409-22-11 ext. 102',
        email: 'y.vasiliev@vostokpribor.local',
        hireDate: 'September 01, 2017',
        supervisor: 'General Legal Directorate',
        facility: 'Executive Legal Office',
        docs: [
          { name: 'Labor Contract #LC-2017-0580.pdf', type: 'Employment Agreement', date: 'Sep 2017' },
          { name: 'Level 4 Security Clearance (State Secrets).pdf', type: 'Security Clearance', date: 'Verified' }
        ]
      }
    ],

    // Show Toast Notification
    showToast: function (title, message, type = 'plum') {
      const container = document.getElementById('toast-container');
      if (!container) return;

      const toast = document.createElement('div');
      let icon = 'ℹ️';
      if (type === 'success') icon = '✓';
      if (type === 'alert' || type === 'red') icon = '🔒';
      if (type === 'amber') icon = '⚡';

      toast.className = 'hr-toast';
      if (type === 'alert' || type === 'red') toast.style.borderLeftColor = 'var(--hr-confidential)';
      if (type === 'amber') toast.style.borderLeftColor = 'var(--hr-amber)';
      if (type === 'success') toast.style.borderLeftColor = 'var(--hr-success)';

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
        const input = modal.querySelector('input, select');
        if (input) input.focus();
      }
    },

    closeModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) modal.classList.remove('active');
    },

    // Inspect Employee Details Drawer
    inspectEmployee: function (empId) {
      const emp = this.employees.find(e => e.id === empId);
      if (!emp) return;

      const nameEl = document.getElementById('drawer-emp-name');
      const titleEl = document.getElementById('drawer-emp-title');
      const idEl = document.getElementById('drawer-emp-id');
      const deptEl = document.getElementById('drawer-emp-dept');
      const clearanceEl = document.getElementById('drawer-emp-clearance');
      const statusEl = document.getElementById('drawer-emp-status');
      const avatarEl = document.getElementById('drawer-emp-avatar');
      const phoneEl = document.getElementById('drawer-emp-phone');
      const emailEl = document.getElementById('drawer-emp-email');
      const hireEl = document.getElementById('drawer-emp-hire');
      const supEl = document.getElementById('drawer-emp-sup');
      const facilityEl = document.getElementById('drawer-emp-facility');
      const docsWrapper = document.getElementById('drawer-emp-docs');

      if (nameEl) nameEl.textContent = emp.name;
      if (titleEl) titleEl.textContent = emp.title;
      if (idEl) idEl.textContent = emp.id;
      if (deptEl) deptEl.textContent = emp.department;
      if (avatarEl) avatarEl.src = emp.avatar;
      if (phoneEl) phoneEl.textContent = emp.phone;
      if (emailEl) emailEl.textContent = emp.email;
      if (hireEl) hireEl.textContent = emp.hireDate;
      if (supEl) supEl.textContent = emp.supervisor;
      if (facilityEl) facilityEl.textContent = emp.facility;

      if (clearanceEl) {
        clearanceEl.className = `clearance-badge clearance-l${emp.clearance}`;
        clearanceEl.innerHTML = `<span>🔒</span><span>Level ${emp.clearance} Clearance</span>`;
      }

      if (statusEl) {
        let statusClass = 'status-active';
        if (emp.status === 'Probation') statusClass = 'status-probation';
        if (emp.status === 'On Leave') statusClass = 'status-leave';
        if (emp.status === 'Offboarding') statusClass = 'status-offboarding';
        statusEl.className = `status-pill ${statusClass}`;
        statusEl.textContent = emp.status;
      }

      if (docsWrapper) {
        docsWrapper.innerHTML = '';
        emp.docs.forEach(doc => {
          const row = document.createElement('div');
          row.className = 'doc-item-row';
          row.innerHTML = `
            <div>
              <div style="font-weight: 600; color: var(--hr-navy); font-size: 12px;">${doc.name}</div>
              <div style="font-size: 10.5px; color: var(--hr-text-muted);">${doc.type} • ${doc.date}</div>
            </div>
            <button class="btn btn-outline btn-sm" onclick="window.hrApp.showToast('Secure Document Access', 'Accessing encrypted dossier: ${doc.name}')">View 🔒</button>
          `;
          docsWrapper.appendChild(row);
        });
      }

      this.openModal('modal-employee-detail');
    },

    // Search and Filter Employees
    filterEmployees: function () {
      const searchInput = document.getElementById('employee-table-search');
      const deptFilter = document.getElementById('employee-dept-filter');
      const clearanceFilter = document.getElementById('employee-clearance-filter');

      const query = (searchInput ? searchInput.value : '').toLowerCase();
      const selectedDept = deptFilter ? deptFilter.value : 'all';
      const selectedClearance = clearanceFilter ? clearanceFilter.value : 'all';

      const rows = document.querySelectorAll('.employee-table-body tr');
      rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const id = row.getAttribute('data-id') || '';
        const dept = row.getAttribute('data-dept') || '';
        const clearance = row.getAttribute('data-clearance') || '';

        const matchQuery = !query || name.toLowerCase().includes(query) || id.toLowerCase().includes(query);
        const matchDept = selectedDept === 'all' || dept === selectedDept;
        const matchClearance = selectedClearance === 'all' || clearance === selectedClearance;

        if (matchQuery && matchDept && matchClearance) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    },

    // Toggle Onboarding Step Action
    toggleOnboardingStep: function (stepId) {
      const step = document.getElementById(stepId);
      if (!step) return;

      if (step.classList.contains('in-progress')) {
        step.classList.remove('in-progress');
        step.classList.add('completed');
        const icon = step.querySelector('.step-node-icon');
        if (icon) icon.innerHTML = '✓';
        this.showToast('Onboarding Step Completed', 'State verification stored to personnel ledger.', 'success');
      } else if (step.classList.contains('pending')) {
        step.classList.remove('pending');
        step.classList.add('in-progress');
        const icon = step.querySelector('.step-node-icon');
        if (icon) icon.innerHTML = '⚡';
        this.showToast('Step In-Progress', 'Assigned compliance officer notified.', 'amber');
      }
    },

    // Init
    init: function () {
      // Auto-highlight active sidebar item based on current URL
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

      // Global Omni Search
      const omniSearch = document.getElementById('global-omni-search');
      if (omniSearch) {
        omniSearch.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            const val = omniSearch.value.trim();
            if (val) {
              hrApp.showToast('Personnel Search', `Filtering records for "${val}" in Employee Directory...`);
              setTimeout(() => {
                window.location.href = 'EmployeeRecords.php';
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

  window.hrApp = hrApp;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => hrApp.init());
  } else {
    hrApp.init();
  }
})();
