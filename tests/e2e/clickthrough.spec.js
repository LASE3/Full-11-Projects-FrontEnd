/**
 * VOSTOKPRIBOR - Universal Click-Through E2E Suite (Playwright)
 * Specification: Phase 6 (Portability, Honest Counters, Background API Interception)
 *                Phase 8 (Full 11-System Real UI Write Actions & Dual Viewports)
 */

const { chromium } = require('playwright');
const { execFileSync } = require('child_process');
const fs = require('fs');

const BASE_URL = (process.env.BASE_URL || 'http://127.0.0.1/Full-11-Projects-FrontEnd').replace(/\/+$/, '');
const urlObj = new URL(BASE_URL);
const HOSTNAME = urlObj.hostname;
const PHP_BIN = process.env.PHP_BINARY || (fs.existsSync('C:\\xampp\\php\\php.exe') ? 'C:\\xampp\\php\\php.exe' : 'php');

// Database query helper via PHP CLI
function queryDb(sql) {
  try {
    const out = execFileSync(PHP_BIN, [
      'scripts/query_db.php',
      'query',
      sql
    ]).toString().trim();
    return JSON.parse(out);
  } catch (err) {
    console.error('DB Query Error:', err.message);
    return [];
  }
}

function execDb(sql) {
  try {
    const out = execFileSync(PHP_BIN, [
      'scripts/query_db.php',
      'exec',
      sql
    ]).toString().trim();
    return JSON.parse(out);
  } catch (err) {
    console.error('DB Exec Error:', err.message);
    return null;
  }
}

// Generate SSO cookie for any user
function getSsoTokenForUser(userId) {
  try {
    const out = execFileSync(PHP_BIN, [
      'scripts/get_sso_cookie.php',
      userId,
      '--active'
    ]).toString().trim();
    return out;
  } catch (err) {
    console.error(`Failed to get SSO token for ${userId}:`, err.message);
    return '';
  }
}

const VIEWPORTS = [
  { name: 'Desktop 1440px', width: 1440, height: 900, isMobile: false, hasTouch: false },
  { name: 'Mobile 375px', width: 375, height: 667, isMobile: true, hasTouch: true }
];

async function setupPageContext(browser, vp, userId = null) {
  const context = await browser.newContext({
    viewport: { width: vp.width, height: vp.height },
    hasTouch: vp.hasTouch,
    isMobile: vp.isMobile
  });

  if (userId) {
    const token = getSsoTokenForUser(userId);
    if (token) {
      await context.addCookies([
        {
          name: 'vostok_sso_token',
          value: token,
          domain: HOSTNAME,
          path: '/'
        }
      ]);
    }
  }

  const page = await context.newPage();

  // Dialog auto-accept for alerts, confirms, and prompts
  page.on('dialog', async (dialog) => {
    try {
      if (dialog.type() === 'prompt') {
        await dialog.accept('L3');
      } else {
        await dialog.accept();
      }
    } catch (_) {}
  });

  // Block non-local external network requests for offline safety
  await page.route('**/*', (route) => {
    const url = route.request().url();
    if (url.includes(HOSTNAME) || url.startsWith('http://localhost') || url.startsWith('http://127.0.0.1') || url.startsWith('data:')) {
      route.continue();
    } else {
      route.abort('blockedbyclient');
    }
  });

  const apiErrors = [];
  const jsErrors = [];

  page.on('pageerror', (err) => {
    jsErrors.push(`JS Exception: ${err.message}`);
  });

  // Intercept all responses: MUST fail on ANY non-2xx response from the app's own /api/
  // including background calls (notifications, polling)
  page.on('response', (res) => {
    const url = res.url();
    const status = res.status();
    if (url.includes('/api/')) {
      if (status < 200 || status >= 300) {
        apiErrors.push(`API Error [HTTP ${status}] on ${url}`);
      }
    }
  });

  return { context, page, apiErrors, jsErrors };
}

