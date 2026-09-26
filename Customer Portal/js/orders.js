/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Procurement & Orders Controller
 * Page-specific logic for order filtering, live carrier telemetry streaming, and manifests.
 */

(function () {
  "use strict";

  /**
   * Filter orders table by search query, status, and facility sector
   */
  window.filterOrders = function () {
    const searchInput = document.getElementById("tableSearch");
    const statusSelect = document.getElementById("statusFilter");
    const facilitySelect = document.getElementById("facilityFilter");

    const searchVal = searchInput ? searchInput.value.toLowerCase() : "";
    const statusVal = statusSelect ? statusSelect.value : "ALL";
    const facilityVal = facilitySelect ? facilitySelect.value : "ALL";
    const rows = document.querySelectorAll("#ordersTable tbody tr");
    let visible = 0;

    rows.forEach((row) => {
      const text = row.innerText.toLowerCase();
      const status = row.getAttribute("data-status");
      const facility = row.getAttribute("data-facility");

      const matchSearch = text.includes(searchVal);
      const matchStatus = statusVal === "ALL" || status === statusVal;
      const matchFacility = facilityVal === "ALL" || facility === facilityVal;

      if (matchSearch && matchStatus && matchFacility) {
        row.style.display = "";
        visible++;
      } else {
        row.style.display = "none";
      }
    });

    const recordCount = document.getElementById("recordCount");
    if (recordCount) {
      recordCount.innerText = `${visible} of 18`;
    }
  };

  /**
   * Display interactive live carrier telemetry stream modal
   */
  window.showLiveTelemetryModal = function (orderId = "ORD-2024-8812") {
    if (!window.openModal) return;

    window.openModal(`
            <div class="flex flex-col gap-4 text-left">
                <div class="flex items-center justify-between pb-3 border-b border-outline/20">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded bg-tertiary-container text-tertiary-fixed">
                            <span class="material-symbols-outlined text-xl">satellite_alt</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-secondary">${orderId}</span>
                                <span class="px-1.5 py-0.2 rounded bg-tertiary-fixed/30 text-on-tertiary-fixed-variant font-mono text-[10px] font-bold">ACTIVE GPS FIX</span>
                            </div>
                            <h3 class="font-headline-sm text-sm font-bold text-primary">Live Carrier Transit Telemetry Stream</h3>
                        </div>
                    </div>
                    <span class="text-xs text-on-surface-variant font-mono">GLONASS / GPS</span>
                </div>
                <div class="grid grid-cols-3 gap-2 bg-surface-container-low p-3 rounded-lg text-center font-mono">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-outline uppercase">Current Speed</span>
                        <span class="text-sm font-bold text-primary">78 km/h</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-outline uppercase">Internal Cargo Temp</span>
                        <span class="text-sm font-bold text-secondary">+18.4°C</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-outline uppercase">Shock / G-Force</span>
                        <span class="text-sm font-bold text-on-tertiary-container">0.08G (Nominal)</span>
                    </div>
                </div>
                <div class="p-3 rounded-lg border border-outline/20 bg-surface-container-lowest flex flex-col gap-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-primary">Corridor Route: Highway A-114 (Km 182)</span>
                        <span class="text-secondary font-mono text-[11px]">ETA Nov 18, 14:00 MSK</span>
                    </div>
                    <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                        <div class="bg-secondary h-full rounded-full" style="width: 68%;"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-outline font-mono">
                        <span>Origin: Kolpino Facility</span>
                        <span>Destination: Cherepovets Plant #4</span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-tertiary-fixed-dim animate-ping"></span>
                        <span class="text-xs text-on-surface-variant font-mono">Telemetry Heartbeat: 4s</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="window.showToast('Carrier Contacted', 'Radio channel COM-DISP-4 linked to driver handset.', 'info')" class="px-3 py-1.5 rounded bg-surface-container hover:bg-surface-container-high text-xs font-semibold">Contact Driver</button>
                        <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" class="px-4 py-1.5 rounded bg-primary text-on-primary text-xs font-semibold">Close Stream</button>
                    </div>
                </div>
            </div>
        `);
  };

  /**
   * Check query parameters for automatic modal triggers or order focus
   */
  document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    if (params.get("telemetry") === "true") {
      const orderRef = params.get("order") || "ORD-2024-8812";
      setTimeout(() => window.showLiveTelemetryModal(orderRef), 400);
    }
  });
})();
