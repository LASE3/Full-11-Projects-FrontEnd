/**
 * VOSTOKPRIBOR HR & Human Capital Management System (System 06)
 * Subdomain: hr.vostokpribor.local
 * Database-integrated asynchronous application logic
 */

(function () {
  "use strict";

  let currentDossierEmpId = null;

  const hrApp = {
    // Toast notification manager
    showToast: function (title, message, type = "info") {
      const container = document.getElementById("toast-container");
      if (!container) return;

      const toast = document.createElement("div");
      toast.className = `toast-item toast-${type}`;

      let icon = "ℹ️";
      if (type === "success") icon = "✓";
      if (type === "alert" || type === "error") icon = "⚠";
      if (type === "amber") icon = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`;

      toast.innerHTML = `
        <div class="toast-icon">${icon}</div>
        <div class="toast-content">
          <div class="toast-title">${title}</div>
          <div class="toast-desc">${message}</div>
        </div>
      `;

      container.appendChild(toast);

      setTimeout(() => {
        toast.classList.add("fade-out");
        setTimeout(() => toast.remove(), 400);
      }, 3500);
    },

    openModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add("active");
        const input = modal.querySelector("input:not([type=hidden]), select");
        if (input) input.focus();
      }
    },

    closeModal: function (modalId) {
      const modal = document.getElementById(modalId);
      if (modal) modal.classList.remove("active");
    },

    // Inspect Employee Details Dossier from Database via API
    inspectEmployee: function (empId) {
      currentDossierEmpId = empId;
      const self = this;

      fetch(
        "api/hr_actions.php?action=get_employee_dossier&emp_id=" +
          encodeURIComponent(empId),
      )
        .then((res) => res.json())
        .then((data) => {
          if (!data.success || !data.employee) {
            self.showToast(
              "Lookup Error",
              data.message || "Could not load employee dossier.",
              "alert",
            );
            return;
          }

          const emp = data.employee;
          const nameEl = document.getElementById("drawer-emp-name");
          const titleEl = document.getElementById("drawer-emp-title");
          const idEl = document.getElementById("drawer-emp-id");
          const deptEl = document.getElementById("drawer-emp-dept");
          const clearanceEl = document.getElementById("drawer-emp-clearance");
          const statusEl = document.getElementById("drawer-emp-status");
          const avatarPlaceholder = document.getElementById(
            "drawer-emp-avatar-placeholder",
          );
          const emailEl = document.getElementById("drawer-emp-email");
          const usernameEl = document.getElementById("drawer-emp-username");
          const hireEl = document.getElementById("drawer-emp-hire");
          const supEl = document.getElementById("drawer-emp-sup");
          const roleEl = document.getElementById("drawer-emp-role");
          const accStatusEl = document.getElementById("drawer-emp-accstatus");

          if (nameEl) nameEl.textContent = emp.full_name;
          if (titleEl) titleEl.textContent = emp.job_title;
          if (idEl) idEl.textContent = emp.emp_id;
          if (deptEl)
            deptEl.textContent =
              (emp.dept_name || emp.department_code) +
              " (" +
              emp.department_code +
              ")";
          if (emailEl) emailEl.textContent = emp.email || "N/A";
          if (usernameEl) usernameEl.textContent = emp.username || emp.emp_id;
          if (hireEl) hireEl.textContent = emp.hire_date || "Standard Roster";
          if (supEl)
            supEl.textContent = emp.manager_name
              ? `${emp.manager_name} (${emp.manager_emp_id})`
              : "Executive Board / Direct";
          if (roleEl)
            roleEl.textContent = emp.role_name || "Standard Personnel";
          if (accStatusEl)
            accStatusEl.textContent =
              emp.account_status || emp.employment_status;

          if (avatarPlaceholder) {
            const parts = emp.full_name.trim().split(" ");
            const init =
              (parts[0] ? parts[0][0] : "") + (parts[1] ? parts[1][0] : "");
            avatarPlaceholder.textContent = init.toUpperCase();
          }

          if (clearanceEl) {
            const cNum = (emp.clearance_level || "L1").replace("L", "");
            clearanceEl.className = `clearance-badge clearance-l${cNum}`;
            clearanceEl.innerHTML = `<span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span><span>Level ${cNum} · ${cNum === "4" ? "Top Secret" : cNum === "3" ? "Secret SCADA" : cNum === "2" ? "Confidential" : "General"}</span>`;
          }

          if (statusEl) {
            let statusClass = "status-active";
            if (
              emp.employment_status === "Suspended" ||
              emp.employment_status === "Terminated"
            )
              statusClass = "status-offboarding";
            if (emp.employment_status === "OnLeave")
              statusClass = "status-leave";
            statusEl.className = `status-pill ${statusClass}`;
            statusEl.textContent = emp.employment_status;
          }

          self.openModal("modal-employee-detail");
        })
        .catch((err) => {
          self.showToast("Network Error", err.message, "alert");
        });
    },

    _filterTimeout: null,
    filterEmployees: function () {
      if (this._filterTimeout) clearTimeout(this._filterTimeout);
      this._filterTimeout = setTimeout(() => {
        this.fetchEmployees();
      }, 300);
    },

    fetchEmployees: async function () {
      const tbody = document.querySelector(".employee-table-body");
      if (!tbody) return;
      const searchInput = document.getElementById("employee-table-search");
      const deptFilter = document.getElementById("employee-dept-filter");
      const clearanceFilter = document.getElementById("employee-clearance-filter");

      const query = (searchInput ? searchInput.value : "").trim();
      const selectedDept = deptFilter ? deptFilter.value : "all";
      const selectedClearance = clearanceFilter ? clearanceFilter.value : "all";

      const url = `api/employees.php?q=${encodeURIComponent(query)}&page=1&per_page=100`;
      try {
        const fetchFn = window.vpFetch || (async (u) => {
          const r = await fetch(u);
          return await r.json();
        });
        const data = await fetchFn(url);
        const rows = (data && data.rows) ? data.rows : ((data && data.data) ? data.data : []);
        this.renderEmployeesTable(rows, selectedDept, selectedClearance);
      } catch (err) {
        console.error("Failed to fetch employees:", err);
      }
    },

    renderEmployeesTable: function (employees, selectedDept, selectedClearance) {
      const tbody = document.querySelector(".employee-table-body");
      if (!tbody) return;

      const esc = (s) => {
        if (s == null) return "";
        return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
      };

      const filtered = (employees || []).filter((emp) => {
        const cNum = String(emp.clearance_level || "").replace(/^L/i, "");
        const matchDept = selectedDept === "all" || emp.department_code === selectedDept;
        const matchClearance = selectedClearance === "all" || cNum === selectedClearance;
        return matchDept && matchClearance;
      });

      if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:#94a3b8;">No matching employee records found</td></tr>`;
        return;
      }

      tbody.innerHTML = filtered.map((emp) => {
        const cNum = String(emp.clearance_level || "1").replace(/^L/i, "");
        const nameParts = (emp.full_name || "").trim().split(/\s+/);
        const rowInitials = (nameParts[0] ? nameParts[0][0] : "") + (nameParts[1] ? nameParts[1][0] : "");
        const initials = (rowInitials || "VP").toUpperCase();

        let statusPillClass = "status-active";
        if (emp.employment_status === "Suspended") statusPillClass = "status-offboarding";
        if (emp.employment_status === "OnLeave") statusPillClass = "status-leave";
        if (emp.employment_status === "Terminated") statusPillClass = "status-offboarding";

        const clrLabels = { "4": "Top Secret", "3": "Secret SCADA", "2": "Confidential", "1": "General" };
        const clrLabel = clrLabels[cNum] || "General";

        return `
          <tr class="employee-row"
            data-id="${esc(emp.emp_id)}"
            data-name="${esc(emp.full_name)}"
            data-dept="${esc(emp.department_code)}"
            data-clearance="${esc(cNum)}"
            onclick="window.hrApp.inspectEmployee('${esc(emp.emp_id)}')">
            <td>
              <span class="hr-mono-bold-plum">${esc(emp.emp_id)}</span>
            </td>
            <td>
              <div class="hr-flex-gap-65">
                <div class="hr-avatar-32">${esc(initials)}</div>
                <div>
                  <div class="hr-font-semibold-navy-13">${esc(emp.full_name)}</div>
                  <div class="hr-text-muted-11">${esc(emp.email || "")}</div>
                </div>
              </div>
            </td>
            <td>
              <span class="hr-font-medium-125">${esc(emp.dept_name || emp.department_code)}</span>
            </td>
            <td>
              <span class="hr-text-secondary-125">${esc(emp.job_title)}</span>
            </td>
            <td>
              <span class="clearance-badge clearance-l${esc(cNum)}">
                <span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                <span>Level ${esc(cNum)} · ${esc(clrLabel)}</span>
              </span>
            </td>
            <td>
              <span class="status-pill ${statusPillClass}">${esc(emp.employment_status || "Active")}</span>
            </td>
            <td class="hr-text-right" onclick="event.stopPropagation()">
              <div class="hr-inline-gap-xs">
                <button class="btn btn-outline btn-sm" onclick="window.hrApp.inspectEmployee('${esc(emp.emp_id)}')">
                  Dossier
                </button>
                <button class="btn btn-outline btn-sm hr-btn-border-red-40"
                  title="Remove or terminate employee record"
                  onclick="window.hrApp.deleteEmployee('${esc(emp.emp_id)}', '${esc(emp.full_name)}')">
                  🗑
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join("");
    },

    // Handle Add New Employee Form Submit
    handleRegisterEmployee: function (e, form) {
      e.preventDefault();
      const self = this;
      const btn = document.getElementById("btn-submit-employee");
      if (btn) {
        btn.disabled = true;
        btn.textContent = "Registering in MySQL...";
      }

      const formData = new FormData(form);
      formData.append("action", "add_employee");

      const fetchFn = window.vpFetch || (async (u, opt) => {
        const r = await fetch(u, opt);
        return await r.json();
      });

      fetchFn("api/hr_actions.php", {
        method: "POST",
        body: formData,
      })
        .then((data) => {
          if (data && data.success) {
            self.showToast(
              "Employee Registered",
              data.message || "Record created successfully in MySQL.",
              "success",
            );
            self.closeModal("modal-add-employee");
            form.reset();
            self.fetchEmployees();
          } else {
            self.showToast("Registration Error", (data && data.message) || "Failed", "alert");
            if (btn) {
              btn.disabled = false;
              btn.textContent = "Register & Initialize Onboarding →";
            }
          }
        })
        .catch((err) => {
          self.showToast("Connection Error", err.message, "alert");
          if (btn) {
            btn.disabled = false;
            btn.textContent = "Register & Initialize Onboarding →";
          }
        });

      return false;
    },


    // Advance Onboarding Step
    advanceOnboarding: function (empId, step, status) {
      const self = this;
      const formData = new FormData();
      formData.append("action", "advance_onboarding");
      formData.append("emp_id", empId);
      formData.append("step", step);
      formData.append("status", status);

      fetch("api/hr_actions.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            self.showToast(
              "Onboarding Step Updated",
              `${step} marked as ${status} in MySQL.`,
              "success",
            );
            document.querySelectorAll(".step-item").forEach((el) => {
              if (el.textContent.includes(step)) {
                const isDone = status === "Completed";
                el.className = "step-item " + (isDone ? "completed" : "pending");
                const badge = el.querySelector(".step-badge-status");
                if (badge) {
                  badge.className = "step-badge-status " + (isDone ? "status-completed" : "status-pending");
                  badge.textContent = status.toUpperCase();
                }
                const btn = el.querySelector("button");
                if (btn) btn.textContent = isDone ? "Reopen Step" : "Mark Completed ✓";
              }
            });
          } else {
            self.showToast("Update Error", data.message, "alert");
          }
        })
        .catch((err) => {
          self.showToast("Error", err.message, "alert");
        });
    },

    // Handle Initiate Offboarding
    handleInitiateOffboarding: function (e, form) {
      e.preventDefault();
      const self = this;
      const formData = new FormData(form);
      formData.append("action", "initiate_offboarding");

      fetch("api/hr_actions.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            self.showToast("Offboarding Initiated", data.message, "amber");
            self.closeModal("modal-initiate-offboarding");
            self.fetchEmployees();
          } else {
            self.showToast("Offboarding Error", data.message, "alert");
          }
        })
        .catch((err) => {
          self.showToast("Error", err.message, "alert");
        });

      return false;
    },

    // Advance Offboarding Step
    advanceOffboarding: function (empId, step, status) {
      const self = this;
      const formData = new FormData();
      formData.append("action", "advance_offboarding");
      formData.append("emp_id", empId);
      formData.append("step", step);
      formData.append("status", status);

      fetch("api/hr_actions.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            self.showToast(
              "Offboarding Step Updated",
              `${step} status updated to ${status}.`,
              "success",
            );
            document.querySelectorAll(".offboarding-case-card").forEach((card) => {
              if (card.textContent.includes(empId)) {
                card.querySelectorAll("button").forEach((btn) => {
                  if (btn.getAttribute("onclick") && btn.getAttribute("onclick").includes(step)) {
                    const isDone = status === "Completed";
                    btn.textContent = isDone ? "✓ Done" : "Complete";
                    const parent = btn.parentElement;
                    const meta = parent ? parent.querySelector(".hr-meta-muted-10") : null;
                    if (meta) meta.textContent = status;
                  }
                });
              }
            });
          } else {
            self.showToast("Error", data.message, "alert");
          }
        })
        .catch((err) => {
          self.showToast("Error", err.message, "alert");
        });
    },

    // Process Leave Request (Approve / Reject)
    processLeave: function (leaveId, decision) {
      const self = this;
      const formData = new FormData();
      formData.append("action", "process_leave");
      formData.append("leave_id", leaveId);
      formData.append("decision", decision);

      fetch("api/hr_actions.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            self.showToast(
              `Leave ${decision}`,
              `Request #${leaveId} successfully ${decision.toLowerCase()}.`,
              decision === "Approved" ? "success" : "alert",
            );
            const badge = document.getElementById(
              `leave-status-badge-${leaveId}`,
            );
            if (badge) {
              badge.className =
                decision === "Approved"
                  ? "status-pill status-active"
                  : "status-pill status-offboarding";
              badge.textContent = decision;
            }
          } else {
            self.showToast("Action Failed", data.message, "alert");
          }
        })
        .catch((err) => {
          self.showToast("Error", err.message, "alert");
        });
    },

    // Handle Create Leave Application Form Submit
    handleCreateLeave: function (e, form) {
      e.preventDefault();
      const self = this;
      const formData = new FormData(form);
      formData.append("action", "create_leave");

      fetch("api/hr_actions.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            self.showToast(
              "Leave Filed",
              "Application recorded in MySQL.",
              "success",
            );
            self.closeModal("modal-create-leave");
            form.reset();
          } else {
            self.showToast("Filing Error", data.message, "alert");
          }
        })
        .catch((err) => self.showToast("Error", err.message, "alert"));

      return false;
    },

    // Handle Create Training Record Form Submit
    handleCreateTraining: function (e, form) {
      e.preventDefault();
      const self = this;
      const formData = new FormData(form);
      formData.append("action", "create_training");

      fetch("api/hr_actions.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            self.showToast(
              "Training Logged",
              "Accreditation saved in MySQL.",
              "success",
            );
            self.closeModal("modal-create-training");
            form.reset();
          } else {
            self.showToast("Error", data.message, "alert");
          }
        })
        .catch((err) => self.showToast("Error", err.message, "alert"));

      return false;
    },

    // Trigger offboarding directly from Dossier modal
    triggerOffboardingFromDossier: function () {
      if (!currentDossierEmpId) return;
      if (
        confirm(
          `Initiate offboarding & credential revocation for ${currentDossierEmpId}?`,
        )
      ) {
        this.closeModal("modal-employee-detail");
        const formData = new FormData();
        formData.append("action", "initiate_offboarding");
        formData.append("emp_id", currentDossierEmpId);
        formData.append("reason", "Executive Separation Action");

        const self = this;
        fetch("api/hr_actions.php", {
          method: "POST",
          body: formData,
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.success) {
              self.showToast("Offboarding Started", data.message, "amber");
              setTimeout(() => {
                window.location.href = "Offboarding.php";
              }, 800);
            } else {
              self.showToast("Error", data.message, "alert");
            }
          });
      }
    },

    // Prompt to modify clearance tier
    elevateClearancePrompt: function () {
      if (!currentDossierEmpId) return;
      const newClr = prompt(
        `Enter new clearance tier for ${currentDossierEmpId} (L1, L2, L3, L4):`,
        "L3",
      );
      if (newClr && ["L1", "L2", "L3", "L4"].includes(newClr.toUpperCase())) {
        const self = this;
        // Fetch current details then update
        fetch(
          "api/hr_actions.php?action=get_employee_dossier&emp_id=" +
            encodeURIComponent(currentDossierEmpId),
        )
          .then((res) => res.json())
          .then((data) => {
            if (data.success && data.employee) {
              const emp = data.employee;
              const formData = new FormData();
              formData.append("action", "update_employee");
              formData.append("emp_id", currentDossierEmpId);
              formData.append("full_name", emp.full_name);
              formData.append("job_title", emp.job_title);
              formData.append("department_code", emp.department_code);
              formData.append("clearance_level", newClr.toUpperCase());
              formData.append("email", emp.email);
              formData.append("employment_status", emp.employment_status);

              fetch("api/hr_actions.php", { method: "POST", body: formData })
                .then((r) => r.json())
                .then((upd) => {
                  if (upd.success) {
                    self.showToast(
                      "Clearance Updated",
                      `Clearance level changed to ${newClr.toUpperCase()}.`,
                      "success",
                    );
                    self.closeModal("modal-employee-detail");
                    self.fetchEmployees();
                  } else {
                    self.showToast("Update Failed", upd.message, "alert");
                  }
                });
            }
          });
      }
    },

    // Finalize offboarding and set status to Terminated
    completeOffboarding: function (empId) {
      if (
        !confirm(
          `Are you sure you want to finalize offboarding for ${empId}? This will complete all revocation steps, lock user accounts, and set employee status to Terminated in MySQL.`,
        )
      ) {
        return;
      }
      const self = this;
      const formData = new FormData();
      formData.append("action", "complete_offboarding");
      formData.append("emp_id", empId);

      fetch("api/hr_actions.php", { method: "POST", body: formData })
        .then((r) => r.json())
        .then((res) => {
          if (res.success) {
            self.showToast("Offboarding Finalized", res.message, "success");
            self.fetchEmployees();
          } else {
            self.showToast("Operation Failed", res.message, "alert");
          }
        });
    },

    // Permanently delete or terminate employee record from database
    deleteEmployee: function (empId, empName) {
      const promptText = `CONFIRMATION: Are you sure you want to remove employee ${empName || empId} (${empId}) from the database?\n\nThis will remove their system access and clean up associated records, or mark them permanently Terminated if historical logs exist.`;
      if (!confirm(promptText)) {
        return;
      }
      const self = this;
      const formData = new FormData();
      formData.append("action", "delete_employee");
      formData.append("emp_id", empId);

      fetch("api/hr_actions.php", { method: "POST", body: formData })
        .then((r) => r.json())
        .then((res) => {
          if (res.success) {
            self.showToast("Employee Removed", res.message, "success");
            const tr = document.querySelector(`tr[data-id="${empId}"]`);
            if (tr) tr.remove();
            self.fetchEmployees();
          } else {
            self.showToast("Removal Failed", res.message, "alert");
          }
        });
    },

    // Enroll existing employee into onboarding pipeline
    enrollOnboarding: function (empId) {
      if (!empId) return;
      const self = this;
      const formData = new FormData();
      formData.append("action", "enroll_onboarding");
      formData.append("emp_id", empId);

      fetch("api/hr_actions.php", { method: "POST", body: formData })
        .then((r) => r.json())
        .then((res) => {
          if (res.success) {
            self.showToast("Candidate Enrolled", res.message, "success");
            setTimeout(() => {
              window.location.href = `OnboardingTracker.php?emp_id=${encodeURIComponent(empId)}`;
            }, 600);
          } else {
            self.showToast("Enrollment Failed", res.message, "alert");
          }
        });
    },

    init: function () {
      // Auto-highlight active sidebar item
      const currentPath = window.location.pathname.toLowerCase();
      const sidebarLinks = document.querySelectorAll(".sidebar-nav-item");

      sidebarLinks.forEach((link) => {
        const href = (link.getAttribute("href") || "").toLowerCase();
        if (
          href &&
          (currentPath.endsWith(href) ||
            (currentPath.endsWith("/") && href === "dashboard.php"))
        ) {
          link.classList.add("active");
        } else if (href && currentPath.includes(href.replace(".php", ""))) {
          link.classList.add("active");
        }
      });

      // Omni Search Enter Key
      const omniSearch = document.getElementById("global-omni-search");
      if (omniSearch) {
        omniSearch.addEventListener("keydown", (e) => {
          if (e.key === "Enter") {
            const val = omniSearch.value.trim();
            if (val) {
              const searchInput = document.getElementById(
                "employee-table-search",
              );
              if (searchInput) {
                searchInput.value = val;
                hrApp.filterEmployees();
              } else {
                window.location.href = "EmployeeRecords.php";
              }
            }
          }
        });
      }

      // Keyboard Shortcut Ctrl+K
      document.addEventListener("keydown", (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === "k") {
          e.preventDefault();
          if (omniSearch) {
            omniSearch.focus();
            omniSearch.select();
          }
        }
      });

      // Initialize responsive multi-device layout controls
      this.initResponsiveLayout();
    },

    initResponsiveLayout: function () {
      const brandSection =
        document.querySelector(".brand-section") ||
        document.querySelector(".top-nav__content");
      let toggleBtn = document.getElementById("hr-sidebar-toggle");
      if (!toggleBtn && brandSection) {
        toggleBtn = document.createElement("button");
        toggleBtn.id = "hr-sidebar-toggle";
        toggleBtn.className = "mobile-nav-toggle";
        toggleBtn.setAttribute("aria-label", "Toggle Navigation Menu");
        toggleBtn.innerHTML = "☰";
        brandSection.insertBefore(toggleBtn, brandSection.firstChild);
      }

      let backdrop = document.querySelector(".sidebar-backdrop");
      if (!backdrop) {
        backdrop = document.createElement("div");
        backdrop.className = "sidebar-backdrop";
        document.body.appendChild(backdrop);
      }

      if (toggleBtn) {
        toggleBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          document.body.classList.toggle("sidebar-open");
          toggleBtn.innerHTML = document.body.classList.contains("sidebar-open")
            ? "✕"
            : "☰";
        });
      }

      backdrop.addEventListener("click", () => {
        document.body.classList.remove("sidebar-open");
        if (toggleBtn) toggleBtn.innerHTML = "☰";
      });

      document
        .querySelectorAll(".sidebar-nav-item, .sidebar a")
        .forEach((link) => {
          link.addEventListener("click", () => {
            if (window.innerWidth <= 1024) {
              document.body.classList.remove("sidebar-open");
              if (toggleBtn) toggleBtn.innerHTML = "☰";
            }
          });
        });

      document
        .querySelectorAll("table.employee-table, table.data-table, table")
        .forEach((table) => {
          if (
            !table.parentElement.classList.contains("table-responsive") &&
            !table.parentElement.classList.contains("employee-table-container")
          ) {
            const wrapper = document.createElement("div");
            wrapper.className = "table-responsive";
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
          }
        });

      window.addEventListener("resize", () => {
        if (
          window.innerWidth > 1024 &&
          document.body.classList.contains("sidebar-open")
        ) {
          document.body.classList.remove("sidebar-open");
          if (toggleBtn) toggleBtn.innerHTML = "☰";
        }
      });

      if (new URLSearchParams(window.location.search).get("openAdd") === "1") {
        hrApp.openModal("modal-add-employee");
      }
    },
  };

  window.hrApp = hrApp;

  window.switchOnboardTab = function (tab) {
    const formNew = document.getElementById("form-add-onboard-employee");
    const secExisting = document.getElementById("section-enroll-existing");
    const btnNew = document.getElementById("tab-btn-new-hire");
    const btnExist = document.getElementById("tab-btn-enroll-existing");

    if (!formNew || !secExisting || !btnNew || !btnExist) return;

    if (tab === "new") {
      formNew.classList.remove("hidden");
      secExisting.classList.add("hidden");
      btnNew.classList.add("hr-tab-active");
      btnNew.classList.remove("hr-tab-inactive");
      btnExist.classList.add("hr-tab-inactive");
      btnExist.classList.remove("hr-tab-active");
    } else {
      formNew.classList.add("hidden");
      secExisting.classList.remove("hidden");
      btnExist.classList.add("hr-tab-active");
      btnExist.classList.remove("hr-tab-inactive");
      btnNew.classList.add("hr-tab-inactive");
      btnNew.classList.remove("hr-tab-active");
    }
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => hrApp.init());
  } else {
    hrApp.init();
  }
})();
