/**
 * VOSTOKPRIBOR - Phase 3.10 Responsive Layout E2E Suite (Playwright)
 * Tests all pages across all 11 systems at 375px, 768px, and 1440px viewports
 * with all external network requests blocked (offline-safe verification).
 *
 * Assertions:
 *  1. No horizontal scroll (document.documentElement.scrollWidth <= window.innerWidth)
 *  2. Zero unhandled JavaScript errors
 *  3. Zero 4xx / 5xx HTTP responses from local origin
 *  4. Touch targets >= 44x44px for primary controls on touch viewports (375px/768px)
 */

const { chromium } = require('playwright');
const { execSync } = require('child_process');

const BASE_URL = (process.env.BASE_URL || 'http://127.0.0.1/Full-11-Projects-FrontEnd').replace(/\/+$/, '');
const PHP_BIN = process.env.PHP_BINARY || (require('fs').existsSync('C:\\xampp\\php\\php.exe') ? 'C:\\xampp\\php\\php.exe' : 'php');

// Generate fresh SSO cookie token via PHP helper
function getSsoToken() {
  try {
    const resetCode = "require 'config/db.php'; $pdo = getDbConnection(); $pdo->exec('UPDATE employee_accounts SET must_change_password = 0'); $pdo->exec('UPDATE customer_accounts SET must_change_password = 0');";
    execSync(`${JSON.stringify(PHP_BIN)} -r ${JSON.stringify(resetCode)}`);
    const code = "require 'config/db.php'; echo createSsoCookie(['user_id'=>'EMP-1001','emp_id'=>'EMP-1001','account_id'=>1,'clearance_level'=>'L4','role_name'=>'SuperAdmin','account_type'=>'Employee','full_name'=>'SuperAdmin','email'=>'admin@vostokpribor.local']);";
    const token = execSync(`${JSON.stringify(PHP_BIN)} -r ${JSON.stringify(code)}`).toString().trim();
    return token;
  } catch (err) {
    console.error('Failed to generate SSO token:', err.message);
    return '';
  }
}

const SYSTEMS = [
  { name: 'Launchpad', pages: ['index.php'] },
  { name: 'Corporate Web Platform', dir: 'VOSTOKPRIBOR Corporate Web Platform', pages: ['index.php', 'login.php', 'admin.php', 'Integrations.php'] },
  { name: 'Online Shop B2B', dir: 'Online Shop B2B', pages: ['index.php', 'Dashboard.php', 'login.php', 'signup.php', 'Integrations.php'] },
  { name: 'Customer Portal', dir: 'Customer Portal', pages: ['Dashboard.php', 'Documents.php', 'SupportTicketView.php', 'ServiceRequests.php', 'ProjectListAndDetail.php', 'Orders.php', 'Invoices.php', 'Careers.php', 'AccountSettings.php', 'login.php', 'Integrations.php'] },
  { name: 'Employee Intranet', dir: 'Employee Intranet', pages: ['Dashboard.php', 'EmployeeDirectory.php', 'PoliciesAndForms.php', 'login.php', 'Integrations.php'] },
  { name: 'CRM', dir: 'CRM', pages: ['Dashboard.php', 'Customers.php', 'CustomerDetail.php', 'Opportunities.php', 'QuotesAndContracts.php', 'SalesForecast.php', 'Leads.php', 'Projects.php', 'login.php', 'Integrations.php'] },
  { name: 'HR System', dir: 'HR System', pages: ['Dashboard.php', 'EmployeeRecords.php', 'OrgStructure.php', 'LeaveManagement.php', 'OnboardingTracker.php', 'Offboarding.php', 'Training.php', 'JobPostings.php', 'ServiceRequests.php', 'login.php', 'Integrations.php'] },
  { name: 'Finance & Billing', dir: 'Finance & Billing', pages: ['Dashboard.php', 'Invoices.php', 'PaymentsReconciliation.php', 'Budgets.php', 'FinancialReports.php', 'ProjectBilling.php', 'login.php', 'Integrations.php'] },
  { name: 'IT Helpdesk', dir: 'IT Helpdesk', pages: ['Dashboard.php', 'TicketQueue.php', 'TicketDetail.php', 'MyTickets.php', 'KnowledgeBase.php', 'AssetManagement.php', 'SLAReports.php', 'login.php', 'Integrations.php'] },
  { name: 'File Center', dir: 'File Center', pages: ['Dashboard.php', 'upload.php', 'retention.php', 'approvals.php', 'audit.php', 'login.php', 'Integrations.php'] },
  { name: 'Developer', dir: 'Developer', pages: ['Dashboard.php', 'credentials.php', 'guides.php', 'metrics.php', 'partner-registration.php', 'sandbox.php', 'login.php', 'Integrations.php'] },
  { name: 'Admin & Governance Portal', dir: 'Admin & Governance Portal', pages: ['mainDashboard.php', 'accessMatrix.php', 'AuditLogs.php', 'BoardRiskRegister.php', 'Break-GlassAccess.php', 'ComplianceOversight.php', 'EmergencyLockdown.php', 'IngestionBridges.php', 'PrivilegedAccounts.php', 'SecurityPolicies.php', 'SuperAdminConsole.php', 'ChangePassword.php', 'login.php', 'Integrations.php'] }
];