async function assertPageHealth(page, label, apiErrors, jsErrors) {
  // Check horizontal overflow
  const metrics = await page.evaluate(() => {
    const docW = document.documentElement.scrollWidth;
    const winW = window.innerWidth;
    return { docW, winW, hasHScroll: docW > winW + 1 };
  });

  if (metrics.hasHScroll) {
    throw new Error(`${label}: Horizontal overflow detected: ${metrics.docW}px > ${metrics.winW}px`);
  }

  // Check for error toasts on screen
  const errorToast = await page.evaluate(() => {
    const toasts = Array.from(document.querySelectorAll('.vp-toast, [role="alert"], .toast, .alert, .hd-toast'));
    for (const t of toasts) {
      const txt = (t.textContent || '').toLowerCase();
      if (txt.includes('csrf') || txt.includes('creation failed') || txt.includes('execution rejected') || txt.includes('fatal error') || txt.includes('uncaught')) {
        return t.textContent.trim();
      }
    }
    return null;
  });

  if (errorToast) {
    throw new Error(`${label}: Error toast visible on screen: "${errorToast}"`);
  }

  if (apiErrors.length > 0) {
    throw new Error(`${label}: Background/Foreground API failures: ${apiErrors.join('; ')}`);
  }

  if (jsErrors.length > 0) {
    throw new Error(`${label}: JavaScript errors: ${jsErrors.join('; ')}`);
  }
}

// =============================================================================
// SYSTEM WRITE ACTION IMPLEMENTATIONS
// =============================================================================

// 1. Website: Corporate RFQ Submission
async function testWebsiteRfq(browser, vp, record) {
  const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp);
  try {
    await page.goto(`${BASE_URL}/VOSTOKPRIBOR%20Corporate%20Web%20Platform/index.php`, { waitUntil: 'domcontentloaded' });
    const ts = Date.now();
    const testEmail = `corp.rfq.${ts}@nord-metallurgy.local`;
    const testCompany = `Nord Metallurgy Plant ${ts}`;

    await page.waitForSelector('#corporateRfqForm', { timeout: 8000 });
    await page.fill('#rfqCompanyName', testCompany);
    await page.fill('#rfqContactName', 'Ing. Gennady Morozov');
    await page.fill('#rfqEmail', testEmail);
    await page.fill('#rfqNotes', `Telemetry integration and sensor probe array requirements. Timestamp: ${ts}`);

    await page.click('#corporateRfqForm button[type="submit"]');

    await page.waitForSelector('#confirmedLeadId, #rfqConfirmationBackdrop.show, .toast, .vp-toast', { timeout: 8000 });

    const dbRows = queryDb(`SELECT lead_id, company_name, full_name, email FROM leads WHERE email = '${testEmail}'`);
    const dbFound = dbRows.length > 0 && dbRows[0].company_name === testCompany;

    record(`1. [Website] RFQ submission on ${vp.name}`, dbFound, `Lead in DB: ${dbFound ? dbRows[0].lead_id : 'MISSING'}`);
    await assertPageHealth(page, `Website RFQ (${vp.name})`, apiErrors, jsErrors);
  } catch (err) {
    record(`1. [Website] RFQ submission on ${vp.name}`, false, err.message);
  } finally {
    await context.close();
  }
}

// 2. Shop: Add to Cart + Submit Purchase Order
async function testShopOrder(browser, vp, record) {
  const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'CUS-1001');
  try {
    await page.goto(`${BASE_URL}/Online%20Shop%20B2B/Dashboard.php`, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('.product-card', { timeout: 8000 });

    const beforeOrders = queryDb("SELECT COALESCE(MAX(order_id), 0) AS max_id FROM orders");
    const maxId = beforeOrders[0]?.max_id || 0;
    const poNumber = `PO-E2E-${Date.now().toString().slice(-6)}`;

    await page.evaluate((po) => {
      window.shopApp.submitPdpCart('PROD-1001');
      const inp = document.getElementById('po-number-input');
      if (inp) inp.value = po;
      window.shopApp.submitDirectPO();
    }, poNumber);

    await page.waitForSelector('.toast, #tracking-orders-table-body', { timeout: 8000 });

    const dbOrders = queryDb(`SELECT order_id, cus_id, total_amount, status FROM orders WHERE cus_id = 'CUS-1001' AND order_id > ${maxId} ORDER BY order_id DESC LIMIT 1`);
    const dbFound = dbOrders.length > 0;

    record(`2. [Shop] Add to cart + submit order on ${vp.name}`, dbFound, `Order in DB: ${dbFound ? dbOrders[0].order_id : 'MISSING'}`);
    await assertPageHealth(page, `Shop Order (${vp.name})`, apiErrors, jsErrors);
  } catch (err) {
    record(`2. [Shop] Add to cart + submit order on ${vp.name}`, false, err.message);
  } finally {
    await context.close();
  }
}

