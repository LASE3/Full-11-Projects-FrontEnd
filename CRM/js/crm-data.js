/**
 * VOSTOKPRIBOR CRM — Live Data Integration  (Class 5)
 * Requires: assets/js/api-core.js and assets/js/api-crm.js loaded before this file.
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

  const core = window.VostokCore || window.VostokAPI || {};
  const crm = window.VostokCRM || window.VostokAPI?.crm;
  const { ui = {}, escHtml = (s) => s } = core;
  const api = { handleApiError: core.handleApiError || console.error, ui, crm, escHtml };

  /* ──────────────────────────────────────────────
   * CUSTOMERS PAGE
   * Element: #customers-tbody
   * ────────────────────────────────────────────── */
  function filterCustomersBySector(sector) {
    const rows = document.querySelectorAll('#customers-tbody tr.account-row');
    if (!rows.length) return;
    const target = (sector || 'all').toLowerCase().trim();
    let visibleCount = 0;
    rows.forEach(r => {
      const rowSector = (r.getAttribute('data-sector') || '').toLowerCase().trim();
      const match = (target === 'all' || rowSector === target || rowSector.includes(target) || target.includes(rowSector));
      r.style.display = match ? '' : 'none';
      if (match) visibleCount++;
    });

    const noRowsEl = document.getElementById('no-customers-filter-row');
    if (visibleCount === 0) {
      if (!noRowsEl) {
        const tr = document.createElement('tr');
        tr.id = 'no-customers-filter-row';
        tr.innerHTML = '<td colspan="7" style="text-align:center;padding:32px;color:var(--crm-text-muted);">No customer accounts found for this sector.</td>';
        document.getElementById('customers-tbody').appendChild(tr);
      } else {
        noRowsEl.style.display = '';
      }
    } else if (noRowsEl) {
      noRowsEl.style.display = 'none';
    }
  }

  function initSectorFilters() {
    const pillButtons = document.querySelectorAll('.filter-pills-group .filter-pill-btn');
    if (!pillButtons.length) return;

    pillButtons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        pillButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        let sector = btn.getAttribute('data-sector');
        if (!sector) {
          const text = btn.textContent.trim();
          sector = text.replace(/\s*\(\d+\)$/, '').trim();
        }
        filterCustomersBySector(sector);
      });
    });
  }

  async function loadCustomers(forceRefresh = false) {
    const tbody = document.getElementById('customers-tbody');
    if (!tbody) return;

    // If table already has rows rendered by PHP, don't overwrite unless explicitly requested
    if (!forceRefresh && tbody.querySelectorAll('tr.account-row').length > 0) {
      return;
    }

    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;"><span class="vp-spinner"></span> Loading enterprise accounts…</td></tr>';

    try {
      const res  = await crm.customers();
      const rows = res.data || [];

      if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--crm-text-muted);">No customer accounts found.</td></tr>';
        return;
      }

      tbody.innerHTML = rows.map(c => {
        const hs = parseInt(c.health_score || 90);
        const healthClass = hs >= 92 ? 'crm-mono-bold-success' : (hs >= 80 ? 'crm-mono-bold-amber' : 'crm-mono-bold-danger');
        const healthLabel = hs >= 92 ? `${hs}% (Optimal)` : (hs >= 80 ? `${hs}% (Good)` : `${hs}% (Action Required)`);
        const tierClass = (c.account_tier || '').toLowerCase().includes('strategic') ? 'tier-badge strategic'
          : ((c.account_tier || '').toLowerCase().includes('tier-1') ? 'tier-badge tier-1' : 'tier-badge tier-2');
        const contractVal = parseFloat(c.total_contract_value || c.total_contract_arr || 0);

        return `
          <tr class="account-row account-row-tagged" data-cus-id="${escHtml(c.cus_id)}" data-sector="${escHtml(c.sector || '')}"
              onclick="window.location.href='CustomerDetail.php?id=${encodeURIComponent(c.cus_id)}'">
            <td>
              <div class="account-name-cell">
                <span class="account-name-title crm-text-indigo">${escHtml(c.company_name)}</span>
                <span class="account-name-sub">Account ID: #${escHtml(c.cus_id)} · ${escHtml(c.headquarters || c.sector || 'Industrial Facility')}</span>
              </div>
            </td>
            <td><span class="${tierClass}">${escHtml(c.sector || 'Enterprise')}</span></td>
            <td>
              <strong>${escHtml(c.account_manager_name || c.account_manager || 'Dr. Elena Rostova')}</strong>
              <div class="crm-text-muted-sm">Key Account Lead</div>
            </td>
            <td>
              ${contractVal > 0 
                ? `<strong class="crm-mono-navy-lg">${ui.currency(contractVal, 'USD')}</strong><div class="crm-text-success-11">Active Master Agreement</div>`
                : `<strong class="crm-mono-navy-lg" style="color:var(--crm-text-muted);">$0.00</strong><div class="crm-text-muted-sm">Pending Procurement RFP</div>`}
            </td>
            <td>
              ${c.active_contract_ref 
                ? `<span class="crm-mono-semibold-11">${escHtml(c.active_contract_ref)}</span><div class="crm-text-muted-10">${c.contract_end ? 'Valid thru ' + escHtml(c.contract_end) : 'Active SLA Term'}</div>`
                : `<span class="crm-mono-semibold-11" style="color:var(--crm-text-muted);">MSA In Negotiation</span><div class="crm-text-muted-10">Standard Enterprise Terms</div>`}
            </td>
            <td>
              <span class="${healthClass}">${healthLabel}</span>
            </td>
            <td style="white-space:nowrap;">
              <a href="CustomerDetail.php?id=${encodeURIComponent(c.cus_id)}" class="btn btn-indigo btn-sm" onclick="event.stopPropagation()">
                Full Profile →
              </a>
              <button class="btn btn-outline btn-sm" style="color:#ef4444;border-color:rgba(239,68,68,0.3);margin-left:4px;padding:4px 8px;" title="Delete Account" onclick="event.stopPropagation(); window.crmApp && window.crmApp.deleteCustomer ? window.crmApp.deleteCustomer('${escHtml(c.cus_id)}') : null">✕</button>
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
          body.innerHTML = '<div class="kanban-empty">( No opportunities at the moment. )</div>';
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
   * DASHBOARD — KPI Cards, Funnel, Accounts, Activity
   * Elements: #dash-kpi-leads, #dash-kpi-opps, #dash-kpi-pipeline, #dash-kpi-winrate
   *           #dash-funnel-container, #dash-accounts-tbody, #dash-activity-feed
   * ────────────────────────────────────────────── */
  async function loadDashboard() {
    const kpiLeads    = document.getElementById('dash-kpi-leads');
    const kpiOpps     = document.getElementById('dash-kpi-opps');
    const kpiPipeline = document.getElementById('dash-kpi-pipeline');
    const kpiWinrate  = document.getElementById('dash-kpi-winrate');
    const funnelEl    = document.getElementById('dash-funnel-container');
    const accountsTbody = document.getElementById('dash-accounts-tbody');
    const activityFeed  = document.getElementById('dash-activity-feed');

    if (!kpiLeads && !funnelEl && !accountsTbody && !activityFeed) return;

    try {
      const res = await crm.dashboard();
      const d   = res.data || {};

      // ── KPI Cards ──
      if (kpiLeads)    kpiLeads.textContent    = d.lead_count    ?? d.new_leads    ?? '—';
      if (kpiOpps)     kpiOpps.textContent     = d.opp_count     ?? d.open_deals   ?? '—';
      if (kpiPipeline) {
        const pval = d.pipeline_value ?? d.gross_pipeline ?? null;
        kpiPipeline.textContent = pval != null ? ui.currency(pval, 'USD') : '—';
      }
      if (kpiWinrate)  kpiWinrate.textContent  = d.win_rate != null ? d.win_rate + '%' : '—';

      // Sub-text updates
      const leadSub   = document.getElementById('dash-kpi-leads-sub');
      const oppSub    = document.getElementById('dash-kpi-opps-sub');
      const pipeSub   = document.getElementById('dash-kpi-pipeline-sub');
      if (leadSub  && d.qualified_this_week != null) leadSub.textContent  = d.qualified_this_week + ' Qualified this week';
      if (oppSub   && d.late_negotiation    != null) oppSub.textContent   = d.late_negotiation + ' in Late Negotiation';
      if (pipeSub  && d.weighted_pipeline   != null) pipeSub.innerHTML    = 'Weighted: <strong>' + ui.currency(d.weighted_pipeline, 'USD') + '</strong>';

      // All Accounts link counter
      const accLink = document.getElementById('dash-accounts-link');
      if (accLink && d.customer_count != null) accLink.textContent = 'All Accounts (' + d.customer_count + ')';

      // Nav badges
      const navLeads = document.getElementById('dash-nav-leads');
      const navCust  = document.getElementById('dash-nav-customers');
      const navOpps  = document.getElementById('dash-nav-opps');
      if (navLeads && d.lead_count != null)     navLeads.textContent = d.lead_count;
      if (navCust  && d.customer_count != null) navCust.textContent  = d.customer_count;
      if (navOpps  && d.opp_count != null)      navOpps.textContent   = d.opp_count;

      // ── Funnel ──
      if (funnelEl && Array.isArray(d.funnel_stages) && d.funnel_stages.length) {
        const maxVal = Math.max(...d.funnel_stages.map(s => parseFloat(s.total_val || 0)), 1);
        funnelEl.innerHTML = d.funnel_stages.map((s, i) => {
          const pct = Math.round((parseFloat(s.total_val || 0) / maxVal) * 100);
          return `
            <div class="funnel-stage">
              <div class="funnel-stage-header">
                <span class="funnel-stage-name">${i + 1}. ${escHtml(s.stage)}</span>
                <span class="funnel-stage-count">${s.count} Deal${s.count !== 1 ? 's' : ''}</span>
              </div>
              <div class="funnel-stage-val">${ui.currency(s.total_val || 0, 'USD')}</div>
              <div class="funnel-bar-track">
                <div class="funnel-bar-fill" style="width:${pct}%"></div>
              </div>
              <div class="funnel-conversion-rate">
                <span>${i === 0 ? 'Entry' : 'Step Conv.'}</span>
                <strong>${s.conversion_rate ?? (i === 0 ? '100%' : '—')}</strong>
              </div>
            </div>`;
        }).join('');
      } else if (funnelEl) {
        funnelEl.innerHTML = '<div style="padding:24px;text-align:center;opacity:.5;">No pipeline data available.</div>';
      }

      // ── Top Accounts Table ──
      if (accountsTbody) {
        const accs = d.top_accounts || [];
        if (!accs.length) {
          accountsTbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;opacity:.5;">No customer accounts found.</td></tr>';
        } else {
          accountsTbody.innerHTML = accs.map(c => {
            const tierClass = c.account_tier === 'Strategic' ? 'tier-badge strategic'
              : c.account_tier === 'Enterprise' ? 'tier-badge tier-1' : 'tier-badge tier-2';
            return `
              <tr class="account-row account-row-tagged" onclick="window.location.href='CustomerDetail.php?id=${encodeURIComponent(c.cus_id)}'">
                <td>
                  <div class="account-name-cell">
                    <span class="account-name-title">${escHtml(c.company_name)}</span>
                    <span class="account-name-sub">${escHtml(c.industry || '')} · ${escHtml(c.cus_id)}</span>
                  </div>
                </td>
                <td><span class="${tierClass}">${escHtml(c.account_tier || c.industry || '—')}</span></td>
                <td><strong class="crm-mono-navy">${ui.currency(c.total_contract_value || 0, 'USD')}</strong></td>
                <td>
                  <span class="crm-mono-bold-indigo">${c.open_opps ?? 0} Deal${(c.open_opps ?? 0) !== 1 ? 's' : ''}</span>
                </td>
                <td><a href="CustomerDetail.php?id=${encodeURIComponent(c.cus_id)}" class="btn btn-outline btn-sm">Inspect →</a></td>
              </tr>`;
          }).join('');
        }
      }

      // ── Activity Feed ──
      if (activityFeed) {
        const acts = d.recent_activities || [];
        if (!acts.length) {
          activityFeed.innerHTML = '<div style="padding:24px;opacity:.5;text-align:center;">No recent activity recorded.</div>';
        } else {
          const iconMap = { contract: '✓', deal: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>', quote: '📑', meeting: '🤝', call: '📞', email: '✉️' };
          activityFeed.innerHTML = acts.map(a => {
            const typeKey = (a.activity_type || '').toLowerCase();
            const icon    = iconMap[typeKey] || '●';
            return `
              <div class="activity-item">
                <div class="activity-icon-container ${escHtml(typeKey || 'deal')}">${icon}</div>
                <div class="activity-content">
                  <div class="activity-title">${escHtml(a.title || a.activity_type || 'Activity')}</div>
                  <div class="activity-desc">${escHtml(a.notes || a.description || '')}</div>
                  <span class="activity-timestamp">${ui.date(a.activity_date || a.created_at)} · ${escHtml(a.company_name || a.cus_id || '')}</span>
                </div>
              </div>`;
          }).join('');
        }
      }

    } catch (err) {
      console.warn('CRM Dashboard live fetch fallback to pre-rendered database data:', err);
      // Preserve pre-rendered content from PHP if already present
      if (kpiLeads && (!kpiLeads.textContent || kpiLeads.textContent.trim() === '')) kpiLeads.textContent = '—';
      if (kpiOpps && (!kpiOpps.textContent || kpiOpps.textContent.trim() === '')) kpiOpps.textContent = '—';
      if (kpiPipeline && (!kpiPipeline.textContent || kpiPipeline.textContent.trim() === '')) kpiPipeline.textContent = '—';
      if (kpiWinrate && (!kpiWinrate.textContent || kpiWinrate.textContent.trim() === '')) kpiWinrate.textContent = '—';
      if (funnelEl && !funnelEl.children.length) funnelEl.innerHTML = '<div style="padding:24px;color:#e74c3c;text-align:center;">Pipeline unavailable.</div>';
      if (accountsTbody && !accountsTbody.children.length) accountsTbody.innerHTML = '<tr><td colspan="5" style="color:#e74c3c;padding:24px;text-align:center;">Failed to load accounts.</td></tr>';
      if (activityFeed && !activityFeed.children.length) activityFeed.innerHTML = '<div style="padding:24px;color:#e74c3c;">Failed to load activity.</div>';
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
    loadDashboard();
    loadCustomers();
    initSectorFilters();
    loadLeads();
    loadOpportunities();
    loadForecasts();
    initSearch();
  });

})();
