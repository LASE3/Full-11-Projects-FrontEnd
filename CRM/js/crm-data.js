/**
 * VOSTOKPRIBOR CRM — Live Data Integration  (Class 5)
 * Requires: ../../assets/js/api-client.js loaded before this file.
 *
 * What this module does:
 *  - Customers.php  → replaces static table rows with live DB data
 *  - Leads.php      → populates lead pipeline cards
 *  - Opportunities.php → populates Kanban columns
 *  - SalesForecast.php → renders forecast chart rows
 *
 * The script is defensive: if a page element doesn't exist (wrong page),
 * the loader simply skips — so this single file can be included on every
 * CRM page.
 */

(function () {
  'use strict';

  const api = window.VostokAPI;
  const { ui, crm, escHtml } = api;

  /* ──────────────────────────────────────────────
   * CUSTOMERS PAGE
   * Element: #customers-tbody
   * ────────────────────────────────────────────── */
  async function loadCustomers() {
    const tbody = document.getElementById('customers-tbody');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;"><span class="vp-spinner"></span> Loading enterprise accounts…</td></tr>';

    try {
      const res  = await crm.customers();
      const rows = res.data;

      if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;opacity:0.5;">No customer accounts found.</td></tr>';
        return;
      }

      tbody.innerHTML = rows.map(c => {
        const healthClass = c.health_score >= 90 ? 'health-optimal'
          : c.health_score >= 75 ? 'health-good' : 'health-low';
        const tier = c.account_tier === 'Enterprise' ? 'tier-badge strategic'
          : c.account_tier === 'Premium' ? 'tier-badge tier-1' : 'tier-badge tier-2';

        return `
          <tr class="account-row account-row-tagged"
              onclick="window.location.href='CustomerDetail.php?id=${encodeURIComponent(c.cus_id)}'">
            <td>
              <div class="account-name-cell">
                <span class="account-name-title" style="color:var(--crm-indigo);">${escHtml(c.company_name)}</span>
                <span class="account-name-sub">Account ID: ${escHtml(c.cus_id)} · ${escHtml(c.industry || '')}</span>
              </div>
            </td>
            <td><span class="${tier}">${escHtml(c.industry || c.account_tier || '—')}</span></td>
            <td>
              <strong>${escHtml(c.account_manager || '—')}</strong>
              <div style="font-size:11px;color:var(--crm-text-muted);">Key Account Manager</div>
            </td>
            <td>
              <strong style="font-family:var(--crm-font-mono);font-size:14px;color:var(--crm-navy);">
                ${ui.currency(c.total_contract_value || 0, c.currency || 'USD')}
              </strong>
              <div style="font-size:11px;color:var(--crm-success);">Active contracts</div>
            </td>
            <td>
              <span style="font-family:var(--crm-font-mono);font-size:11px;font-weight:600;">
                ${escHtml(c.active_contract_ref || '—')}
              </span>
              <div style="font-size:10px;color:var(--crm-text-muted);">
                ${c.contract_end ? 'Valid thru ' + ui.date(c.contract_end) : ''}
              </div>
            </td>
            <td>
              <span class="${healthClass}" style="font-family:var(--crm-font-mono);font-weight:700;">
                ${c.health_score != null ? c.health_score + '%' : '—'}
              </span>
            </td>
            <td>
              <a href="CustomerDetail.php?id=${encodeURIComponent(c.cus_id)}" class="btn btn-indigo btn-sm">
                Full Profile →
              </a>
            </td>
          </tr>`;
      }).join('');

    } catch (err) {
      api.handleApiError(err, 'CRM Customers');
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;color:#e74c3c;">Failed to load accounts. Check console.</td></tr>';
    }
  }

  /* ──────────────────────────────────────────────
   * LEADS PAGE
   * Element: #leads-pipeline-list
   * ────────────────────────────────────────────── */
  async function loadLeads() {
    const list = document.getElementById('leads-pipeline-list');
    const tbody = document.getElementById('leads-tbody');
    if (!list && !tbody) return;

    if (list) list.innerHTML = '<div style="padding:24px;text-align:center;"><span class="vp-spinner"></span> Loading leads…</div>';
    if (tbody) tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:28px;"><span class="vp-spinner"></span> Loading inbound leads…</td></tr>';

    try {
      const res   = await crm.leads();
      const leads = res.data;

      if (!leads.length) {
        if (list) list.innerHTML = '<div style="padding:24px;opacity:0.5;text-align:center;">No active leads in pipeline.</div>';
        if (tbody) tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:28px;opacity:0.5;">No active leads found.</td></tr>';
        return;
      }

      const stageColor = { Hot: '#e74c3c', Qualified: '#2ecc71', Warm: '#f39c12', Cold: '#3498db', New: '#9b59b6' };

      if (tbody) {
        tbody.innerHTML = leads.map(l => {
          const sc = stageColor[l.status || l.lead_status] || '#888';
          return `
            <tr class="account-row account-row-tagged">
              <td>
                <div class="account-name-cell">
                  <span class="account-name-title">${escHtml(l.full_name || l.contact_name || 'Contact')}</span>
                  <span class="account-name-sub">${escHtml(l.email || '')} ${l.phone ? '· ' + escHtml(l.phone) : ''}</span>
                </div>
              </td>
              <td>
                <strong>${escHtml(l.company_name || '—')}</strong>
                <div style="font-size:11px;color:var(--crm-text-muted);">${escHtml(l.source_page || 'Inbound')}</div>
              </td>
              <td style="max-width:280px;font-size:12px;color:var(--crm-text-muted);">
                ${escHtml(l.message || '—')}
              </td>
              <td>
                <strong style="font-family:var(--crm-font-mono);font-size:13px;color:var(--crm-navy);">
                  ${l.estimated_value ? ui.currency(l.estimated_value) : 'TBD'}
                </strong>
              </td>
              <td>
                <span style="font-family:var(--crm-font-mono);font-weight:700;color:${sc};">
                  ${l.lead_score != null ? l.lead_score + ' pts' : '—'}
                </span>
              </td>
              <td>
                <span class="badge" style="font-size:11px;">${escHtml(l.source_page || 'Web')}</span>
              </td>
              <td>
                <span style="padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700;background:${sc}20;color:${sc};">
                  ${escHtml(l.status || l.lead_status || 'New')}
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-primary-amber"
                  onclick="VostokAPI.crm.convertLead('${escHtml(l.lead_id)}').then(()=>VostokAPI.ui.toast('Lead Converted','Opportunity created.','success')).catch(e=>VostokAPI.handleApiError(e,'Convert Lead'))">
                  Convert →
                </button>
              </td>
            </tr>`;
        }).join('');
      }

      if (list) {
        list.innerHTML = leads.map(l => `
          <div class="lead-card" data-lead-id="${escHtml(l.lead_id)}">
            <div class="lead-card__header">
              <span class="lead-stage-badge" style="background:${stageColor[l.lead_status] || '#555'}20;color:${stageColor[l.lead_status] || '#aaa'};">
                ${escHtml(l.lead_status || 'New')}
              </span>
              <span class="lead-score">${l.lead_score != null ? l.lead_score + ' pts' : ''}</span>
            </div>
            <div class="lead-company">${escHtml(l.company_name || l.contact_name)}</div>
            <div class="lead-contact">${escHtml(l.contact_name)} · ${escHtml(l.contact_email || '')}</div>
            <div class="lead-meta">
              <span>${escHtml(l.industry || '')}</span>
              <span>${ui.currency(l.estimated_value || 0)}</span>
            </div>
            <div class="lead-assigned">Rep: ${escHtml(l.assigned_rep || '—')}</div>
            <div class="lead-actions">
              <button class="btn btn-sm btn-primary-amber"
                onclick="VostokAPI.crm.convertLead('${escHtml(l.lead_id)}').then(()=>VostokAPI.ui.toast('Lead Converted','Opportunity created.','success')).catch(e=>VostokAPI.handleApiError(e,'Convert Lead'))">
                Convert → Opportunity
              </button>
            </div>
          </div>
        `).join('');
      }

    } catch (err) {
      api.handleApiError(err, 'CRM Leads');
      if (list) list.innerHTML = '<div style="padding:24px;color:#e74c3c;">Failed to load leads.</div>';
      if (tbody) tbody.innerHTML = '<tr><td colspan="8" style="color:#e74c3c;padding:24px;text-align:center;">Failed to load leads.</td></tr>';
    }
  }

  /* ──────────────────────────────────────────────
   * OPPORTUNITIES KANBAN
   * Elements: .kanban-column[data-stage] or #kanban-cards-...
   * ────────────────────────────────────────────── */
  async function loadOpportunities() {
    const hasKanban = document.querySelector('.kanban-board')
      || document.getElementById('kanban-cards-qualification')
      || document.querySelector('.kanban-board-container');
    if (!hasKanban) return;

    try {
      const res  = await crm.opportunities();
      const opps = res.data;

      // If crmApp is available, inject data directly into it and re-render
      if (window.crmApp && typeof window.crmApp.renderKanban === 'function') {
        const stageMap = {
          'qualification': 'qualification',
          'proposal': 'proposal',
          'negotiation': 'negotiation',
          'contract': 'contract',
          'won': 'won'
        };
        window.crmApp.opportunities = opps.map((o, idx) => {
          let rawStage = (o.stage || '').toLowerCase().trim();
          let matched = 'qualification';
          for (const s of Object.keys(stageMap)) {
            if (rawStage.includes(s)) { matched = s; break; }
          }
          if (!rawStage && idx === 0) matched = 'qualification';
          if (!rawStage && idx === 3) matched = 'contract';

          return {
            id: 'OPP-2026-' + String(o.opp_id).padStart(4, '0'),
            client: o.company_name || ('Enterprise Account ' + o.cus_id),
            title: o.opp_title || (o.sector ? o.sector + ' Instrumentation' : 'Industrial Automation System'),
            value: parseFloat(o.estimated_value || 500000),
            stage: matched,
            probability: parseInt(o.probability_percent || (matched === 'negotiation' ? 80 : matched === 'proposal' ? 60 : 35)),
            closeDate: o.expected_close_date ? ui.date(o.expected_close_date) : 'Q4 2026',
            rep: { name: o.sales_representative || 'Pavel Orlov', avatar: '' },
            confidential: true,
            priority: 'high'
          };
        });
        window.crmApp.renderKanban();
        return;
      }

      const byStage = {};
      opps.forEach(o => {
        const stage = (o.stage || 'qualification').toLowerCase();
        if (!byStage[stage]) byStage[stage] = [];
        byStage[stage].push(o);
      });

      // Inject into each column that has data-stage attribute
      document.querySelectorAll('.kanban-column[data-stage]').forEach(col => {
        const stage = col.dataset.stage;
        const cards = byStage[stage] || [];
        const body  = col.querySelector('.kanban-column__body');
        if (!body) return;

        // Remove existing static cards (keep the column header)
        body.innerHTML = '';

        if (!cards.length) {
          body.innerHTML = '<div class="kanban-empty">No deals in this stage.</div>';
          return;
        }

        cards.forEach(o => {
          const card = document.createElement('div');
          card.className = 'opportunity-card';
          card.dataset.oppId = o.opp_id;
          card.innerHTML = `
            <div class="opp-client">${escHtml(o.company_name || o.cus_id)}</div>
            <div class="opp-title">${escHtml(o.opp_title || o.deal_name)}</div>
            <div class="opp-value">${ui.currency(o.deal_value || o.estimated_value || 0, o.currency || 'USD')}</div>
            <div class="opp-meta">
              <span class="opp-prob">${o.probability_percent || o.probability || 0}%</span>
              <span class="opp-close">${o.expected_close_date ? ui.date(o.expected_close_date) : ''}</span>
            </div>
          `;
          body.appendChild(card);
        });
      });

    } catch (err) {
      api.handleApiError(err, 'CRM Opportunities');
    }
  }

  /* ──────────────────────────────────────────────
   * SALES FORECAST
   * Element: #forecast-tbody
   * ────────────────────────────────────────────── */
  async function loadForecasts() {
    const tbody = document.getElementById('forecast-tbody');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:24px;"><span class="vp-spinner"></span></td></tr>';

    try {
      const res  = await crm.forecasts();
      const rows = res.data;

      tbody.innerHTML = rows.map(f => {
        const actual   = parseFloat(f.actual_amount  || 0);
        const forecast = parseFloat(f.forecast_amount || 0);
        const pct      = forecast > 0 ? ((actual / forecast) * 100).toFixed(1) : '—';
        const onTrack  = actual >= forecast * 0.9;

        return `
          <tr>
            <td style="font-weight:700;">${escHtml(f.period)}</td>
            <td>${escHtml(f.sales_representative || '—')}</td>
            <td style="font-family:var(--crm-font-mono);">${ui.currency(forecast)}</td>
            <td style="font-family:var(--crm-font-mono);">${actual ? ui.currency(actual) : '<em style="opacity:.5">Pending</em>'}</td>
            <td>
              <span style="font-weight:700;color:${onTrack ? '#2ecc71' : '#e74c3c'};">
                ${pct !== '—' ? pct + '%' : '—'}
              </span>
            </td>
          </tr>`;
      }).join('');

    } catch (err) {
      api.handleApiError(err, 'Sales Forecast');
      tbody.innerHTML = '<tr><td colspan="5" style="color:#e74c3c;padding:24px;">Failed to load forecasts.</td></tr>';
    }
  }

  /* ──────────────────────────────────────────────
   * Omni-search filter (client-side, post-load)
   * ────────────────────────────────────────────── */
  function initSearch() {
    const input = document.getElementById('global-omni-search');
    if (!input) return;

    input.addEventListener('input', () => {
      const q = input.value.toLowerCase().trim();
      document.querySelectorAll('.account-row, .lead-card, .opportunity-card').forEach(el => {
        el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });

    // Keyboard shortcut Ctrl+K
    document.addEventListener('keydown', e => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        input.focus();
        input.select();
      }
    });
  }

  /* ──────────────────────────────────────────────
   * Boot
   * ────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', () => {
    loadCustomers();
    loadLeads();
    loadOpportunities();
    loadForecasts();
    initSearch();
  });

})();