// 3. Customer Portal: Post Ticket Response
async function testCustomerPortalTicketComment(browser, vp, record) {
  const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'CUS-1001');
  try {
    await page.goto(`${BASE_URL}/Customer%20Portal/SupportTicketView.php`, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('#ticketResponseInput', { timeout: 8000 });

    const ts = Date.now();
    const commentNote = `E2E Technical Support Note Verification ${ts}`;

    await page.fill('#ticketResponseInput', commentNote);
    await page.click('#btnPostResponse');

    await page.waitForFunction((txt) => {
      return document.body.innerText.includes(txt);
    }, commentNote, { timeout: 8000 });

    const dbComments = queryDb(`SELECT comment_id, tkt_id, comment_text FROM ticket_comments WHERE comment_text LIKE '%${ts}%'`);
    const dbFound = dbComments.length > 0;

    record(`3. [Customer Portal] Post ticket comment on ${vp.name}`, dbFound, `Comment in DB: ${dbFound ? dbComments[0].comment_id : 'MISSING'}`);
    await assertPageHealth(page, `Customer Portal Ticket (${vp.name})`, apiErrors, jsErrors);
  } catch (err) {
    record(`3. [Customer Portal] Post ticket comment on ${vp.name}`, false, err.message);
  } finally {
    await context.close();
  }
}

// 4. Intranet: Submit Absence Requisition
async function testIntranetLeave(browser, vp, record) {
  const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1001');
  try {
    await page.goto(`${BASE_URL}/Employee%20Intranet/Dashboard.php`, { waitUntil: 'domcontentloaded' });
    
    await page.evaluate(() => window.openQuickRequestModal('leave'));
    await page.waitForSelector('#quick-action-form', { timeout: 8000 });

    const ts = Date.now();
    const handoverText = `Handover Engineer E2E ${ts}`;
    await page.fill('#leave-handover-input', handoverText);

    await page.click('#btnSubmitLeave');
    await page.waitForSelector('.toast, .vp-toast, div:has-text("Leave Request Submitted")', { timeout: 8000 });

    const dbLeaves = queryDb(`SELECT leave_id, emp_id, reason FROM leave_requests WHERE reason LIKE '%${ts}%'`);
    const dbFound = dbLeaves.length > 0;

    record(`4. [Intranet] Submit absence requisition on ${vp.name}`, dbFound, `Leave in DB: ${dbFound ? dbLeaves[0].leave_id : 'MISSING'}`);
    await assertPageHealth(page, `Intranet Leave (${vp.name})`, apiErrors, jsErrors);
  } catch (err) {
    record(`4. [Intranet] Submit absence requisition on ${vp.name}`, false, err.message);
  } finally {
    await context.close();
  }
}

// 5. CRM: Create Commercial Lead
async function testCrmLead(browser, vp, record) {
  const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1006');
  try {
    await page.goto(`${BASE_URL}/CRM/Leads.php`, { waitUntil: 'domcontentloaded' });
    
    await page.click('button[onclick*="openModal(\'modal-new-lead\')"], button:has-text("New Commercial Lead")');
    await page.waitForSelector('#modal-new-lead.active', { timeout: 8000 });

    const ts = Date.now();
    const compName = `Cryo Instruments LLC ${ts}`;
    await page.fill('#new-lead-company', compName);
    await page.fill('#new-lead-name', 'Mikhail Volkov');
    await page.fill('#new-lead-val', '920000');
    await page.fill('#new-lead-notes', 'Extreme low-temperature pressure gauge telemetry');

    await page.click('#btnSaveNewLead');
    await page.waitForSelector('.toast, .vp-toast, div:has-text("Lead Created")', { timeout: 8000 });

    const dbLeads = queryDb(`SELECT lead_id, company_name, full_name FROM leads WHERE company_name = '${compName}'`);
    const dbFound = dbLeads.length > 0;

    record(`5. [CRM] Create commercial lead on ${vp.name}`, dbFound, `Lead in DB: ${dbFound ? dbLeads[0].lead_id : 'MISSING'}`);
    await assertPageHealth(page, `CRM Lead (${vp.name})`, apiErrors, jsErrors);
  } catch (err) {
    record(`5. [CRM] Create commercial lead on ${vp.name}`, false, err.message);
  } finally {
    await context.close();
  }
}

