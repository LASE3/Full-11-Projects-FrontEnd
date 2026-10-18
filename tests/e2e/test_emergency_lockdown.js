const { chromium } = require('playwright');
const { execFileSync } = require('child_process');

async function testLockdown() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  
  const baseUrl = process.env.BASE_URL || 'http://127.0.0.1/Full-11-Projects-FrontEnd/';
  const urlObj = new URL(baseUrl);
  const phpBin = process.env.PHP_BINARY || 'php';

  // Login as SuperAdmin EMP-0001
  const ssoToken = execFileSync(phpBin, [
    'scripts/get_sso_cookie.php',
    'EMP-0001'
  ]).toString().trim();
  
  await context.addCookies([{
    name: 'vostok_sso_token',
    value: ssoToken,
    domain: urlObj.hostname,
    path: '/'
  }]);

  const page = await context.newPage();
  
  // Track network responses
  const responses = [];
  page.on('response', async (res) => {
    if (res.url().includes('api/lockdown.php') && res.request().method() === 'POST') {
      try {
        const text = await res.text();
        responses.push({ status: res.status(), text: JSON.parse(text) });
      } catch (e) {}
    }
  });

  await page.goto(baseUrl + 'Admin%20&%20Governance%20Portal/EmergencyLockdown.php');
  await page.waitForSelector('button[onclick*="toggleSystemLockdown"]');

  // TEST 1: INVALID CONFIRMATION PHRASE
  console.log('--- Test 1: Invalid Confirmation Phrase ---');
  // Open modal for CRM system (SYS05 or CRM)
  await page.evaluate(() => {
    const btn = document.querySelector("button[onclick*='CRM']") || document.querySelector("button[onclick*='SYS05']") || document.querySelectorAll("button[onclick*='toggleSystemLockdown']")[1];
    btn.click();
  });

  await page.waitForSelector('#modalOverlay:not(.hidden)');
  await page.fill('#modalPhraseInput', 'WRONG-PHRASE');
  await page.fill('#modalReasonInput', 'Automated security test quarantine drill');
  await page.fill('#modalAuthKeyInput', 'AdminPass2026!');
  
  // Enable confirm button if disabled
  await page.evaluate(() => {
    document.getElementById('modalBtnConfirm').disabled = false;
  });
  await page.click('#modalBtnConfirm');
  await page.waitForTimeout(1000);

  const lastResp = responses[responses.length - 1];
  console.log('Invalid phrase response:', lastResp);
  if (lastResp.status !== 422 || lastResp.text.error !== 'Confirmation phrase incorrect') {
    throw new Error(`Expected 'Confirmation phrase incorrect', got: ${JSON.stringify(lastResp)}`);
  }
  console.log('[PASS] Test 1: Invalid confirmation phrase returned exactly "Confirmation phrase incorrect" (no hints leaked).');

  // TEST 2: VALID PHRASE SUCCESS PATH
  console.log('\n--- Test 2: Valid Confirmation Phrase & System Isolation ---');
  await page.fill('#modalPhraseInput', 'CONFIRM-TOGGLE');
  await page.fill('#modalReasonInput', 'Live emergency containment drill for security audit');
  await page.fill('#modalAuthKeyInput', 'AdminPass2026!');
  await page.click('#modalBtnConfirm');
  await page.waitForTimeout(2000);

  const successResp = responses[responses.length - 1];
  console.log('Success response:', successResp);
  if (successResp.status !== 200 || !successResp.text.success) {
    throw new Error(`Lockdown execution failed: ${JSON.stringify(successResp)}`);
  }

  const isolatedSysId = successResp.text.system_id;
  console.log(`Isolated system: ${isolatedSysId}`);

  // Assert DB status in systems_catalog
  const dbCheck = JSON.parse(execFileSync(phpBin, [
    'scripts/query_db.php',
    'query',
    `SELECT system_id, status, isolation_reason, isolated_by FROM systems_catalog WHERE system_id = '${isolatedSysId}'`
  ]).toString().trim());
  console.log('DB systems_catalog row:', dbCheck[0]);
  if (dbCheck[0].status !== 'ISOLATED') {
    throw new Error(`Expected system ${isolatedSysId} status ISOLATED, got ${dbCheck[0].status}`);
  }

  // Assert security_events row written with ADM
  const secCheck = JSON.parse(execFileSync(phpBin, [
    'scripts/query_db.php',
    'query',
    `SELECT event_type, source_system_id, actor_emp_id FROM security_events WHERE event_type = 'NODE_ISOLATION_TOGGLED' ORDER BY event_id DESC LIMIT 1`
  ]).toString().trim());
  console.log('DB security_events row:', secCheck[0]);
  if (secCheck[0].source_system_id !== 'ADM') {
    throw new Error(`Expected source_system_id ADM, got ${secCheck[0].source_system_id}`);
  }

  // Assert in-place UI button update
  const btnText = await page.evaluate((sysId) => {
    const btn = document.querySelector(`button[onclick*="'${sysId}'"]`) || document.querySelector(`button[onclick*='"${sysId}"']`);
    return btn ? btn.textContent.trim() : null;
  }, isolatedSysId);
  console.log(`In-place button text on screen: "${btnText}"`);
  if (!btnText || !btnText.includes('ISOLATED')) {
    throw new Error(`Expected in-place button to say ISOLATED, got: "${btnText}"`);
  }
  console.log('[PASS] Test 2: System successfully isolated end-to-end, DB verified, UI updated in-place.');

  // TEST 3: RESTORE WORKS
  console.log('\n--- Test 3: System Restoration ---');
  // Click button again to toggle back to operational
  await page.evaluate((sysId) => {
    const btn = document.querySelector(`button[onclick*="'${sysId}'"]`) || document.querySelector(`button[onclick*='"${sysId}"']`);
    btn.click();
  }, isolatedSysId);

  await page.waitForSelector('#modalOverlay:not(.hidden)');
  await page.fill('#modalPhraseInput', 'CONFIRM-TOGGLE');
  await page.fill('#modalReasonInput', 'Containment drill completed - restoring node');
  await page.fill('#modalAuthKeyInput', 'AdminPass2026!');
  await page.evaluate(() => {
    document.getElementById('modalBtnConfirm').disabled = false;
  });
  await page.click('#modalBtnConfirm');
  await page.waitForTimeout(2000);

  const restoreResp = responses[responses.length - 1];
  console.log('Restore response:', restoreResp);
  if (restoreResp.status !== 200 || !restoreResp.text.success) {
    throw new Error(`Restore execution failed: ${JSON.stringify(restoreResp)}`);
  }

  const restoredDbCheck = JSON.parse(execFileSync(phpBin, [
    'scripts/query_db.php',
    'query',
    `SELECT system_id, status FROM systems_catalog WHERE system_id = '${isolatedSysId}'`
  ]).toString().trim());
  console.log('Restored DB row:', restoredDbCheck[0]);
  if (restoredDbCheck[0].status !== 'OPERATIONAL') {
    throw new Error(`Expected system ${isolatedSysId} status OPERATIONAL, got ${restoredDbCheck[0].status}`);
  }

  const restoredBtnText = await page.evaluate((sysId) => {
    const btn = document.querySelector(`button[onclick*="'${sysId}'"]`) || document.querySelector(`button[onclick*='"${sysId}"']`);
    return btn ? btn.textContent.trim() : null;
  }, isolatedSysId);
  console.log(`Restored button text on screen: "${restoredBtnText}"`);
  if (!restoredBtnText || !restoredBtnText.includes('ISOLATE NODE')) {
    throw new Error(`Expected button to say ISOLATE NODE, got: "${restoredBtnText}"`);
  }
  console.log('[PASS] Test 3: Restoration verified end-to-end in DB and screen.');

  // Clean up transient security events created during test
  execFileSync(phpBin, [
    'scripts/query_db.php',
    'exec',
    `DELETE FROM security_events WHERE description LIKE '%containment drill%'`
  ]);

  await browser.close();
  console.log('\n========================================================================');
  console.log('ALL PHASE 2 EMERGENCY LOCKDOWN TESTS PASSED!');
  console.log('========================================================================\n');
}

testLockdown().catch((err) => {
  console.error('Lockdown test error:', err);
  process.exit(1);
});
