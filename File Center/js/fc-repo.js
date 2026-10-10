/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub
 * Document Repository, Filtering, Inspector Drawer & Full CRUD Operations
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    initSearchAndFilter();
    initFolderTree();
    initDocumentDrawer();
    initEditDocumentModal();
    initDeleteDocumentModal();
  });

  /**
   * Search and Classification Filter
   */
  function initSearchAndFilter() {
    const searchInput = document.getElementById("repo-search-input");
    const filterPills = document.querySelectorAll(".filter-pill");

    let currentClass = "all";
    let currentFolder = "all";

    function filterRows() {
      const rows = document.querySelectorAll(".repo-doc-row");
      const query = searchInput ? searchInput.value.toLowerCase().trim() : "";

      rows.forEach((row) => {
        const docId = (row.getAttribute("data-doc-id") || "").toLowerCase();
        const docName = (row.getAttribute("data-doc-name") || "").toLowerCase();
        const docClass = (row.getAttribute("data-classification") || "").toLowerCase();
        const docFolder = (row.getAttribute("data-folder") || "").toLowerCase();
        const docProject = (row.getAttribute("data-project") || "").toLowerCase();
        const docCustodian = (row.getAttribute("data-custodian") || "").toLowerCase();
        const rowText = row.textContent.toLowerCase();

        const matchesQuery =
          !query ||
          rowText.includes(query) ||
          docId.includes(query) ||
          docName.includes(query) ||
          docProject.includes(query) ||
          docCustodian.includes(query);

        const matchesClass = currentClass === "all" || docClass === currentClass;
        const matchesFolder = currentFolder === "all" || docFolder === currentFolder;

        if (matchesQuery && matchesClass && matchesFolder) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });

      // Update visible count
      const visibleCount = Array.from(rows).filter((r) => r.style.display !== "none").length;
      const countDisplay = document.getElementById("visible-docs-count");
      if (countDisplay) {
        countDisplay.textContent = visibleCount;
      }
    }

    if (searchInput) {
      searchInput.addEventListener("input", filterRows);
    }

    filterPills.forEach((pill) => {
      pill.addEventListener("click", function () {
        filterPills.forEach((p) => p.classList.remove("active"));
        this.classList.add("active");
        currentClass = this.getAttribute("data-class-filter") || "all";
        filterRows();
      });
    });

    window.setFolderFilter = function (folderSlug) {
      currentFolder = folderSlug;
      filterRows();
    };

    window.triggerRepoFilter = filterRows;
  }

  /**
   * Folder Tree Navigation
   */
  function initFolderTree() {
    const folderItems = document.querySelectorAll(".folder-item");
    folderItems.forEach((item) => {
      item.addEventListener("click", function () {
        folderItems.forEach((f) => f.classList.remove("active"));
        this.classList.add("active");
        const slug = this.getAttribute("data-folder-slug") || "all";
        window.setFolderFilter(slug);

        const nameSpan = this.querySelector("span:nth-child(2)");
        const folderName = nameSpan ? nameSpan.textContent : slug;
        if (window.showToast) {
          window.showToast(
            "PARTITION SELECTED",
            `Browsing repository partition: ${folderName}`,
            "info",
            "folder_open"
          );
        }
      });
    });
  }

  /**
   * Slide-Over Document Inspection Drawer
   */
  function initDocumentDrawer() {
    const backdrop = document.getElementById("doc-drawer-backdrop");
    const closeBtn = document.getElementById("btn-close-drawer");

    if (!backdrop) return;

    function closeDrawer() {
      backdrop.style.display = "none";
    }

    if (closeBtn) closeBtn.addEventListener("click", closeDrawer);
    backdrop.addEventListener("click", (e) => {
      if (e.target === backdrop) closeDrawer();
    });

    // Delegate row click or inspect button
    document.addEventListener("click", (e) => {
      const inspectBtn = e.target.closest(".btn-inspect-doc");
      if (inspectBtn) {
        e.stopPropagation();
        const row = inspectBtn.closest(".repo-doc-row");
        if (row) openDrawerWithData(row);
        return;
      }

      // If clicked row directly (not a button)
      const row = e.target.closest(".repo-doc-row");
      if (row && !e.target.closest("button") && !e.target.closest("a")) {
        openDrawerWithData(row);
      }
    });

    function openDrawerWithData(row) {
      const docId = row.getAttribute("data-doc-id");
      const docName = row.getAttribute("data-doc-name");
      const docClass = row.getAttribute("data-classification") || "internal";
      const docProj = row.getAttribute("data-project") || "N/A";
      const docCust = row.getAttribute("data-custodian") || "Farida Iskakova (EMP-1019)";
      const docSize = row.getAttribute("data-size") || "2.0 MB";
      const docDate = row.getAttribute("data-date") || "2026-09-11";
      const docHash = row.getAttribute("data-hash") || "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855";
      const docSys = row.getAttribute("data-system") || "File Center";
      const docStatus = row.getAttribute("data-status") || "Approved";
      const docDesc = row.getAttribute("data-description") || "Standard archival record.";

      document.getElementById("drawer-doc-id").textContent = docId;
      document.getElementById("drawer-doc-name").textContent = docName;
      document.getElementById("drawer-doc-size").textContent = docSize;
      document.getElementById("drawer-doc-date").textContent = docDate;
      document.getElementById("drawer-doc-project").textContent = docProj;
      document.getElementById("drawer-doc-custodian").textContent = docCust;
      document.getElementById("drawer-doc-hash").textContent = docHash;
      document.getElementById("drawer-doc-system").textContent = docSys;
      document.getElementById("drawer-doc-status").textContent = docStatus;
      const descElem = document.getElementById("drawer-doc-desc");
      if (descElem) descElem.textContent = docDesc;

      // Classification badge
      const classBadge = document.getElementById("drawer-class-badge");
      if (classBadge) {
        classBadge.className = "vk-tag";
        classBadge.textContent = docClass.replace("-", " ").toUpperCase();
        if (docClass === "highly-confidential") {
          classBadge.classList.add("vk-tag-highly-confidential");
        } else if (docClass === "confidential") {
          classBadge.classList.add("vk-tag-confidential");
        } else if (docClass === "internal") {
          classBadge.classList.add("vk-tag-internal");
        } else {
          classBadge.classList.add("vk-tag-public");
        }
      }

      // Copy Hash Button
      const copyHashBtn = document.getElementById("btn-copy-drawer-hash");
      if (copyHashBtn) {
        copyHashBtn.onclick = () => {
          if (window.copyToClipboard) {
            window.copyToClipboard(docHash, "SHA-256 Checksum");
          }
        };
      }

      // Decrypt & Download
      const downloadBtn = document.getElementById("btn-drawer-download");
      if (downloadBtn) {
        downloadBtn.onclick = () => {
          if (window.showToast) {
            window.showToast(
              "STREAMING VAULT RECORD",
              `Authorizing and downloading ${docName}...`,
              "info",
              "download"
            );
          }
          // Trigger actual file download
          window.location.href = "api/download.php?doc_id=" + encodeURIComponent(docId);
        };
      }

      // Drawer Edit Shortcut
      const drawerEditBtn = document.getElementById("btn-drawer-edit");
      if (drawerEditBtn) {
        drawerEditBtn.onclick = () => {
          closeDrawer();
          window.openEditModal(row);
        };
      }

      // Drawer Delete Shortcut
      const drawerDeleteBtn = document.getElementById("btn-drawer-delete");
      if (drawerDeleteBtn) {
        drawerDeleteBtn.onclick = () => {
          closeDrawer();
          window.openDeleteModal(docId, docName, row);
        };
      }

      backdrop.style.display = "flex";
    }

    window.openInspectionDrawer = openDrawerWithData;
  }

  /**
   * Edit Document Modal & Database Update
   */
  function initEditDocumentModal() {
    const modal = document.getElementById("edit-doc-modal");
    const form = document.getElementById("edit-doc-form");
    const btnClose = document.getElementById("btn-close-edit-modal");
    const btnCancel = document.getElementById("btn-cancel-edit");

    if (!modal || !form) return;

    let targetRow = null;

    function closeModal() {
      modal.style.display = "none";
      targetRow = null;
    }

    if (btnClose) btnClose.addEventListener("click", closeModal);
    if (btnCancel) btnCancel.addEventListener("click", closeModal);
    modal.addEventListener("click", (e) => {
      if (e.target === modal) closeModal();
    });

    window.openEditModal = function (row) {
      targetRow = row;
      const docId = row.getAttribute("data-doc-id");
      const docName = row.getAttribute("data-doc-name");
      const docDesc = row.getAttribute("data-description") || "";
      const docClass = row.getAttribute("data-classification") || "internal";
      const docFolder = row.getAttribute("data-folder") || "projects";
      const docDept = row.getAttribute("data-dept") || "ENG";
      const docStatus = row.getAttribute("data-status") || "Approved";
      const docProj = row.getAttribute("data-project") || "";
      const docCustRef = row.getAttribute("data-customer") || "";
      const docRet = row.getAttribute("data-retention") || "7y";

      document.getElementById("edit-doc-id").value = docId;
      document.getElementById("edit-modal-doc-id-badge").textContent = docId;
      document.getElementById("edit-file-name").value = docName;
      document.getElementById("edit-description").value = docDesc;
      document.getElementById("edit-classification").value = docClass;
      document.getElementById("edit-folder").value = docFolder;
      document.getElementById("edit-department").value = docDept;
      document.getElementById("edit-status").value = docStatus;
      document.getElementById("edit-project-ref").value = docProj;
      document.getElementById("edit-customer-ref").value = docCustRef;
      document.getElementById("edit-retention").value = docRet;

      modal.style.display = "flex";
    };

    // Table edit button click
    document.addEventListener("click", (e) => {
      const editBtn = e.target.closest(".btn-edit-doc");
      if (editBtn) {
        e.stopPropagation();
        const row = editBtn.closest(".repo-doc-row");
        if (row) window.openEditModal(row);
      }
    });

    // Submit Edit Form -> Post to Database API
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const docId = document.getElementById("edit-doc-id").value;
      const fileName = document.getElementById("edit-file-name").value.trim();
      const description = document.getElementById("edit-description").value.trim();
      const classification = document.getElementById("edit-classification").value;
      const folder = document.getElementById("edit-folder").value;
      const department = document.getElementById("edit-department").value;
      const status = document.getElementById("edit-status").value;
      const projectRef = document.getElementById("edit-project-ref").value.trim();
      const customerRef = document.getElementById("edit-customer-ref").value.trim();
      const retention = document.getElementById("edit-retention").value;

      const submitBtn = form.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Updating Database...';

      try {
        const response = await fetch("api/documents.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            action: "update",
            doc_id: docId,
            file_name: fileName,
            description: description,
            classification: classification,
            folder: folder,
            department: department,
            status: status,
            project_ref: projectRef,
            customer_ref: customerRef,
            retention_period: retention,
          }),
        });

        const res = await response.json();

        if (res.success) {
          // Update DOM row if present
          if (targetRow) {
            targetRow.setAttribute("data-doc-name", fileName);
            targetRow.setAttribute("data-description", description);
            targetRow.setAttribute("data-classification", classification);
            targetRow.setAttribute("data-folder", folder);
            targetRow.setAttribute("data-dept", department);
            targetRow.setAttribute("data-status", status);
            targetRow.setAttribute("data-project", projectRef);
            targetRow.setAttribute("data-customer", customerRef);
            targetRow.setAttribute("data-retention", retention);

            // Update visible columns
            const nameDiv = targetRow.querySelector(".fc-text-primary-bold");
            if (nameDiv) nameDiv.textContent = fileName;
            const descDiv = targetRow.querySelector(".fc-text-muted-11");
            if (descDiv) descDiv.textContent = description;

            // Classification badge
            const classBadge = targetRow.querySelector(".vk-tag[class*='vk-tag-']");
            if (classBadge) {
              classBadge.className = `vk-tag vk-tag-${classification}`;
              classBadge.textContent = classification.replace("-", " ").toUpperCase();
            }

            // Status badge
            const statusBadge = targetRow.querySelector(".vk-status-badge");
            if (statusBadge) {
              statusBadge.textContent = status;
              const statusSlug = status.toLowerCase().replace(" ", "-");
              statusBadge.className = `vk-status-badge status-${statusSlug}`;
            }
          }

          if (window.showToast) {
            window.showToast("DATABASE UPDATED", `Document ${docId} metadata updated in database.`, "success", "check_circle");
          }

          closeModal();
          refreshStats();
        } else {
          alert("Update error: " + (res.message || "Failed to update"));
        }
      } catch (err) {
        alert("Network or database error: " + err.message);
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span class="material-symbols-outlined text-[16px]">save</span> Save Changes to Database';
      }
    });
  }

  /**
   * Delete Document Modal & Database Removal
   */
  function initDeleteDocumentModal() {
    const modal = document.getElementById("delete-doc-modal");
    const btnClose = document.getElementById("btn-close-delete-modal");
    const btnCancel = document.getElementById("btn-cancel-delete");
    const btnConfirm = document.getElementById("btn-confirm-delete");

    if (!modal) return;

    let targetDocId = null;
    let targetRow = null;

    function closeModal() {
      modal.style.display = "none";
      targetDocId = null;
      targetRow = null;
    }

    if (btnClose) btnClose.addEventListener("click", closeModal);
    if (btnCancel) btnCancel.addEventListener("click", closeModal);
    modal.addEventListener("click", (e) => {
      if (e.target === modal) closeModal();
    });

    window.openDeleteModal = function (docId, fileName, row) {
      targetDocId = docId;
      targetRow = row;
      document.getElementById("delete-target-id").textContent = docId;
      document.getElementById("delete-target-name").textContent = fileName;
      modal.style.display = "flex";
    };

    // Table delete button click
    document.addEventListener("click", (e) => {
      const delBtn = e.target.closest(".btn-delete-doc");
      if (delBtn) {
        e.stopPropagation();
        const row = delBtn.closest(".repo-doc-row");
        if (row) {
          const docId = row.getAttribute("data-doc-id");
          const fileName = row.getAttribute("data-doc-name");
          window.openDeleteModal(docId, fileName, row);
        }
      }
    });

    if (btnConfirm) {
      btnConfirm.addEventListener("click", async () => {
        if (!targetDocId) return;

        btnConfirm.disabled = true;
        btnConfirm.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> Purging from Database...';

        try {
          const response = await fetch("api/documents.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              action: "delete",
              doc_id: targetDocId,
            }),
          });

          const res = await response.json();

          if (res.success) {
            if (targetRow) {
              targetRow.remove();
            }

            if (window.showToast) {
              window.showToast("DOCUMENT PURGED", `Document ${targetDocId} removed from database vault.`, "warning", "delete");
            }

            closeModal();
            refreshStats();
            if (window.triggerRepoFilter) window.triggerRepoFilter();
          } else {
            alert("Deletion blocked: " + (res.message || "Failed to delete document"));
          }
        } catch (err) {
          alert("Network or database error: " + err.message);
        } finally {
          btnConfirm.disabled = false;
          btnConfirm.innerHTML = '<span class="material-symbols-outlined text-[16px]">delete_forever</span> Confirm Permanent Deletion';
        }
      });
    }
  }

  /**
   * Refresh Stats from Database API
   */
  async function refreshStats() {
    try {
      const response = await fetch("api/documents.php?action=stats");
      const res = await response.json();
      if (res.success && res.data) {
        const d = res.data;
        const totalElem = document.getElementById("kpi-total-docs");
        if (totalElem) totalElem.textContent = d.total_documents;
        const volElem = document.getElementById("kpi-vault-vol");
        if (volElem) volElem.innerHTML = `${d.vault_volume_gb.toFixed(1)} <span class="fc-font-size-14px-font-8cb8">GB</span>`;
        const pendElem = document.getElementById("kpi-pending-actions");
        if (pendElem) pendElem.textContent = `${d.pending_approvals} Action${d.pending_approvals === 1 ? "" : "s"}`;
        const intElem = document.getElementById("kpi-integrity-pct");
        if (intElem) intElem.textContent = `${d.integrity_percentage.toFixed(1)}%`;

        // Update pills
        if (d.classification_counts) {
          const c = d.classification_counts;
          const fAll = document.getElementById("filter-count-all");
          if (fAll) fAll.textContent = `(${c.all})`;
          const fHc = document.getElementById("filter-count-hc");
          if (fHc) fHc.textContent = `(${c["highly-confidential"]})`;
          const fConf = document.getElementById("filter-count-conf");
          if (fConf) fConf.textContent = `(${c.confidential})`;
          const fInt = document.getElementById("filter-count-internal");
          if (fInt) fInt.textContent = `(${c.internal})`;
          const fPub = document.getElementById("filter-count-public");
          if (fPub) fPub.textContent = `(${c.public})`;
        }

        // Update sidebar count
        const sbTotal = document.getElementById("sidebar-total-docs");
        if (sbTotal) sbTotal.textContent = d.total_documents;
      }
    } catch (e) {
      // Ignore background stats fetch errors
    }
  }
})();
