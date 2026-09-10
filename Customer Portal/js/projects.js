/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Industrial Projects Controller
 * Page-specific logic for project filtering, milestone roadmaps, and RFQ scope changes.
 */

(function () {
    'use strict';

    /**
     * Filter projects table by search input and execution stage
     */
    window.filterProjects = function () {
        const searchInput = document.getElementById('projectFilterInput');
        const stageSelect = document.getElementById('projectStageSelect');

        const searchVal = (searchInput ? searchInput.value : '').toLowerCase();
        const stageVal = stageSelect ? stageSelect.value : 'ALL';
        const rows = document.querySelectorAll('table tbody tr:not([class*="bg-surface-container-low/60"])');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const matchesSearch = !searchVal || text.includes(searchVal);
            const matchesStage = (stageVal === 'ALL' || text.includes(stageVal.toLowerCase()));

            if (matchesSearch && matchesStage) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    /**
     * Display interactive Request For Quotation (RFQ) / Scope Change dialog
     */
    window.showRFQModal = function () {
        if (!window.openModal) return;

        window.openModal(`
            <div class="flex flex-col gap-4 text-left">
                <div class="flex items-center gap-3 pb-3 border-b border-outline/20">
                    <div class="p-2.5 rounded bg-tertiary-fixed text-primary">
                        <span class="material-symbols-outlined text-xl">post_add</span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-base font-bold text-primary">Request Project Scope Change / New RFQ</h3>
                        <p class="text-xs text-on-surface-variant">Severstal Metallurgy Plant #4 • Engineering Expansion</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 text-xs">
                    <div>
                        <label class="font-medium text-on-surface block mb-1">Associated Project ID</label>
                        <select id="rfqProjectSelect" class="w-full p-2 rounded bg-surface-container border border-outline/30 text-primary font-body-sm">
                            <option>PRJ-VP-7721: Blast Furnace #5 Automation Suite</option>
                            <option>PRJ-VP-7804: Hot Strip Mill Hydraulic Pressure Telemetry</option>
                            <option>PRJ-VP-7910: Coke Oven Battery IR Cameras</option>
                            <option>NEW RFQ (Standalone Turnkey Project)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-medium text-on-surface block mb-1">Scope Modification Description</label>
                        <textarea id="rfqDescInput" class="w-full p-2.5 rounded bg-surface-container border border-outline/30 text-primary font-body-sm h-20" placeholder="Specify addition of optical sensors, telemetry channels, or milestone dates..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-medium text-on-surface block mb-1">Estimated Budget Scope</label>
                            <input type="text" class="w-full p-2 rounded bg-surface-container border border-outline/30 text-primary font-body-sm" value="$150,000 — $300,000 USD">
                        </div>
                        <div>
                            <label class="font-medium text-on-surface block mb-1">Required In-Service Date</label>
                            <input type="text" class="w-full p-2 rounded bg-surface-container border border-outline/30 text-primary font-body-sm" value="Q1 2025">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline/20">
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" class="px-4 py-2 rounded bg-surface-container hover:bg-surface-container-high text-xs font-semibold">Cancel</button>
                    <button onclick="window.showToast('RFQ Transmitted', 'Project change order filed with Vostokpribor Chief Engineer. Ref: RFQ-2024-884.', 'success'); document.getElementById('portal-dynamic-modal')?.remove();" class="px-4 py-2 rounded bg-primary text-on-primary text-xs font-semibold">Submit Scope Request</button>
                </div>
            </div>
        `);
    };

    /**
     * Check query parameters for automatic RFQ dialog
     */
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        if (params.get('rfq') === 'true') {
            setTimeout(window.showRFQModal, 400);
        }
    });

})();