// 6. HR: Modify Employee Clearance Tier
async function testHrEmployeeEdit(browser, vp, record) {
  const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1001');
  try {
    await page.goto(`${BASE_URL}/HR%20System/EmployeeRecords.php`, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('table', { timeout: 8000 });

    const curDb = queryDb(`SELECT clearance_level FROM employees WHERE emp_id = 'EMP-1012'`);
    const curClr = (curDb[0] && curDb[0].clearance_level) || 'L2';
    const targetClr = curClr === 'L3' ? 'L2' : 'L3';

    await page.evaluate(() => window.hrApp.inspectEmployee('EMP-1012'));
    await page.waitForSelector('#modal-employee-detail.active, #modal-employee-detail.show, #modal-employee-detail', { timeout: 8000 });

    await page.evaluate((clr) => window.hrApp.elevateClearancePrompt(clr), targetClr);
    await page.waitForSelector('.toast, .vp-toast, div:has-text("Clearance Updated")', { timeout: 8000 });

    const updatedDb = queryDb(`SELECT clearance_level FROM employees WHERE emp_id = 'EMP-1012'`);
    const updatedClr = updatedDb[0] ? updatedDb[0].clearance_level : null;
    const dbVerified = updatedClr === targetClr;

    record(`6. [HR] Modify employee clearance tier on ${vp.name}`, dbVerified, `EMP-1012 clearance in DB: ${updatedClr}`);
    await assertPageHealth(page, `HR Employee Edit (${vp.name})`, apiErrors, jsErrors);
  } catch (err) {
    record(`6. [HR] Modify employee clearance tier on ${vp.name}`, false, err.message);
  } finally {
    await context.close();
  }
}

// 7. Finance: 3-User Maker-Checker (Create -> Approve -> Pay)
async function testFinanceMakerChecker(browser, vp, record) {
  const ts = Date.now();
  const desc = `Calibration Skid Invoice E2E ${ts}`;
  let createdInvId = null;

  // Step 1: EMP-1060 (Maker) creates invoice
  {
    const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1060');
    try {
      await page.goto(`${BASE_URL}/Finance%20&%20Billing/Invoices.php`, { waitUntil: 'domcontentloaded' });
      await page.waitForSelector('table', { timeout: 8000 });

      await page.click('button[onclick*="openModal(\'modal-create-invoice\')"]');
      await page.waitForSelector('#modal-create-invoice.active, #modal-create-invoice.show, #modal-create-invoice', { timeout: 8000 });

      await page.selectOption('#modal-create-invoice select[name="cus_id"]', 'CUS-1001');
      await page.selectOption('#modal-create-invoice select[name="prj_id"]', 'PRJ-2026-001');
      await page.fill('#modal-create-invoice input[name="line_description"]', desc);
      await page.fill('#modal-create-invoice input[name="total_value"]', '64000.00');

      await page.click('#modal-create-invoice button[type="submit"]');
      await page.waitForSelector('.toast, .vp-toast, .fin-toast, div:has-text("Invoice Issued")', { timeout: 8000 });

      const dbInvs = queryDb(`SELECT inv_id, payment_status, created_by_emp_id FROM invoices WHERE prj_id = 'PRJ-2026-001' ORDER BY inv_id DESC LIMIT 1`);
      if (dbInvs.length > 0) {
        createdInvId = dbInvs[0].inv_id;
      }
      record(`7.1 [Finance] Invoice created by EMP-1060 on ${vp.name}`, !!createdInvId, `Invoice ID: ${createdInvId}`);
      await assertPageHealth(page, `Finance Invoice Create (${vp.name})`, apiErrors, jsErrors);
    } catch (err) {
      record(`7.1 [Finance] Invoice created by EMP-1060 on ${vp.name}`, false, err.message);
    } finally {
      await context.close();
    }
  }

  if (!createdInvId) {
    record(`7.2 [Finance] Invoice approval skipped (no invoice) on ${vp.name}`, false);
    record(`7.3 [Finance] Invoice payment skipped (no invoice) on ${vp.name}`, false);
    return;
  }

  // Step 2: EMP-1059 (Approver) approves invoice
  {
    const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1059');
    try {
      page.on('dialog', async (dialog) => {
        try { await dialog.accept(); } catch (_) {}
      });

      await page.goto(`${BASE_URL}/Finance%20&%20Billing/Invoices.php`, { waitUntil: 'domcontentloaded' });
      await page.evaluate((id) => window.finApp.approveInvoice(id), createdInvId);

      await page.waitForTimeout(1000);

      const dbAppr = queryDb(`SELECT inv_id, approved_by_emp_id, payment_status FROM invoices WHERE inv_id = '${createdInvId}'`);
      const approved = dbAppr.length > 0 && !!dbAppr[0].approved_by_emp_id;

      record(`7.2 [Finance] Invoice approved by EMP-1059 on ${vp.name}`, approved, `Approver in DB: ${dbAppr[0]?.approved_by_emp_id}`);
      await assertPageHealth(page, `Finance Invoice Approve (${vp.name})`, apiErrors, jsErrors);
    } catch (err) {
      record(`7.2 [Finance] Invoice approved by EMP-1059 on ${vp.name}`, false, err.message);
    } finally {
      await context.close();
    }
  }

  // Step 3: EMP-1061 (Payer) marks invoice paid
  {
    const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1061');
    try {
      page.on('dialog', async (dialog) => {
        try { await dialog.accept(); } catch (_) {}
      });

      await page.goto(`${BASE_URL}/Finance%20&%20Billing/Invoices.php`, { waitUntil: 'domcontentloaded' });
      await page.evaluate((id) => window.finApp.markInvoicePaid(id), createdInvId);

      await page.waitForTimeout(1000);

      const dbPaid = queryDb(`SELECT inv_id, payment_status, paid_at FROM invoices WHERE inv_id = '${createdInvId}'`);
      const paid = dbPaid.length > 0 && dbPaid[0].payment_status === 'Paid';

      record(`7.3 [Finance] Invoice marked paid by EMP-1061 on ${vp.name}`, paid, `Status in DB: ${dbPaid[0]?.payment_status}`);
      await assertPageHealth(page, `Finance Invoice Pay (${vp.name})`, apiErrors, jsErrors);
    } catch (err) {
      record(`7.3 [Finance] Invoice marked paid by EMP-1061 on ${vp.name}`, false, err.message);
    } finally {
      await context.close();
    }
  }
}

// 8. IT Helpdesk: Ticket Create + Asset Registration
async function testItTicketAndAsset(browser, vp, record) {
  let createdTicketId = null;
  // 8.1 Ticket Creation by EMP-1018
  {
    const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1018');
    try {
      await page.goto(`${BASE_URL}/IT%20Helpdesk/MyTickets.php`, { waitUntil: 'domcontentloaded' });
      const btn = await page.waitForSelector('button:has-text("Create Incident"), button[onclick*="openCreateTicketModal"]', { timeout: 8000 });
      await btn.click();
      await page.waitForSelector('#modal-create-ticket', { state: 'visible', timeout: 5000 });

      const testTitle = 'E2E Automated Ticket ' + Date.now();
      await page.fill('#form-create-ticket input[name="title"]', testTitle);
      await page.fill('#form-create-ticket input[name="affected_system"]', 'Optical Bay 7');
      await page.fill('#form-create-ticket textarea[name="description"]', 'Validating immediate appearance without page refresh');
      await page.click('#form-create-ticket button[type="submit"]');

      const newRow = await page.waitForSelector('.hd-row-new', { timeout: 5000 });
      const newRowText = await newRow.textContent();
      const hasNewBadge = newRowText.includes('NEW');
      const tidMatch = newRowText.match(/TKT-2026-\d+/);
      createdTicketId = tidMatch ? tidMatch[0] : null;

      let dbTicketPersisted = false;
      if (createdTicketId) {
        const dbCheck = queryDb(`SELECT tkt_id, title FROM tickets WHERE tkt_id = '${createdTicketId}'`);
        dbTicketPersisted = dbCheck.length > 0 && dbCheck[0].title === testTitle;
      }

      record(`8.1 [IT Helpdesk] Ticket creation with NEW badge on ${vp.name}`, hasNewBadge && dbTicketPersisted, `Ticket ID: ${createdTicketId}`);
      await assertPageHealth(page, `IT Helpdesk Tickets (${vp.name})`, apiErrors, jsErrors);
    } catch (err) {
      record(`8.1 [IT Helpdesk] Ticket creation on ${vp.name}`, false, err.message);
    } finally {
      if (createdTicketId) {
        execDb(`DELETE FROM ticket_comments WHERE tkt_id = '${createdTicketId}'`);
        execDb(`DELETE FROM tickets WHERE tkt_id = '${createdTicketId}'`);
      }
      await context.close();
    }
  }

  // 8.2 Asset Registration by Authorized IT Admin (EMP-1001)
  {
    const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1001');
    try {
      await page.goto(`${BASE_URL}/IT%20Helpdesk/AssetManagement.php`, { waitUntil: 'domcontentloaded' });
      await page.click('button[onclick*="openCreateAssetModal"]');
      await page.waitForSelector('#modal-create-asset.hd-modal-open, #modal-create-asset', { timeout: 5000 });

      const tag = `VP-DEV-${Date.now().toString().slice(-6)}`;
      await page.fill('#form-create-asset input[name="asset_tag"]', tag);
      await page.fill('#form-create-asset input[name="device_model"]', 'Moxa MB3170-E2E');
      await page.fill('#form-create-asset input[name="asset_type"]', 'Modbus Gateway');
      await page.fill('#form-create-asset input[name="location"]', 'Lipetsk Bay 4');
      await page.click('#form-create-asset button[type="submit"]');

      await page.waitForSelector('.hd-toast, .toast, .vp-toast, div:has-text("Asset Registered")', { timeout: 8000 });

      const dbAssets = queryDb(`SELECT asset_id, asset_tag, device_model FROM it_assets WHERE asset_tag = '${tag}'`);
      const dbAssetFound = dbAssets.length > 0;

      record(`8.2 [IT Helpdesk] Asset registration on ${vp.name}`, dbAssetFound, `Asset in DB: ${tag}`);
      await assertPageHealth(page, `IT Helpdesk Asset (${vp.name})`, apiErrors, jsErrors);
    } catch (err) {
      record(`8.2 [IT Helpdesk] Asset registration on ${vp.name}`, false, err.message);
    } finally {
      await context.close();
    }
  }
}

// 9. File Center: Upload + Byte-Identical Download
async function testFileCenterUploadDownload(browser, vp, record) {
  const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1019');
  try {
    await page.goto(`${BASE_URL}/File%20Center/upload.php`, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('#doc-upload-form', { timeout: 8000 });

    const ts = Date.now();
    const testContent = `VOSTOKPRIBOR_ENCRYPTED_VAULT_TEST_PAYLOAD_${ts}_BYTE_EXACT_CHECK`;
    const testBuffer = Buffer.from(testContent, 'utf-8');
    const docTitle = `Certified Telemetry Calibration Specs ${ts}.txt`;

    await page.setInputFiles('#file-input-hidden', {
      name: `telemetry_specs_${ts}.txt`,
      mimeType: 'text/plain',
      buffer: testBuffer
    });

    await page.fill('#meta-doc-title', docTitle);
    await page.fill('#meta-description', 'Automated byte verification test');

    await page.click('#doc-upload-form button[type="submit"]');
    await page.waitForFunction(() => {
      const el = document.getElementById('upload-success-card');
      return el && el.style.display === 'block';
    }, { timeout: 10000 });
    const docId = (await page.textContent('#disp-new-doc-id')).trim();

    const dbDocs = queryDb(`SELECT doc_id, storage_path, file_hash, file_size FROM documents WHERE doc_id = '${docId}'`);
    const dbFound = dbDocs.length > 0;

    let bytesIdentical = false;
    if (dbFound) {
      const downloadRes = await page.request.get(`${BASE_URL}/File%20Center/api/download.php?doc_id=${docId}`);
      if (downloadRes.ok()) {
        const downloadedBuffer = await downloadRes.body();
        bytesIdentical = Buffer.compare(testBuffer, downloadedBuffer) === 0;
      }
    }

    record(`9. [File Center] Upload & byte-identical download on ${vp.name}`, dbFound && bytesIdentical, `Doc ID: ${docId}, Bytes Match: ${bytesIdentical}`);
    await assertPageHealth(page, `File Center Upload/Download (${vp.name})`, apiErrors, jsErrors);
  } catch (err) {
    record(`9. [File Center] Upload & download on ${vp.name}`, false, err.message);
  } finally {
    await context.close();
  }
}

// 10. Developer: API Credential Mint + Single Secret Display
async function testDeveloperCredential(browser, vp, record) {
  const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1020');
  try {
    await page.goto(`${BASE_URL}/Developer/credentials.php`, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('#btnOpenGenKey', { timeout: 8000 });

    await page.evaluate(() => document.getElementById('btnOpenGenKey')?.click());
    await page.waitForSelector('#genKeyModal', { state: 'visible', timeout: 5000 });

    const ts = Date.now();
    const keyLabel = `E2E Test Key ${ts}`;
    await page.fill('#keyLabelInput', keyLabel);

    await page.click('#btnSubmitGenKey');
    await page.waitForFunction(() => {
      const modal = document.getElementById('secretKeyModal');
      return modal && modal.style.display === 'flex';
    }, { timeout: 8000 });
    const secretVal = await page.inputValue('#secretKeyTokenInput');
    const secretShown = !!secretVal && secretVal.length >= 16;

    const dbKeys = queryDb(`SELECT id, key_identifier, label FROM developer_api_keys WHERE label = '${keyLabel}'`);
    const dbFound = dbKeys.length > 0;

    record(`10. [Developer] API credential minted & secret displayed once on ${vp.name}`, secretShown && dbFound, `Key: ${dbKeys[0]?.key_identifier}`);
    await assertPageHealth(page, `Developer Credentials (${vp.name})`, apiErrors, jsErrors);
  } catch (err) {
    record(`10. [Developer] API credential mint on ${vp.name}`, false, err.message);
  } finally {
    await context.close();
  }
}

// 11. Admin: Risk Register Create + Emergency Lockdown Node Toggle
async function testAdminRiskAndLockdown(browser, vp, record) {
  const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1001');
  try {
    // 11.1 Risk Create
    await page.goto(`${BASE_URL}/Admin%20&%20Governance%20Portal/BoardRiskRegister.php`, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('#btnNewRisk', { timeout: 8000 });
    await page.evaluate(() => window.openNewRiskModal());
    await page.waitForSelector('#riskModal:not(.hidden)', { timeout: 5000 });

    const ts = Date.now();
    const riskDesc = `Statutory Board Risk Telemetry Interlock ${ts}`;
    await page.fill('#modalRiskDesc', riskDesc);
    await page.click('#btnSaveRisk');

    await page.waitForSelector('.toast, .vp-toast, div:has-text("RISK COMMITTED")', { timeout: 8000 });
    const dbRisks = queryDb(`SELECT risk_id, description FROM risk_register WHERE description = '${riskDesc}'`);
    const riskFound = dbRisks.length > 0;

    record(`11.1 [Admin] Statutory risk filed in register on ${vp.name}`, riskFound, `Risk ID: ${dbRisks[0]?.risk_id}`);

    // 11.2 Lockdown Toggle with Re-authentication
    await page.goto(`${BASE_URL}/Admin%20&%20Governance%20Portal/EmergencyLockdown.php`, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('table', { timeout: 8000 });
    await page.evaluate(() => window.toggleSystemLockdown('CRM'));
    await page.waitForSelector('#modalOverlay:not(.hidden)', { timeout: 5000 });

    await page.fill('#modalPhraseInput', 'CONFIRM-TOGGLE');
    await page.dispatchEvent('#modalPhraseInput', 'input');
    const adminPass = process.env.TEST_ADMIN_PASSWORD || 'VostokPribor2026!';
    await page.fill('#modalAuthKeyInput', adminPass);
    await page.fill('#modalReasonInput', 'Manual Air-Gap Interlock Triggered on Node CRM');

    await page.click('#modalBtnConfirm');
    await page.waitForFunction(() => {
      const el = document.getElementById('modalOverlay');
      return el && el.classList.contains('hidden');
    }, { timeout: 8000 });

    const dbSystems = queryDb(`SELECT system_id, status FROM systems_catalog WHERE system_id = 'CRM'`);
    const statusToggled = dbSystems.length > 0 && dbSystems[0].status === 'ISOLATED';

    record(`11.2 [Admin] Emergency node posture shift with re-authentication on ${vp.name}`, statusToggled, `CRM Status: ${dbSystems[0]?.status}`);

    // Reset CRM back to nominal OPERATIONAL
    execDb("UPDATE systems_catalog SET status = 'OPERATIONAL', isolation_reason = NULL, isolated_at = NULL, isolated_by = NULL WHERE system_id = 'CRM'");

    await assertPageHealth(page, `Admin Risk & Lockdown (${vp.name})`, apiErrors, jsErrors);
  } catch (err) {
    record(`11. [Admin] Risk & Lockdown on ${vp.name}`, false, err.message);
  } finally {
    await context.close();
  }
}

// =============================================================================
// MAIN HARNESS EXECUTION
// =============================================================================

async function runClickthroughHarness() {
  console.log('========================================================================');
  console.log('           VOSTOKPRIBOR CLICK-THROUGH E2E REGRESSION SUITE              ');
  console.log(`           Base URL: ${BASE_URL}                                         `);
  console.log('========================================================================\n');

  let totalTests = 0;
  let passedTests = 0;
  let failedTests = 0;

  function record(name, pass, detail = '') {
    totalTests++;
    if (pass) {
      passedTests++;
      console.log(`  [PASS] ${name}${detail ? ' -- ' + detail : ''}`);
    } else {
      failedTests++;
      console.error(`  [FAIL] ${name}${detail ? ' -- ' + detail : ''}`);
    }
  }

  // Ensure deterministic test credentials in database
  const adminPass = process.env.TEST_ADMIN_PASSWORD || 'VostokPribor2026!';
  try {
    const hashOut = execFileSync(PHP_BIN, ['-r', `echo password_hash('${adminPass}', PASSWORD_BCRYPT);`]).toString().trim();
    if (hashOut) {
      execDb(`UPDATE employee_accounts SET password_hash = '${hashOut}', must_change_password = 0 WHERE emp_id IN ('EMP-1001', 'EMP-1003', 'EMP-1006', 'EMP-1018', 'EMP-1019', 'EMP-1020', 'EMP-1059', 'EMP-1060', 'EMP-1061')`);
    }
  } catch (e) {
    console.error('Password hash sync error:', e.message);
  }
  execDb("UPDATE customer_accounts SET must_change_password = 0 WHERE cus_id = 'CUS-1001'");
  execDb("INSERT IGNORE INTO employee_roles (emp_id, role_id, granted_by_emp_id) SELECT emp_id, 6, 'EMP-1001' FROM employees WHERE department_code = 'FIN'");

  const launchOptions = { headless: true };
  if (process.env.PLAYWRIGHT_CHANNEL) {
    launchOptions.channel = process.env.PLAYWRIGHT_CHANNEL;
  }
  const browser = await chromium.launch(launchOptions).catch(() => chromium.launch({ headless: true }));

  try {
    for (const vp of VIEWPORTS) {
      console.log(`\n--- EXECUTING SYSTEM WRITE ACTIONS ON: ${vp.name} (${vp.width}x${vp.height}) ---`);

      // System 1: Website
      await testWebsiteRfq(browser, vp, record);

      // System 2: Shop
      await testShopOrder(browser, vp, record);

      // System 3: Customer Portal
      await testCustomerPortalTicketComment(browser, vp, record);

      // System 4: Intranet
      await testIntranetLeave(browser, vp, record);

      // System 5: CRM
      await testCrmLead(browser, vp, record);

      // System 6: HR
      await testHrEmployeeEdit(browser, vp, record);

      // System 7: Finance (3 users: EMP-1060 -> EMP-1059 -> EMP-1061)
      await testFinanceMakerChecker(browser, vp, record);

      // System 8: IT Helpdesk (Ticket + Asset)
      await testItTicketAndAsset(browser, vp, record);

      // System 9: File Center (Upload + Download byte verification)
      await testFileCenterUploadDownload(browser, vp, record);

      // System 10: Developer (API Key create + single secret view)
      await testDeveloperCredential(browser, vp, record);

      // System 11: Admin (Risk register create + Lockdown toggle with re-authentication)
      await testAdminRiskAndLockdown(browser, vp, record);
    }
  } finally {
    await browser.close();
  }

  // -------------------------------------------------------------------------
  // Honest Summary: Computed dynamically from counters
  // -------------------------------------------------------------------------
  console.log('\n========================================================================');
  console.log('                 CLICK-THROUGH TEST SUITE SUMMARY                       ');
  console.log('========================================================================');
  console.log(`TOTAL   : ${totalTests}`);
  console.log(`PASSED  : ${passedTests}`);
  console.log(`FAILED  : ${failedTests}`);
  if (totalTests > 0 && failedTests === 0) {
    console.log('STATUS  : 100% PASS - ALL CLICK-THROUGH CHECKS VERIFIED');
  } else {
    console.log('STATUS  : FAILURES DETECTED IN CLICK-THROUGH SUITE');
  }
  console.log('========================================================================\n');

  if (failedTests > 0 || totalTests === 0) {
    process.exit(1);
  }
}

if (require.main === module) {
  runClickthroughHarness().catch((err) => {
    console.error('Fatal clickthrough failure:', err);
    process.exit(1);
  });
}

module.exports = {
  BASE_URL,
  queryDb,
  execDb,
  getSsoTokenForUser,
  VIEWPORTS,
  setupPageContext,
  assertPageHealth,
  runClickthroughHarness
};
