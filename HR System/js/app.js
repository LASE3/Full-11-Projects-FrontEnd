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
      if (type === "amber") icon = "⚡";

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
            clearanceEl.innerHTML = `<span>🔒</span><span>Level ${cNum} · ${cNum === "4" ? "Top Secret" : cNum === "3" ? "Secret SCADA" : cNum === "2" ? "Confidential" : "General"}</span>`;
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

    // Search and Filter Employees table
    filterEmployees: function () {
      const searchInput = document.getElementById("employee-table-search");
      const deptFilter = document.getElementById("employee-dept-filter");
      const clearanceFilter = document.getElementById(
        "employee-clearance-filter",
      );

      const query = (searchInput ? searchInput.value : "").toLowerCase();
      const selectedDept = deptFilter ? deptFilter.value : "all";
      const selectedClearance = clearanceFilter ? clearanceFilter.value : "all";

      const rows = document.querySelectorAll(".employee-table-body tr");
      rows.forEach((row) => {
        const name = (row.getAttribute("data-name") || "").toLowerCase();
        const id = (row.getAttribute("data-id") || "").toLowerCase();
        const dept = row.getAttribute("data-dept") || "";
        const clearance = row.getAttribute("data-clearance") || "";

        const matchQuery = !query || name.includes(query) || id.includes(query);
        const matchDept = selectedDept === "all" || dept === selectedDept;
        const matchClearance =
          selectedClearance === "all" || clearance === selectedClearance;

        if (matchQuery && matchDept && matchClearance) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
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

      fetch("api/hr_actions.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            self.showToast(
              "Employee Registered",
              data.message || "Record created successfully in MySQL.",
              "success",
            );
            self.closeModal("modal-add-employee");
            form.reset();
            setTimeout(() => {
              window.location.reload();
            }, 800);
          } else {
            self.showToast("Registration Error", data.message, "alert");
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
            setTimeout(() => {
              window.location.reload();
            }, 600);
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
            setTimeout(() => {
              window.location.reload();
            }, 800);
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
            setTimeout(() => {
              window.location.reload();
            }, 600);
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
            setTimeout(() => {
              window.location.reload();
            }, 600);
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
            setTimeout(() => window.location.reload(), 600);
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
            setTimeout(() => window.location.reload(), 600);
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
                    setTimeout(() => window.location.reload(), 600);
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
            setTimeout(() => window.location.reload(), 600);
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
            setTimeout(() => window.location.reload(), 800);
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
