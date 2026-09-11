/**
 * VOSTOKPRIBOR Administration & Governance Portal - System 11
 * Telemetry Ingestion Bridges Module (SYS 01-10 Pipeline)
 */

(function () {
    'use strict';

    // Global bridge selector function so existing onclick attributes work seamlessly
    window.selectBridge = function (id, name, location, protocol, eps, latency, buffer, status, classification) {
        const titleElem = document.getElementById('inspector-bridge-title');
        const idElem = document.getElementById('inspector-bridge-id');
        const locElem = document.getElementById('inspector-location');
        const protoElem = document.getElementById('inspector-protocol');
        const epsElem = document.getElementById('inspector-eps');
        const latElem = document.getElementById('inspector-latency');
        const buffText = document.getElementById('inspector-buffer-text');
        const barElem = document.getElementById('inspector-buffer-bar');
        const badgeElem = document.getElementById('inspector-status-badge');
        const stripElem = document.getElementById('inspector-strip');

        if (titleElem) titleElem.textContent = name;
        if (idElem) idElem.textContent = `${id} // TELEMETRY BRIDGE`;
        if (locElem) locElem.textContent = location;
        if (protoElem) protoElem.textContent = protocol;
        if (epsElem) epsElem.textContent = `${eps} EPS`;
        if (latElem) latElem.textContent = latency;

        if (buffText) buffText.textContent = `${Math.floor(buffer * 81.92)} / 8,192 LINES (${buffer}%)`;
        if (barElem) barElem.style.width = `${buffer}%`;

        if (badgeElem && barElem && stripElem) {
            if (buffer > 70) {
                badgeElem.textContent = 'BUFFER SATURATION WARNING';
                badgeElem.className = 'font-label-uppercase text-label-uppercase text-error font-bold';
                barElem.className = 'bg-error h-full transition-all duration-500 animate-pulse';
                stripElem.className = 'absolute left-0 top-0 bottom-0 w-1 bg-error';
            } else if (buffer > 40) {
                badgeElem.textContent = 'BUFFER NOMINAL / MODERATE LOAD';
                badgeElem.className = 'font-label-uppercase text-label-uppercase text-tertiary-container font-bold';
                barElem.className = 'bg-tertiary-fixed-dim h-full transition-all duration-500';
                stripElem.className = 'absolute left-0 top-0 bottom-0 w-1 bg-[#D9822B]';
            } else {
                badgeElem.textContent = 'BUFFER OPTIMAL';
                badgeElem.className = 'font-label-uppercase text-label-uppercase text-secondary font-bold';
                barElem.className = 'bg-secondary h-full transition-all duration-500';
                stripElem.className = 'absolute left-0 top-0 bottom-0 w-1 bg-secondary';
            }
        }

        // Highlight selected bridge card or row
        document.querySelectorAll('.bridge-card').forEach(c => c.classList.remove('active-bridge'));
        const activeCard = Array.from(document.querySelectorAll('.bridge-card')).find(c => c.textContent.includes(id));
        if (activeCard) activeCard.classList.add('active-bridge');

        // Append to live log
        const logBox = document.getElementById('live-event-stream');
        if (logBox) {
            const entry = document.createElement('div');
            entry.className = 'flex items-start gap-space-xs py-0.5 border-b border-white/5 font-mono text-[11px]';
            const now = new Date();
            const timeStr = now.toTimeString().split(' ')[0];
            entry.innerHTML = `<span class="text-on-surface-variant">${timeStr}</span> <span class="text-primary font-bold">[${id}]</span> <span class="text-on-surface">Operator switched inspection focus to ${name} (${location})</span>`;
            logBox.insertBefore(entry, logBox.firstChild);
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Buffer expander button
        const btnIncreaseBuffer = document.getElementById('btn-increase-buffer');
        if (btnIncreaseBuffer) {
            btnIncreaseBuffer.addEventListener('click', function () {
                const bar = document.getElementById('inspector-buffer-bar');
                const buffText = document.getElementById('inspector-buffer-text');
                const badge = document.getElementById('inspector-status-badge');
                const strip = document.getElementById('inspector-strip');

                if (bar) {
                    bar.style.width = '54%';
                    bar.className = 'bg-secondary h-full transition-all duration-500';
                }
                if (buffText) buffText.textContent = '6,881 / 12,288 LINES (54%)';
                if (badge) {
                    badge.textContent = 'BUFFER EXPANDED (+4MB COMMITTED)';
                    badge.className = 'font-label-uppercase text-label-uppercase text-secondary font-bold';
                }
                if (strip) strip.className = 'absolute left-0 top-0 bottom-0 w-1 bg-secondary';

                this.textContent = 'BUFFER ALLOCATED (+4 MB)';
                this.disabled = true;
                this.classList.add('opacity-70');

                window.showToast('BUFFER COMMITTED', 'Allocated +4 MB RAM pool to selected ingestion bridge.', 'success', 'memory');
            });
        }

        // Force flush button
        const btnForceFlush = document.getElementById('btn-force-flush');
        if (btnForceFlush) {
            btnForceFlush.addEventListener('click', function () {
                const logBox = document.getElementById('live-event-stream');
                if (logBox) {
                    const entry = document.createElement('div');
                    entry.className = 'flex items-start gap-space-xs text-secondary font-bold py-0.5 border-b border-white/5 font-mono text-[11px]';
                    entry.innerHTML = `<span class="text-on-surface-variant">NOW</span><span>[PIPELINE]</span><span>Manual Kafka ring-buffer checkpoint flushed (6,881 lines cleared to disk)</span>`;
                    logBox.insertBefore(entry, logBox.firstChild);
                }
                const bar = document.getElementById('inspector-buffer-bar');
                const buffText = document.getElementById('inspector-buffer-text');
                if (bar) bar.style.width = '12%';
                if (buffText) buffText.textContent = '983 / 8,192 LINES (12%)';

                window.showToast('BUFFER PURGED', 'Manual ring-buffer flush committed to persistent write-once store.', 'info', 'cleaning_services');
            });
        }

        // Node filter search
        const filterInput = document.getElementById('node-filter-input');
        if (filterInput) {
            filterInput.addEventListener('input', function (e) {
                const q = e.target.value.toUpperCase();
                const rows = document.querySelectorAll('#bridge-table-body tr');
                rows.forEach(r => {
                    const text = r.innerText.toUpperCase();
                    r.style.display = text.includes(q) ? '' : 'none';
                });
            });
        }
    });
})();
