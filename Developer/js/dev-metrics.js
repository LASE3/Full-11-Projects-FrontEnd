/**
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM
 * System 10: Usage Metrics, Quotas & Webhook Telemetry Module
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        // Timeframe selector
        document.querySelectorAll('.timeframe-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.timeframe-btn').forEach(b => {
                    b.classList.remove('vk-btn-primary');
                    b.classList.add('vk-btn-outline');
                });
                this.classList.remove('vk-btn-outline');
                this.classList.add('vk-btn-primary');

                const range = this.getAttribute('data-range');
                window.showToast('TELEMETRY FILTERED', `Aggregated metrics recomputed for timeframe: ${range}`, 'info', 'calendar_today');
            });
        });

        // Webhook Retry Action
        document.querySelectorAll('.btn-retry-webhook').forEach(btn => {
            btn.addEventListener('click', function () {
                const orig = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Sending...';

                setTimeout(() => {
                    this.innerHTML = '<span class="material-symbols-outlined text-[14px]">done</span> Delivered';
                    this.style.color = 'var(--vk-secondary)';
                    window.showToast('WEBHOOK DISPATCHED', 'Event frame re-delivered to customer HTTPS listener endpoint. Status 200 OK.', 'success', 'forward_to_inbox');
                }, 800);
            });
        });
    });
})();
