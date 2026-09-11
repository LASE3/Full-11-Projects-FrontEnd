/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Privileged Accounts Monitoring & Vault Control Module
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        const reqModal = document.getElementById('modal-request-credential');
        const checkinModal = document.getElementById('modal-checkin-confirm');
        const btnRequestCred = document.getElementById('btn-request-cred');
        const btnCheckinAll = document.getElementById('btn-checkin-all');
        const closeModalReq = document.getElementById('close-modal-req');
        const cancelModalReq = document.getElementById('cancel-modal-req');
        const closeModalCheckin = document.getElementById('close-modal-checkin');
        const cancelModalCheckin = document.getElementById('cancel-modal-checkin');
        const submitModalReq = document.getElementById('submit-modal-req');
        const confirmMassCheckin = document.getElementById('confirm-mass-checkin');

        // Modal Open / Close
        if (btnRequestCred && reqModal) {
            btnRequestCred.addEventListener('click', () => reqModal.classList.remove('hidden'));
        }
        if (closeModalReq && reqModal) {
            closeModalReq.addEventListener('click', () => reqModal.classList.add('hidden'));
        }
        if (cancelModalReq && reqModal) {
            cancelModalReq.addEventListener('click', () => reqModal.classList.add('hidden'));
        }

        if (btnCheckinAll && checkinModal) {
            btnCheckinAll.addEventListener('click', () => checkinModal.classList.remove('hidden'));
        }
        if (closeModalCheckin && checkinModal) {
            closeModalCheckin.addEventListener('click', () => checkinModal.classList.add('hidden'));
        }
        if (cancelModalCheckin && checkinModal) {
            cancelModalCheckin.addEventListener('click', () => checkinModal.classList.add('hidden'));
        }

        // Tier Selection
        document.querySelectorAll('.tier-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tier-btn').forEach(b => {
                    b.classList.remove('border-error', 'bg-error/10', 'text-error', 'border-primary', 'bg-primary-container', 'text-on-primary', 'border-secondary', 'bg-secondary-container/20');
                    b.classList.add('border-outline-variant/60');
                    const title = b.querySelector('span:first-child');
                    if (title) title.className = 'font-security-stamp text-[11px] font-bold text-primary';
                });

                const tier = btn.getAttribute('data-tier');
                const selectedTierInput = document.getElementById('req-selected-tier');
                if (selectedTierInput) selectedTierInput.value = tier;
                const notice = document.getElementById('tier0-notice');

                if (tier === '0') {
                    btn.classList.add('border-error', 'bg-error/10', 'text-error');
                    btn.querySelector('span:first-child').className = 'font-security-stamp text-[11px] font-bold text-error';
                    if (notice) notice.classList.remove('hidden');
                } else if (tier === '1') {
                    btn.classList.add('border-[#D9822B]', 'bg-tertiary-fixed/20');
                    btn.querySelector('span:first-child').className = 'font-security-stamp text-[11px] font-bold text-[#D9822B]';
                    if (notice) notice.classList.add('hidden');
                } else {
                    btn.classList.add('border-secondary', 'bg-secondary-container/20');
                    btn.querySelector('span:first-child').className = 'font-security-stamp text-[11px] font-bold text-secondary';
                    if (notice) notice.classList.add('hidden');
                }
            });
        });

        // TTL Selection
        document.querySelectorAll('.ttl-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.ttl-btn').forEach(b => {
                    b.className = 'ttl-btn py-space-xs px-1 text-center font-telemetry-micro text-[11px] rounded border border-outline-variant/50 hover:bg-surface-container font-medium';
                });
                btn.className = 'ttl-btn py-space-xs px-1 text-center font-telemetry-micro text-[11px] rounded border-2 border-primary bg-primary-container text-on-primary font-bold';
                const dur = btn.getAttribute('data-duration');
                const ttlInput = document.getElementById('req-selected-ttl');
                const ttlDisplay = document.getElementById('req-ttl-display');
                if (ttlInput) ttlInput.value = dur;
                if (ttlDisplay) ttlDisplay.innerText = dur;
            });
        });

        // Submit Request (Mint Credential)
        if (submitModalReq) {
            submitModalReq.addEventListener('click', () => {
                const targetAssetEl = document.getElementById('req-target-asset');
                const asset = targetAssetEl ? targetAssetEl.value.split(' ')[0] : 'SYS-11';
                const tierEl = document.getElementById('req-selected-tier');
                const tier = tierEl ? tierEl.value : '1';
                const ttlEl = document.getElementById('req-selected-ttl');
                const ttl = ttlEl ? ttlEl.value : '30m';

                if (reqModal) reqModal.classList.add('hidden');

                const tbody = document.getElementById('vault-registry-tbody');
                const newRow = document.createElement('tr');
                newRow.className = 'bg-secondary-container/20 border-l-4 border-l-secondary-fixed transition-colors vault-row';
                newRow.innerHTML = `
                    <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-space-xs">
                                <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-ping"></span>
                                <span class="font-telemetry-data text-telemetry-data font-bold text-primary">req_${asset.toLowerCase()}@vostok</span>
                            </div>
                            <span class="font-telemetry-micro text-[10px] text-on-surface-variant">${asset} • JUST-IN-TIME</span>
                        </div>
                    </td>
                    <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                        <div class="flex flex-col">
                            <span class="font-semibold text-primary">Timur Akhmetov</span>
                            <span class="font-telemetry-micro text-[10px] text-secondary font-bold">EMP-1005 (DISPATCHED)</span>
                        </div>
                    </td>
                    <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                        <span class="font-security-stamp text-[10px] px-space-xs py-[2px] ${tier === '0' ? 'bg-error text-on-error' : (tier === '1' ? 'bg-[#D9822B] text-white' : 'bg-secondary-fixed text-on-secondary-fixed')} font-bold rounded">TIER ${tier}</span>
                    </td>
                    <td class="py-space-xs px-space-sm border-r border-outline-variant/20 font-telemetry-micro text-telemetry-micro">
                        HSM Ephemeral v1.3
                    </td>
                    <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                        <div class="flex flex-col gap-1 w-28">
                            <span class="font-bold text-secondary font-telemetry-micro">${ttl} TTL Active</span>
                            <div class="w-full bg-surface-container h-1.5 rounded overflow-hidden">
                                <div class="bg-secondary h-full" style="width: 100%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-space-xs px-space-sm border-r border-outline-variant/20">
                        <span class="inline-flex items-center gap-[4px] px-space-xs py-[2px] bg-secondary-container/30 text-on-secondary-container font-security-stamp text-[10px] font-bold rounded">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>PROVISIONED
                        </span>
                    </td>
                    <td class="py-space-xs px-space-sm text-right">
                        <button class="px-space-xs py-[3px] bg-error-container text-on-error-container hover:bg-error hover:text-on-error rounded font-telemetry-micro text-telemetry-micro font-bold sever-row-btn">Sever</button>
                    </td>
                `;

                if (tbody) tbody.insertBefore(newRow, tbody.firstChild);

                const activeKpi = document.getElementById('kpi-active-leases-count');
                if (activeKpi) activeKpi.innerText = '08 ACCOUNTS CHECKED OUT';

                window.showToast(
                    'EPHEMERAL LEASE GRANTED',
                    `New credential minted for ${asset} (${ttl}) [FIPS-140-3 HSM VALIDATED]`,
                    'success',
                    'vpn_key'
                );
            });
        }

        // Mass Check-In Confirmation
        if (confirmMassCheckin) {
            confirmMassCheckin.addEventListener('click', () => {
                if (checkinModal) checkinModal.classList.add('hidden');
                const activeKpi = document.getElementById('kpi-active-leases-count');
                if (activeKpi) activeKpi.innerText = '00 ACCOUNTS CHECKED OUT';

                const tbody = document.getElementById('vault-registry-tbody');
                if (tbody) {
                    tbody.querySelectorAll('tr').forEach(tr => {
                        const tag = tr.querySelector('.bg-secondary-container\\/30, .bg-primary-fixed');
                        if (tag) {
                            tag.className = 'inline-flex items-center gap-[4px] px-space-xs py-[2px] bg-surface-container text-on-surface-variant font-security-stamp text-[10px] font-bold rounded';
                            tag.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-outline"></span>ROTATED &amp; VAULTED';
                        }
                    });
                    tbody.querySelectorAll('.bg-secondary-fixed, .animate-pulse, .animate-ping').forEach(el => {
                        el.classList.remove('animate-pulse', 'animate-ping', 'bg-secondary-fixed');
                        el.classList.add('bg-outline');
                    });
                }

                window.showToast(
                    'ALL LEASES CHECKED IN',
                    'All 7 active leases successfully returned to HSM Vault [ECDSA-SECP256K1 CONFIRMED]',
                    'success',
                    'lock_reset'
                );
            });
        }

        // Delegated Event for Sever Row buttons
        document.addEventListener('click', (e) => {
            if (e.target && e.target.classList.contains('sever-row-btn')) {
                const tr = e.target.closest('tr');
                if (tr) {
                    tr.remove();
                    window.showToast('LEASE SEVERED', 'Ephemeral session terminated and cryptographic keys flushed from memory.', 'warn', 'link_off');
                }
            }
        });
    });
})();