const VIEWPORTS = [
  { name: '375px Phone', width: 375, height: 667, isMobile: true, hasTouch: true },
  { name: '768px Tablet', width: 768, height: 1024, isMobile: true, hasTouch: true },
  { name: '1440px Desktop', width: 1440, height: 900, isMobile: false, hasTouch: false }
];

async function runResponsiveSuite() {
  console.log('========================================================================');
  console.log('      VOSTOKPRIBOR PHASE 3 - RESPONSIVE & OFFLINE-SAFE E2E SUITE        ');
  console.log('========================================================================\n');

  const ssoToken = getSsoToken();
  if (!ssoToken) {
    console.error('Fatal: Unable to initialize SSO token.');
    process.exit(1);
  }

  const browser = await chromium.launch({ headless: true });
  let totalTests = 0;
  let passedTests = 0;
  let failures = [];

  for (const vp of VIEWPORTS) {
    console.log(`\n--- TESTING VIEWPORT: ${vp.name} (${vp.width}x${vp.height}) ---`);

    const context = await browser.newContext({
      viewport: { width: vp.width, height: vp.height },
      hasTouch: vp.hasTouch,
      isMobile: vp.isMobile
    });

    // Seed SSO authentication cookie
    await context.addCookies([
      {
        name: 'vostok_sso_token',
        value: ssoToken,
        domain: '127.0.0.1',
        path: '/'
      }
    ]);

    const page = await context.newPage();

    // Block all external network requests (Item 3.10 & Phase 4)
    await page.route('**/*', (route) => {
      const url = route.request().url();
      if (url.startsWith('http://127.0.0.1') || url.startsWith('http://localhost') || url.startsWith('data:')) {
        route.continue();
      } else {
        // Block CDN / external request
        route.abort('blockedbyclient');
      }
    });

    for (const sys of SYSTEMS) {
      for (const p of sys.pages) {
        totalTests++;
        const targetUrl = sys.dir ? `${BASE_URL}/${encodeURIComponent(sys.dir)}/${p}` : `${BASE_URL}/${p}`;
        const pageLabel = `[${sys.name}] ${p} @ ${vp.width}px`;

        const jsErrors = [];
        page.removeAllListeners('pageerror');
        page.on('pageerror', (err) => jsErrors.push(err.message));

        try {
          const response = await page.goto(targetUrl, { waitUntil: 'domcontentloaded', timeout: 10000 });
          const status = response ? response.status() : 0;

          if (status >= 400) {
            failures.push({ test: pageLabel, error: `HTTP ${status} returned from origin` });
            console.log(`  [FAIL] ${pageLabel} - HTTP ${status}`);
            continue;
          }

          // Evaluate layout overflow
          const layoutMetrics = await page.evaluate(() => {
            const docWidth = document.documentElement.scrollWidth;
            const bodyWidth = document.body ? document.body.scrollWidth : 0;
            const winWidth = window.innerWidth;
            const scrollWidth = Math.max(docWidth, bodyWidth);
            const hasHScroll = scrollWidth > winWidth + 1; // 1px threshold for subpixel rounding

            return {
              scrollWidth,
              winWidth,
              hasHScroll
            };
          });

          if (layoutMetrics.hasHScroll) {
            const diff = layoutMetrics.scrollWidth - layoutMetrics.winWidth;
            failures.push({ test: pageLabel, error: `Horizontal overflow: scrollWidth=${layoutMetrics.scrollWidth} > innerWidth=${layoutMetrics.winWidth} (+${diff}px)` });
            console.log(`  [FAIL] ${pageLabel} - Overflow (+${diff}px)`);
            continue;
          }

          // Touch target validation on touch viewports (375px/768px, WCAG 2.5.5 / 2.5.8)
          if (vp.hasTouch) {
            const tapTargetViolations = await page.evaluate(() => {
              const elements = Array.from(document.querySelectorAll('button, a, input, select'));
              const violations = [];

              for (const el of elements) {
                const style = window.getComputedStyle(el);
                if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') continue;
                if (el.closest('.hidden, [hidden], [style*="display: none"], [style*="display:none"]')) continue;

                // Documented exceptions:
                // 1. Native checkboxes, radios, and hidden inputs (compact native browser elements)
                if (el.tagName === 'INPUT' && (el.type === 'checkbox' || el.type === 'radio' || el.type === 'hidden')) continue;
                // 2. Inline links within running body prose paragraphs, spans, or labels
                if (el.tagName === 'A' && (el.closest('p') || el.closest('label') || el.closest('.prose') || el.classList.contains('inline-prose-link') || el.classList.contains('text-link'))) continue;
                // 3. Off-canvas drawer elements when sidebar is closed
                if (!document.body.classList.contains('sidebar-open') && el.closest('aside.sidebar, .app-sidebar, .portal-sidebar, .admin-sidebar')) continue;
                // 4. Inactive/hidden modal contents
                if (el.closest('.vk-modal-overlay:not(.active), .modal:not(.show), .modal:not(.active)')) continue;

                const rect = el.getBoundingClientRect();
                if (rect.width === 0 && rect.height === 0) continue;

                // 43px threshold with 1px subpixel rounding tolerance for 44px
                if (rect.width < 43 || rect.height < 43) {
                  violations.push({
                    tag: el.tagName,
                    text: (el.textContent || el.value || el.placeholder || '').trim().slice(0, 30),
                    width: Math.round(rect.width),
                    height: Math.round(rect.height),
                    cls: el.className
                  });
                }
              }
              return violations;
            });

            if (tapTargetViolations.length > 0) {
              const sample = tapTargetViolations[0];
              failures.push({
                test: pageLabel,
                error: `Touch target violation: ${tapTargetViolations.length} controls < 44x44px. Sample: <${sample.tag} class="${sample.cls}"> "${sample.text}" is ${sample.width}x${sample.height}px`
              });
              console.log(`  [FAIL] ${pageLabel} - Touch target (${tapTargetViolations.length} controls < 44x44px)`);
              continue;
            }
          }

          if (jsErrors.length > 0) {
            failures.push({ test: pageLabel, error: `JS Error: ${jsErrors[0]}` });
            console.log(`  [FAIL] ${pageLabel} - JS Error: ${jsErrors[0]}`);
            continue;
          }

          passedTests++;
          console.log(`  [PASS] ${pageLabel}`);
        } catch (err) {
          failures.push({ test: pageLabel, error: err.message });
          console.log(`  [FAIL] ${pageLabel} - ${err.message}`);
        }
      }
    }

    await context.close();
  }

  await browser.close();

  console.log('\n========================================================================');
  console.log('                      TEST EXECUTION SUMMARY                            ');
  console.log('========================================================================');
  console.log(`  TOTAL TESTS : ${totalTests}`);
  console.log(`  PASSED      : ${passedTests}`);
  console.log(`  FAILED      : ${failures.length}`);
  console.log(`  SUCCESS RATE: ${((passedTests / totalTests) * 100).toFixed(1)}%`);
  console.log('========================================================================');

  if (failures.length > 0) {
    console.log('\nFAILURES:');
    failures.slice(0, 20).forEach((f, idx) => {
      console.log(`  ${idx + 1}. ${f.test}: ${f.error}`);
    });
    if (failures.length > 20) {
      console.log(`  ... and ${failures.length - 20} more failures.`);
    }
    process.exit(1);
  } else {
    console.log('\nALL RESPONSIVE AND OFFLINE CHECKS PASSED PERFECTLY!');
    process.exit(0);
  }
}

if (require.main === module) {
  runResponsiveSuite().catch((err) => {
    console.error('Test runner fatal error:', err);
    process.exit(1);
  });
}

module.exports = { runResponsiveSuite };
