/**
 * VOSTOKPRIBOR - Universal Click-Through E2E Suite (Playwright)
 * Specification: Phase 6 (Portability, Honest Counters, Background API Interception)
 *                Phase 8.1 (Immediate Ticket Injection Verification)
 */

const { chromium } = require('playwright');
const { execFileSync } = require('child_process');

const BASE_URL = (process.env.BASE_URL || 'http://127.0.0.1/Full-11-Projects-FrontEnd').replace(/\/+$/, '');
const urlObj = new URL(BASE_URL);
const HOSTNAME = urlObj.hostname;
const PHP_BIN = process.env.PHP_BINARY || 'php';

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
  // including background calls (notifications, polling) - Phase 6.3
  page.on('response', (res) => {
    const url = res.url();
    const status = res.status();
    if (url.includes('/api/')) {
      // Intended errors such as negative test probes are allowed if explicitly flagged,
      // but standard page operations and background calls MUST return 2xx (200-299)
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

  // Unlock test accounts from must_change_password forced redirection
  execDb("UPDATE employee_accounts SET must_change_password = 0 WHERE emp_id IN ('EMP-1001', 'EMP-1003', 'EMP-1018', 'EMP-1019', 'EMP-1020')");
  execDb("UPDATE customer_accounts SET must_change_password = 0 WHERE cus_id = 'CUS-1001'");

  const launchOptions = { headless: true };
  if (process.env.PLAYWRIGHT_CHANNEL) {
    launchOptions.channel = process.env.PLAYWRIGHT_CHANNEL;
  } else {
    launchOptions.channel = 'chrome';
  }
  const browser = await chromium.launch(launchOptions).catch(() => chromium.launch({ headless: true }));

  try {
    // -------------------------------------------------------------------------
    // Test 1: Launchpad responsiveness & zero background errors
    // -------------------------------------------------------------------------
    for (const vp of VIEWPORTS) {
      try {
        const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, vp, 'EMP-1001');
        await page.goto(`${BASE_URL}/index.php`);
        await page.waitForTimeout(500);
        await assertPageHealth(page, `Launchpad (${vp.name})`, apiErrors, jsErrors);
        record(`1. Launchpad clean render on ${vp.name}`, true);
        await context.close();
      } catch (err) {
        record(`1. Launchpad clean render on ${vp.name}`, false, err.message);
      }
    }

    // -------------------------------------------------------------------------
    // Test 2: IT Helpdesk - Ticket creation & immediate row appearance (Phase 8.1)
    // -------------------------------------------------------------------------
    let createdTicketId = null;
    try {
      const desktopVp = VIEWPORTS[0];
      const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, desktopVp, 'EMP-1018');
      
      await page.goto(`${BASE_URL}/IT%20Helpdesk/MyTickets.php`);
      console.log('Navigated to:', page.url());
      const btn = await page.waitForSelector('button:has-text("Create Incident"), button[onclick*="openCreateTicketModal"]', { timeout: 5000 });
      
      // Open modal
      await btn.click();
      await page.waitForSelector('#modal-create-ticket', { state: 'visible', timeout: 5000 });

      const testTitle = 'E2E Automated Worklist Test Ticket ' + Date.now();
      await page.fill('#form-create-ticket input[name="title"]', testTitle);
      await page.fill('#form-create-ticket input[name="affected_system"]', 'Optical Bay 7');
      await page.fill('#form-create-ticket textarea[name="description"]', 'Validating immediate appearance without page refresh');
      
      // Click submit
      await page.click('#form-create-ticket button[type="submit"]');
      await page.waitForTimeout(1000);

      // Verify that the new row appears immediately without refresh and has NEW indicator
      const newRow = await page.waitForSelector('.hd-row-new', { timeout: 3000 });
      const newRowText = await newRow.textContent();
      const hasNewBadge = newRowText.includes('NEW');
      const hasTitle = newRowText.includes(testTitle);
      
      record("2.1 IT My Tickets: New row appears immediately without refresh (Phase 8.1)", !!newRow && hasTitle);
      record("2.2 IT My Tickets: New row displays 'NEW' indicator badge", hasNewBadge);

      // Extract generated ticket ID
      const tidMatch = newRowText.match(/TKT-2026-\d+/);
      createdTicketId = tidMatch ? tidMatch[0] : null;

      // Assert database persistence
      if (createdTicketId) {
        const dbCheck = queryDb(`SELECT tkt_id, title FROM tickets WHERE tkt_id = '${createdTicketId}'`);
        const dbPersisted = dbCheck.length > 0 && dbCheck[0].title === testTitle;
        record(`2.3 IT Helpdesk: Ticket ${createdTicketId} verified in MariaDB`, dbPersisted);
      } else {
        record("2.3 IT Helpdesk: Ticket ID extraction", false, "No TKT-2026 ID found in new row");
      }

      await assertPageHealth(page, 'IT My Tickets Post-Create', apiErrors, jsErrors);
      record("2.4 IT My Tickets: Zero background API errors during create & poll", apiErrors.length === 0);

      await context.close();
    } catch (err) {
      record("2. IT My Tickets E2E Create Flow", false, err.message);
    } finally {
      // Clean up test ticket if created
      if (createdTicketId) {
        execDb(`DELETE FROM ticket_comments WHERE tkt_id = '${createdTicketId}'`);
        execDb(`DELETE FROM tickets WHERE tkt_id = '${createdTicketId}'`);
      }
    }

    // -------------------------------------------------------------------------
    // Test 3: Notification Bell background calls across systems (N1 verification)
    // -------------------------------------------------------------------------
    const testSystems = [
      { name: 'Admin Portal', path: 'Admin%20&%20Governance%20Portal/mainDashboard.php', user: 'EMP-1001' },
      { name: 'Developer Portal', path: 'Developer/Dashboard.php', user: 'EMP-1020' },
      { name: 'File Center', path: 'File%20Center/Dashboard.php', user: 'EMP-1019' },
      { name: 'Customer Portal', path: 'Customer%20Portal/Dashboard.php', user: 'CUS-1001' },
      { name: 'Finance & Billing', path: 'Finance%20&%20Billing/Dashboard.php', user: 'EMP-1003' },
    ];

    for (const sys of testSystems) {
      try {
        const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, VIEWPORTS[0], sys.user);
        await page.goto(`${BASE_URL}/${sys.path}`);
        await page.waitForTimeout(600);
        await assertPageHealth(page, sys.name, apiErrors, jsErrors);
        record(`3. ${sys.name}: Page render & background API calls clean (zero 5xx/fatals)`, true);
        await context.close();
      } catch (err) {
        record(`3. ${sys.name}: Page render & background API calls`, false, err.message);
      }
    }

    // -------------------------------------------------------------------------
    // Test 4: Developer Guides & Endpoints navigation
    // -------------------------------------------------------------------------
    try {
      const { context, page, apiErrors, jsErrors } = await setupPageContext(browser, VIEWPORTS[0], 'EMP-1020');
      await page.goto(`${BASE_URL}/Developer/guides.php`);
      await page.waitForSelector('body');
      await assertPageHealth(page, 'Developer Guides', apiErrors, jsErrors);
      record("4.1 Developer Guides page loads with zero background errors", true);

      await page.goto(`${BASE_URL}/Developer/endpoints.php`);
      await page.waitForSelector('body');
      await assertPageHealth(page, 'Developer Endpoints', apiErrors, jsErrors);
      record("4.2 Developer Endpoints page loads with zero background errors", true);
      await context.close();
    } catch (err) {
      record("4. Developer Guides & Endpoints", false, err.message);
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
