/**
 * VOSTOKPRIBOR ENTERPRISE NOTIFICATIONS HUB - CLIENT ENGINE
 * Connects any system's topbar notification bell directly to live MySQL notifications.
 */
(function() {
  'use strict';

  // Inject CSS if not already loaded
  if (!document.getElementById('vstk-notifications-css')) {
    const link = document.createElement('link');
    link.id = 'vstk-notifications-css';
    link.rel = 'stylesheet';
    const basePath = window.location.pathname.includes('/Full-11-Projects-FrontEnd/')
      ? '/Full-11-Projects-FrontEnd/assets/css/notifications-hub.css'
      : '../assets/css/notifications-hub.css';
    link.href = basePath;
    document.head.appendChild(link);
  }

  const apiEndpoint = window.location.pathname.includes('/Full-11-Projects-FrontEnd/')
    ? '/Full-11-Projects-FrontEnd/api/notifications.php'
    : '../api/notifications.php';

  function initNotificationHub() {
    // Specifically locate the notification button in header/topbar
    let bellBtn = document.querySelector(
      '#notifications-toggle-btn, ' +
      'header #notifications-toggle-btn, ' +
      '.top-nav #notifications-toggle-btn, ' +
      '.vk-top-navbar #notifications-toggle-btn, ' +
      'button.notifications-btn, ' +
      'header button.notifications-btn, ' +
      'header button[title*="Notification" i], ' +
      'header button[title*="Alert" i], ' +
      'header #header-bell-btn, ' +
      '#header-bell-btn, ' +
      '.top-nav button[title*="Notification" i], ' +
      '[data-notification-trigger]'
    );

    // Fallback: search header buttons for notification bell icon
    if (!bellBtn) {
      const headerBtns = document.querySelectorAll('header button, .top-nav button, .vk-top-navbar button, .vk-top-header button');
      for (const btn of headerBtns) {
        if (btn.classList.contains('top-signout-btn')) continue;
        const iconSpan = btn.querySelector('.material-symbols-outlined, .material-icons');
        if (iconSpan && iconSpan.textContent.trim().toLowerCase().includes('notifications')) {
          bellBtn = btn;
          break;
        }
      }
    }

    let actualBtn = bellBtn;
    if (actualBtn && actualBtn.tagName === 'path') {
      actualBtn = actualBtn.closest('button');
    }

    // Always remove any rogue badge that might have ended up near Sign Out
    document.querySelectorAll('.top-signout-btn .vstk-notif-badge, .top-nav__actions > .vstk-notif-badge').forEach(b => b.remove());

    if (!actualBtn) return;

    // Ensure actualBtn has positioning
    actualBtn.classList.add('vstk-notif-btn');
    actualBtn.style.position = 'relative';

    // Wrap ONLY the notification button in a dedicated positioning wrapper
    let wrapper = actualBtn.parentElement;
    if (!wrapper || !wrapper.classList.contains('vstk-notif-btn-wrapper')) {
      wrapper = document.createElement('div');
      wrapper.className = 'vstk-notif-btn-wrapper';
      wrapper.style.position = 'relative';
      wrapper.style.display = 'inline-flex';
      wrapper.style.alignItems = 'center';
      wrapper.style.justifyContent = 'center';
      wrapper.style.verticalAlign = 'middle';
      actualBtn.parentNode.insertBefore(wrapper, actualBtn);
      wrapper.appendChild(actualBtn);
    }

    // Create or locate badge element strictly inside actualBtn for 100% anchor accuracy
    let badgeEl = actualBtn.querySelector('.vstk-notif-badge');
    if (!badgeEl) {
      badgeEl = document.createElement('span');
      badgeEl.className = 'vstk-notif-badge';
      badgeEl.style.display = 'none';
      actualBtn.appendChild(badgeEl);
    }

    // Hide any old static dots or hardcoded badges inside actualBtn or wrapper
    wrapper.querySelectorAll('.badge-dot, #notif-unread-count, #bell-unread-dot, .font-telemetry-micro').forEach(d => {
      if (d !== badgeEl) d.style.display = 'none';
    });

    // Create or locate popover dropdown strictly inside wrapper
    let dropdown = wrapper.querySelector('.vstk-notif-dropdown');
    if (!dropdown) {
      dropdown = document.createElement('div');
      dropdown.className = 'vstk-notif-dropdown';
      dropdown.innerHTML = `
        <div class="vstk-notif-header">
          <div class="vstk-notif-header-title">
            <span>Enterprise Telemetry</span>
            <span class="vstk-notif-header-badge" id="vstk-notif-header-count">0 New</span>
          </div>
          <button type="button" class="vstk-notif-mark-read-btn" id="vstk-mark-all-read">Mark All Read</button>
        </div>
        <div class="vstk-notif-list" id="vstk-notif-list">
          <div class="vstk-notif-empty">Fetching notifications from database...</div>
        </div>
      `;
      wrapper.appendChild(dropdown);
    }

    let notificationsData = [];

    // Fetch notifications from live DB
    function fetchNotifications() {
      fetch(apiEndpoint)
        .then(res => res.json())
        .then(data => {
          if (data && data.success) {
            notificationsData = data.notifications || [];
            updateUI(data.unread_count || 0);
          }
        })
        .catch(err => {
          console.warn('Notification hub fetch warning:', err);
        });
    }

    // Update UI elements
    function updateUI(unreadCount) {
      if (unreadCount > 0) {
        badgeEl.textContent = unreadCount > 99 ? '99+' : unreadCount;
        badgeEl.style.display = 'inline-block';
      } else {
        badgeEl.style.display = 'none';
      }

      const headerBadge = dropdown.querySelector('#vstk-notif-header-count');
      if (headerBadge) {
        headerBadge.textContent = unreadCount > 0 ? `${unreadCount} New` : 'All Caught Up';
      }

      const listEl = dropdown.querySelector('#vstk-notif-list');
      if (!listEl) return;

      if (notificationsData.length === 0) {
        listEl.innerHTML = '<div class="vstk-notif-empty">No telemetry notifications on record.</div>';
        return;
      }

      let html = '';
      notificationsData.forEach(item => {
        const severity = item.severity || 'info';
        const unreadClass = item.is_read == 0 ? 'unread' : '';
        const sysCode = item.system_code || 'SYS';

        html += `
          <div class="vstk-notif-item ${unreadClass}">
            <div class="vstk-notif-item-icon severity-${severity}">
              ${sysCode}
            </div>
            <div class="vstk-notif-content">
              <div class="vstk-notif-title-row">
                <div class="vstk-notif-title">${escapeHtml(item.title || 'System Alert')}</div>
                <div class="vstk-notif-time">${escapeHtml(item.time_ago || '')}</div>
              </div>
              <div class="vstk-notif-msg">${escapeHtml(item.message || '')}</div>
              <span class="vstk-notif-footer-tag">${escapeHtml(item.related_entity_type || 'Telemetry')} // ${escapeHtml(item.related_entity_id || 'LOG')}</span>
            </div>
          </div>
        `;
      });

      listEl.innerHTML = html;
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    // Toggle dropdown
    actualBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      e.preventDefault();
      const isOpen = dropdown.classList.contains('show');
      document.querySelectorAll('.vstk-notif-dropdown.show, .notifications-popover.show').forEach(d => d.classList.remove('show'));
      // Close any ecosystem dropdowns
      document.querySelectorAll('.ecosystem-dropdown.show, #ecosystem-dropdown.show').forEach(el => el.classList.remove('show'));
      if (!isOpen) {
        dropdown.classList.add('show');
        fetchNotifications();
      }
    });

    // Close notifications when ecosystem switcher button is clicked
    document.querySelectorAll('#ecosystem-toggle-btn, [title*="Ecosystem" i], .ecosystem-btn').forEach(eco => {
      eco.addEventListener('click', function() {
        dropdown.classList.remove('show');
      });
    });

    // Mark all as read
    const markReadBtn = dropdown.querySelector('#vstk-mark-all-read');
    if (markReadBtn) {
      markReadBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fetch(apiEndpoint + '?action=mark_all_read', { method: 'POST' })
          .then(res => res.json())
          .then(res => {
            if (res && res.success) {
              notificationsData.forEach(n => n.is_read = 1);
              updateUI(0);
            }
          })
          .catch(err => console.error(err));
      });
    }

    // Close on click outside
    document.addEventListener('click', function(e) {
      if (!wrapper.contains(e.target)) {
        dropdown.classList.remove('show');
      }
    });

    // Close on escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
      }
    });

    // Initial fetch
    fetchNotifications();

    // Auto poll every 45 seconds for new DB events
    setInterval(fetchNotifications, 45000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNotificationHub);
  } else {
    initNotificationHub();
  }
})();
