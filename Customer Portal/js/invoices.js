/**
 * VOSTOKPRIBOR Enterprise Customer Portal - Treasury & Invoices Controller
 * Page-specific logic for invoice ledger filtering, wire packet copying, and Crypto-Pro EDS payments.
 */

(function () {
    'use strict';

    let currentFilterStatus = 'all';

    /**
     * Copy corporate banking wire instructions to clipboard
     */
    window.copyWirePacket = function () {
        const wireText = `VOSTOKPRIBOR INDUSTRIAL NPO\nDepository Bank: PJSC Sberbank / VTB Enterprise\nBIC: 044525225 | SWIFT: SABRRUMM\nClearing Acc: 40702810938000018921\nCorr Acc: 30101810400000000225\nINN: 7802194812 | KPP: 780201001\nOGRN: 1027801569420`;
        navigator.clipboard.writeText(wireText).then(() => {
            if (window.showToast) {
                window.showToast('Banking Wire Packet Copied', 'Cleared for Sberbank / VTB treasury transfer.', 'info');
            } else {
                alert('Banking details copied!');
            }
        }).catch(() => {
            if (window.showToast) {
                window.showToast('Banking wire packet ready in memory', 'info');
            }
        });
    };

    /**
     * Export FY 2024 Corporate Settlement Ledger
     */
    window.exportLedger = function () {
        if (window.showToast) {
            window.showToast('Corporate Ledger Exported', 'Severstal_Settlement_Ledger_2024.xlsx generated.', 'info');
        }
    };

    /**
     * Filter invoices by status tab (All, Pending, Paid, Credit)
     */
    window.filterInvoiceStatus = function (btn, status) {
        currentFilterStatus = status;
        document.querySelectorAll('.invoice-filter-tab').forEach(b => {
            b.className = 'invoice-filter-tab px-unit-base py-1 rounded text-on-surface-variant hover:text-on-surface font-body-md transition-colors';
        });
        if (btn) {
            btn.className = 'invoice-filter-tab px-unit-base py-1 rounded bg-surface-container-lowest text-on-surface font-headline-sm text-body-md font-semibold shadow-sm';
        }
        window.filterInvoiceTable();
    };

    /**
     * Filter invoices table rows based on status tab and search input
     */
    window.filterInvoiceTable = function () {
        const input = document.getElementById('invoiceSearchInput');
        const query = (input ? input.value : '').toLowerCase().trim();
        const rows = document.querySelectorAll('.invoice-row');

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status') || '';
            const text = row.innerText.toLowerCase();
            const matchesStatus = currentFilterStatus === 'all' || 
                                  (currentFilterStatus === 'pending' && rowStatus === 'pending') ||
                                  (currentFilterStatus === 'paid' && rowStatus === 'paid') ||
                                  (currentFilterStatus === 'credit' && rowStatus === 'credit');
            const matchesQuery = !query || text.includes(query);
            row.style.display = matchesStatus && matchesQuery ? '' : 'none';
        });
    };

    /**
     * Reset invoice search & filters
     */
    window.resetInvoiceFilters = function () {
        const input = document.getElementById('invoiceSearchInput');
        if (input) input.value = '';
        const allTab = document.querySelector('.invoice-filter-tab');
        if (allTab) window.filterInvoiceStatus(allTab, 'all');
    };

    /**
     * Display treasury payment disbursement modal with Crypto-Pro EDS signing
     */
    window.showPaymentModal = function (invoiceRef, customAmount) {
        const isSpecific = !!invoiceRef;
        const title = isSpecific ? `Authorize Disbursement: ${invoiceRef}` : 'Disburse Outstanding Accounts Payable';
        const amountFormatted = customAmount ? '$' + customAmount.toLocaleString('en-US') + '.00 USD' : (isSpecific ? '$114,200.00 USD' : '$248,600.00 USD');
        const invRef = invoiceRef || 'INV-2024-8819 & INV-2024-7019';

        const html = `
            <div class="space-y-4 text-left">
                <div class="p-3 bg-surface-container-high rounded border border-outline-variant/30 flex items-center justify-between">
                    <div>
                        <div class="text-xs text-on-surface-variant uppercase font-label-caps">Authorized Payee</div>
                        <div class="font-bold text-on-surface text-sm">VOSTOKPRIBOR INDUSTRIAL NPO (INN: 7802194812)</div>
                        <div class="text-xs text-on-surface-variant font-data-mono-md">Acc: 40702810938000018921 | BIC: 044525225</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-on-surface-variant uppercase font-label-caps">Total Disbursement</div>
                        <div class="text-xl font-bold font-data-mono-lg text-tertiary-fixed-dim">${amountFormatted}</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase">Invoice Allocation</label>
                    <input type="text" readonly value="${invRef}" 
                        class="w-full bg-surface-container px-3 py-2 rounded text-sm font-data-mono-md text-on-surface border border-outline-variant/30 focus:outline-none" />
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase">Payment Settlement Gateway</label>
                    <select id="paymentGatewaySelect" class="w-full bg-surface-container px-3 py-2 rounded text-sm text-on-surface border border-outline-variant/30 focus:outline-none">
                        <option value="sberbank">PJSC Sberbank Corporate Escrow Direct Wire (044525225)</option>
                        <option value="vtb">VTB Enterprise SWIFT MT103 Settlement (SABRRUMM)</option>
                        <option value="gazprom">Severstal Central Treasury Direct Clearing</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase">Disbursement Authorization Notes</label>
                    <input type="text" id="paymentNotesInput" value="Corporate settlement approved per Contract CTR-SVR-2024 Stage completion." 
                        class="w-full bg-surface-container px-3 py-2 rounded text-sm text-on-surface border border-outline-variant/30 focus:outline-none" />
                </div>

                <div class="p-3 bg-primary/5 rounded border border-tertiary-fixed/30 flex items-start gap-2">
                    <input type="checkbox" id="cryptoTokenAuth" checked class="mt-0.5 rounded border-outline" />
                    <label for="cryptoTokenAuth" class="text-xs text-on-surface cursor-pointer">
                        <span class="font-semibold text-on-surface">Crypto-Pro EDS Token Authentication:</span>
                        Attach qualified electronic digital signature of Alexey R. Danilov (Key #RU-SVR-77401-EDS).
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-outline-variant/30">
                    <button onclick="document.getElementById('portal-dynamic-modal')?.remove()" 
                        class="px-4 py-2 rounded bg-surface-container-high text-on-surface text-sm font-medium hover:bg-surface-container transition-colors">
                        Cancel
                    </button>
                    <button onclick="window.executePaymentRelease('${invRef}', '${amountFormatted}')" 
                        class="px-5 py-2 rounded bg-tertiary-fixed-dim text-on-tertiary-fixed text-sm font-bold hover:bg-tertiary-fixed transition-colors flex items-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-base">verified_user</span>
                        Authorize &amp; Transmit Wire
                    </button>
                </div>
            </div>
        `;

        if (window.openModal) {
            window.openModal(title, html);
        } else {
            alert(title);
        }
    };

    /**
     * Execute simulated payment release and update invoice table badges
     */
    window.executePaymentRelease = function (invRef, amount) {
        const modal = document.getElementById('portal-dynamic-modal');
        if (modal) modal.remove();

        if (window.showToast) {
            window.showToast(`Wire Transmitted`, `Electronic wire of ${amount} transmitted to PJSC Sberbank for ${invRef}`, 'success');
        }

        // Update UI badge of corresponding rows
        if (invRef.includes('8819')) {
            const r = document.getElementById('row-INV-2024-8819');
            if (r) {
                const badge = r.querySelector('td:nth-child(7) span');
                if (badge) {
                    badge.className = 'inline-flex items-center gap-1 px-unit-sm py-0.5 rounded bg-secondary-container text-on-secondary-container font-technical-tag text-technical-tag font-semibold';
                    badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> Wire Transmitted / In Clearing';
                }
            }
        }
        if (invRef.includes('7019')) {
            const r = document.getElementById('row-INV-2024-7019');
            if (r) {
                const badge = r.querySelector('td:nth-child(7) span');
                if (badge) {
                    badge.className = 'inline-flex items-center gap-1 px-unit-sm py-0.5 rounded bg-secondary-container text-on-secondary-container font-technical-tag text-technical-tag font-semibold';
                    badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> Wire Transmitted / In Clearing';
                }
            }
        }
    };

    /**
     * Parse query parameters on load
     */
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        if (params.get('pay') === 'true') {
            setTimeout(() => window.showPaymentModal(), 400);
        } else if (params.get('invoice')) {
            const inv = params.get('invoice');
            setTimeout(() => window.showPaymentModal(inv), 400);
        }
    });

})();
