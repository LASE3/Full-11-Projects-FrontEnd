/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 09: File Center / Document Hub
 * Document Repository, Filtering & Inspector Drawer Module
 */

(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    initSearchAndFilter();
    initFolderTree();
    initDocumentDrawer();
  });

  /**
   * Search and Classification Filter
   */
  function initSearchAndFilter() {
    const searchInput = document.getElementById("repo-search-input");
    const filterPills = document.querySelectorAll(".filter-pill");
    const rows = document.querySelectorAll(".repo-doc-row");

    let currentClass = "all";
    let currentFolder = "all";

    function filterRows() {
      const query = searchInput ? searchInput.value.toLowerCase().trim() : "";

      rows.forEach((row) => {
        const docId = (row.getAttribute("data-doc-id") || "").toLowerCase();
        const docName = (row.getAttribute("data-doc-name") || "").toLowerCase();
        const docClass = (
          row.getAttribute("data-classification") || ""
        ).toLowerCase();
        const docFolder = (row.getAttribute("data-folder") || "").toLowerCase();
        const rowText = row.textContent.toLowerCase();

        const matchesQuery =
          !query ||
          rowText.includes(query) ||
          docId.includes(query) ||
          docName.includes(query);
        const matchesClass =
          currentClass === "all" || docClass === currentClass;
        const matchesFolder =
          currentFolder === "all" || docFolder === currentFolder;

        if (matchesQuery && matchesClass && matchesFolder) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });

      // Update visible document count
      const visibleCount = Array.from(rows).filter(
        (r) => r.style.display !== "none",
      ).length;
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

        const folderName = this.querySelector("span:nth-child(2)").textContent;
        window.showToast(
          "FOLDER SELECTED",
          `Browsing repository partition: ${folderName}`,
          "info",
          "folder_open",
        );
      });
    });
  }

  /**
   * Slide-Over Document Inspection Drawer
   */
  function initDocumentDrawer() {
    const backdrop = document.getElementById("doc-drawer-backdrop");
    const closeBtn = document.getElementById("btn-close-drawer");
    const rows = document.querySelectorAll(".repo-doc-row");

    if (!backdrop) return;

    function closeDrawer() {
      backdrop.style.display = "none";
    }

    if (closeBtn) closeBtn.addEventListener("click", closeDrawer);
    backdrop.addEventListener("click", (e) => {
      if (e.target === backdrop) closeDrawer();
    });

    // Inspector trigger buttons or row click
    document.querySelectorAll(".btn-inspect-doc").forEach((btn) => {
      btn.addEventListener("click", function (e) {
        e.stopPropagation();
        const row = this.closest(".repo-doc-row");
        if (row) openDrawerWithData(row);
      });
    });

    rows.forEach((row) => {
      row.addEventListener("click", function () {
        openDrawerWithData(this);
      });
    });

    function openDrawerWithData(row) {
      const docId = row.getAttribute("data-doc-id");
      const docName = row.getAttribute("data-doc-name");
      const docClass = row.getAttribute("data-classification");
      const docProj = row.getAttribute("data-project") || "N/A";
      const docCust =
        row.getAttribute("data-custodian") || "Farida Iskakova (EMP-1019)";
      const docSize = row.getAttribute("data-size") || "1.4 MB";
      const docDate = row.getAttribute("data-date") || "2026-09-11";
      const docHash =
        row.getAttribute("data-hash") ||
        "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855";
      const docSys = row.getAttribute("data-system") || "File Center";
      const docStatus = row.getAttribute("data-status") || "Approved";

      document.getElementById("drawer-doc-id").textContent = docId;
      document.getElementById("drawer-doc-name").textContent = docName;
      document.getElementById("drawer-doc-size").textContent = docSize;
      document.getElementById("drawer-doc-date").textContent = docDate;
      document.getElementById("drawer-doc-project").textContent = docProj;
      document.getElementById("drawer-doc-custodian").textContent = docCust;
      document.getElementById("drawer-doc-hash").textContent = docHash;
      document.getElementById("drawer-doc-system").textContent = docSys;
      document.getElementById("drawer-doc-status").textContent = docStatus;

      // Classification badge
      const classBadge = document.getElementById("drawer-class-badge");
      if (classBadge) {
        classBadge.className = "vk-tag";
        classBadge.textContent = docClass.toUpperCase();
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
          window.copyToClipboard(docHash, "SHA-256 Checksum");
        };
      }

      // Mock Download Action
      const downloadBtn = document.getElementById("btn-drawer-download");
      if (downloadBtn) {
        downloadBtn.onclick = () => {
          window.showToast(
            "SECURE DECRYPTION IN PROGRESS",
            `Retrieving ${docName} from encrypted Almaty vault...`,
            "info",
            "lock_open",
          );
          setTimeout(() => {
            window.showToast(
              "DOWNLOAD READY",
              `${docName} integrity verified (SHA-256 OK). Transferred via TLS 1.3.`,
              "success",
              "download_done",
            );
          }, 1000);
        };
      }

      backdrop.style.display = "flex";
    }
  }
})();
