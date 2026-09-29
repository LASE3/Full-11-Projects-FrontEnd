/**
 * VOSTOKPRIBOR Employee Intranet — Live Data Integration  (Class 4)
 * Requires: assets/js/api-core.js and assets/js/api-intranet.js loaded before this file.
 *
 * Pages covered:
 *  - Dashboard.php        → announcements feed
 *  - EmployeeDirectory.php → directory table
 *  - PoliciesAndForms.php  → leave requests + submit form
 */

(function () {
  "use strict";

  const core = window.VostokCore || window.VostokAPI || {};
  const intranet = window.VostokIntranet || window.VostokAPI?.intranet;
  const { ui = {}, escHtml = (s) => s } = core;
  const handleApiError = core.handleApiError || console.error;

  /* ──────────────────────────────────────────────
   * ANNOUNCEMENTS
   * Element: #announcements-feed
   * ────────────────────────────────────────────── */
  async function loadAnnouncements() {
    const feed = document.getElementById("announcements-feed");
    const badge = document.getElementById("announcements-badge");
    const navDirCount = document.getElementById("intra-nav-dir-count");

    if (navDirCount) {
      intranet.directory().then(dres => {
        if (dres && Array.isArray(dres.data)) {
          navDirCount.textContent = dres.data.length;
        }
      }).catch(() => { navDirCount.textContent = '—'; });
    }

    if (!feed) return;

    feed.innerHTML =
      '<div style="padding:24px;text-align:center;"><span class="vp-spinner"></span> Loading announcements…</div>';

    try {
      const res = await intranet.announcements();
      const items = res.data || [];

      if (badge) {
        badge.textContent = `${items.length} New`;
      }

      // Update Carousel with latest announcement if present
      const carTitle  = document.getElementById("carousel-slide-title");
      const carDesc   = document.getElementById("carousel-slide-desc");
      const carDate   = document.getElementById("carousel-slide-date");
      const carBadge  = document.getElementById("carousel-slide-badge");
      const carAuthor = document.getElementById("carousel-slide-author");

      if (items.length > 0) {
        const topItem = items[0];
        if (carTitle)  carTitle.textContent  = topItem.title || '';
        if (carDesc)   carDesc.textContent   = (topItem.body || topItem.content || '').substring(0, 240);
        if (carDate)   carDate.textContent   = ui.date(topItem.posted_at || topItem.published_at || topItem.created_at);
        if (carBadge)  carBadge.textContent  = topItem.audience_dept || topItem.announcement_type || 'DIRECTIVE';
        if (carAuthor) carAuthor.textContent = `${topItem.posted_by_name || topItem.author_name || 'Executive Staff'} • ${topItem.posted_by_role || 'Enterprise'}`;
      } else {
        if (carTitle)  carTitle.textContent  = 'No Active Directives';
        if (carDesc)   carDesc.textContent   = 'Company-wide updates and plant operational notices will appear here once published.';
        if (carDate)   carDate.textContent   = '—';
        if (carBadge)  carBadge.textContent  = 'ANNOUNCEMENT';
        if (carAuthor) carAuthor.textContent = 'Corporate Communications';
      }

      if (!items.length) {
        feed.innerHTML =
          '<div style="padding:32px;opacity:.5;text-align:center;">No announcements at this time.</div>';
        return;
      }

      const typeColor = {
        "All-Hands": "#3498db",
        Department: "#9b59b6",
        Safety: "#e74c3c",
        IT: "#2ecc71",
        HR: "#f39c12",
        ENG: "#137333",
        ITD: "#1a73e8",
        HRA: "#6e4c7c",
        OPS: "#00796b"
      };

      feed.innerHTML = items
        .map((a) => {
          const dept = a.audience_dept || a.announcement_type || "General";
          const color = typeColor[dept] || typeColor[a.announcement_type] || "#3498db";
          const dateStr = ui.date(a.posted_at || a.published_at || a.created_at);
          const author = a.posted_by_name || a.author_name || a.emp_id || "Enterprise Staff";
          const role = a.posted_by_role ? ` • ${a.posted_by_role}` : "";
          const targetDept = a.target_department_display || a.target_department_name || a.target_departments || "";

          return `
          <div class="announcement-card" style="border-left:3px solid ${color};
            background:${color}08;border-radius:8px;padding:16px 20px;margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
              <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                  <span style="padding:2px 8px;border-radius:20px;font-size:10px;font-weight:800;
                    background:${color}20;color:${color};text-transform:uppercase;letter-spacing:.5px;">
                    ${escHtml(dept)}
                  </span>
                  ${a.is_pinned ? '<span style="font-size:10px;font-weight:700;color:#f39c12;">📌 PINNED</span>' : ""}
                </div>
                <div style="font-size:15px;font-weight:700;margin-bottom:6px;">${escHtml(a.title)}</div>
                <div style="font-size:13px;opacity:.7;line-height:1.5;">${escHtml((a.body || a.content || "").substring(0, 240))}${(a.body || a.content || "").length > 240 ? "…" : ""}</div>
              </div>
              <div style="font-size:11px;opacity:.5;white-space:nowrap;">${dateStr}</div>
            </div>
            <div style="margin-top:10px;font-size:11px;opacity:.5;">
              By: ${escHtml(author)}${escHtml(role)}
              ${targetDept ? " · Target: " + escHtml(targetDept) : ""}
            </div>
          </div>`;
        })
        .join("");
    } catch (err) {
      handleApiError(err, "Announcements");
      feed.innerHTML =
        '<div style="padding:24px;color:#e74c3c;">Failed to load announcements.</div>';
    }
  }

  /* ──────────────────────────────────────────────
   * EMPLOYEE DIRECTORY
   * Element: #directory-tbody
   * ────────────────────────────────────────────── */
  async function loadDirectory() {
    // Targets the card grid used in EmployeeDirectory.php
    const grid = document.getElementById("employee-directory-grid");
    if (!grid) return;

    grid.innerHTML =
      '<div style="padding:32px;text-align:center;grid-column:1/-1;"><span class="vp-spinner"></span> Loading directory…</div>';

    try {
      const res = await intranet.directory();
      const rows = res.data;

      if (!rows.length) {
        grid.innerHTML =
          '<div style="padding:32px;text-align:center;opacity:.5;grid-column:1/-1;">Directory is empty.</div>';
        return;
      }

      const statusColor = {
        Active: "#2ecc71",
        "On Leave": "#f39c12",
        Inactive: "#e74c3c",
      };

      grid.innerHTML = rows
        .map((emp) => {
          const sc = statusColor[emp.employment_status] || "#aaa";
          const initials = (emp.full_name || emp.emp_id || "??")
            .split(" ")
            .map((p) => p[0])
            .join("")
            .substring(0, 2)
            .toUpperCase();

          return `
          <div class="intranet-card directory-card" style="padding:1.25rem;transition:transform .15s,box-shadow .15s;cursor:pointer;"
            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,.15)'"
            onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
              <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#3498db,#9b59b6);
                display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;">
                ${initials}
              </div>
              <div style="flex:1;min-width:0;">
                <div style="font-weight:700;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(emp.full_name || emp.emp_id)}</div>
                <div style="font-size:11px;opacity:.5;font-family:monospace;">${escHtml(emp.emp_id)}</div>
              </div>
              <span style="width:8px;height:8px;border-radius:50%;background:${sc};flex-shrink:0;" title="${escHtml(emp.employment_status || "")}"></span>
            </div>
            <div style="font-size:12px;font-weight:600;margin-bottom:4px;">${escHtml(emp.job_title || "—")}</div>
            <div style="font-size:11px;opacity:.6;margin-bottom:8px;">${escHtml(emp.department_name || emp.department_code || "—")}</div>
            <div style="font-size:11px;opacity:.5;display:flex;flex-direction:column;gap:3px;">
              ${emp.email ? `<a href="mailto:${escHtml(emp.email)}" style="color:#3498db;text-decoration:none;">${escHtml(emp.email)}</a>` : ""}
              ${emp.phone ? `<span style="font-family:monospace;">${escHtml(emp.phone)}</span>` : ""}
              ${emp.location || emp.office_location ? `<span>${escHtml(emp.location || emp.office_location)}</span>` : ""}
            </div>
          </div>`;
        })
        .join("");
    } catch (err) {
      handleApiError(err, "Employee Directory");
      grid.innerHTML =
        '<div style="padding:32px;color:#e74c3c;grid-column:1/-1;">Failed to load directory.</div>';
    }
  }

  /* ──────────────────────────────────────────────
   * LEAVE REQUESTS
   * Element: #leaves-tbody
   * ────────────────────────────────────────────── */
  async function loadLeaves() {
    const tbody = document.getElementById("leaves-tbody");
    if (!tbody) return;

    tbody.innerHTML =
      '<tr><td colspan="6" style="text-align:center;padding:28px;"><span class="vp-spinner"></span></td></tr>';

    try {
      const res = await intranet.leaves();
      const rows = res.data;

      if (!rows.length) {
        tbody.innerHTML =
          '<tr><td colspan="6" style="text-align:center;padding:28px;opacity:.5;">No leave requests found.</td></tr>';
        return;
      }

      const statusStyle = {
        Approved: "color:#2ecc71;background:rgba(46,204,113,.12)",
        Pending: "color:#f39c12;background:rgba(243,156,18,.12)",
        Rejected: "color:#e74c3c;background:rgba(231,76,60,.12)",
        Cancelled: "color:#aaa;background:rgba(170,170,170,.1)",
      };

      tbody.innerHTML = rows
        .map((l) => {
          const st = l.status || l.approval_status || "Pending";
          const style = statusStyle[st] || statusStyle["Pending"];
          const days = l.total_days ?? (l.start_date && l.end_date ? Math.max(1, Math.round((new Date(l.end_date) - new Date(l.start_date)) / 86400000) + 1) : "—");
          return `
          <tr class="leave-row">
            <td style="font-family:monospace;font-weight:700;">${escHtml(l.leave_id || l.request_id || "—")}</td>
            <td>${escHtml(l.leave_type)}</td>
            <td>${ui.date(l.start_date)}</td>
            <td>${ui.date(l.end_date)}</td>
            <td style="text-align:center;font-weight:700;">${days}</td>
            <td>
              <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;${style};">
                ${escHtml(st)}
              </span>
            </td>
          </tr>`;
        })
        .join("");
    } catch (err) {
      handleApiError(err, "Leave Requests");
      tbody.innerHTML =
        '<tr><td colspan="6" style="color:#e74c3c;padding:28px;">Failed to load leave requests.</td></tr>';
    }
  }

  /* ──────────────────────────────────────────────
   * LEAVE SUBMIT FORM
   * Element: #leave-submit-form
   * ────────────────────────────────────────────── */
  function initLeaveForm() {
    const form = document.getElementById("leave-submit-form");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const btn = form.querySelector('button[type="submit"]');

      const payload = {
        leave_type: form.querySelector('[name="leave_type"]')?.value,
        start_date: form.querySelector('[name="start_date"]')?.value,
        end_date: form.querySelector('[name="end_date"]')?.value,
        reason: form.querySelector('[name="reason"]')?.value,
      };

      if (!payload.leave_type || !payload.start_date || !payload.end_date) {
        ui.toast("Validation", "Leave type and dates are required.", "warning");
        return;
      }

      try {
        await ui.withLoading(btn, intranet.submitLeave(payload));
        ui.toast(
          "Request Submitted",
          "Your leave request is pending approval.",
          "success",
        );
        form.reset();
        loadLeaves();
      } catch (err) {
        handleApiError(err, "Submit Leave");
      }
    });
  }

  /* ──────────────────────────────────────────────
   * Directory search (client-side)
   * ────────────────────────────────────────────── */
  function initDirectorySearch() {
    // Works with both #directory-search and any search input on the directory page
    const input =
      document.getElementById("directory-search") ||
      document.querySelector('.dir-filter-grid input[type="text"]');
    if (!input) return;
    input.addEventListener("input", () => {
      const q = input.value.toLowerCase();
      document
        .querySelectorAll(".directory-card,.directory-row")
        .forEach((el) => {
          el.style.display = el.textContent.toLowerCase().includes(q)
            ? ""
            : "none";
        });
    });
  }

  /* ──────────────────────────────────────────────
   * Boot
   * ────────────────────────────────────────────── */
  document.addEventListener("DOMContentLoaded", () => {
    loadAnnouncements();
    loadDirectory();
    loadLeaves();
    initLeaveForm();
    initDirectorySearch();
  });
})();
