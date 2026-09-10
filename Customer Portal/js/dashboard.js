/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Executive Dashboard Controller
 * Page-specific logic for telemetry exports, manager contacts, and live facility metrics.
 */

(function () {
    'use strict';

    /**
     * Export Facility Telemetry Report (.PDF)
     */
    window.exportTelemetryPDF = function () {
        if (window.showToast) {
            window.showToast('Telemetry Archive Exported', 'Severstal_BF5_Telemetry_Report_2024.pdf compiled & cryptographically stamped.', 'success');
        }
    };

    /**
     * Connect directly to Assigned Manager Viktor Morozov
     */
    window.contactManager = function () {
        window.location.href = 'SupportTicketView.html?ticket=TCK-9482';
    };

    /**
     * Simulate live SCADA micro-fluctuations on dashboard gauges
     */
    function initLiveMetricsTicker() {
        const tempEl = document.getElementById('dash-bf5-temp');
        if (tempEl) {
            setInterval(() => {
                const baseTemp = 1350;
                const jitter = (Math.random() * 4 - 2).toFixed(1);
                tempEl.textContent = `${(baseTemp + parseFloat(jitter)).toFixed(1)}°C`;
            }, 3000);
        }
    }

    // Initialize on DOM load
    document.addEventListener('DOMContentLoaded', () => {
        initLiveMetricsTicker();
    });

})();
