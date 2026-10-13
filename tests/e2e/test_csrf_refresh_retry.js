const { chromium } = require('playwright');
const { execFileSync } = require('child_process');

async function testCsrfRefreshRetry() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  
  const ssoToken = execFileSync('c:\\xampp\\php\\php.exe', [
    'scripts/get_sso_cookie.php',
    'EMP-0001'
  ]).toString().trim();
  
  await context.addCookies([{
    name: 'vostok_sso_token',
    value: ssoToken,
    domain: '127.0.0.1',
    path: '/'
  }]);

  const page = await context.newPage();
  await page.goto('http://127.0.0.1/Full-11-Projects-FrontEnd/IT%20Helpdesk/Dashboard.php');

  // Intentionally tamper with CSRF token in meta tag and memory to simulate expired token
  const result = await page.evaluate(async () => {
    // Corrupt token
    window.__CSRF_TOKEN__ = 'EXPIRED_INVALID_TOKEN_1234567890abcdef';
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) meta.setAttribute('content', 'EXPIRED_INVALID_TOKEN_1234567890abcdef');
    
    // Now perform a mutating fetch call
    const res = await fetch('api/tickets.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        action: 'create',
        title: 'Auto Refresh CSRF Ticket Test',
        affected_system: 'Optical Bay 9',
        description: 'Verifying auto-refresh on 403 CSRF rejection'
      })
    });
    
    const body = await res.json();
    return {
      status: res.status,
      body,
      refreshedToken: window.__CSRF_TOKEN__
    };
  });

  console.log('Result of request with expired token:', JSON.stringify(result, null, 2));

  // Clean up created ticket if succeeded
  if (result.body && result.body.data && result.body.data.tkt_id) {
    const tktId = result.body.data.tkt_id;
    console.log(`Cleaning up test ticket ${tktId}...`);
    execFileSync('c:\\xampp\\php\\php.exe', ['scripts/query_db.php', 'exec', `DELETE FROM ticket_comments WHERE tkt_id = '${tktId}'`]);
    execFileSync('c:\\xampp\\php\\php.exe', ['scripts/query_db.php', 'exec', `DELETE FROM tickets WHERE tkt_id = '${tktId}'`]);
  }

  await browser.close();

  if (result.status === 200 && result.body.success) {
    console.log('[PASS] CSRF 403 transparent refresh and retry succeeded!');
  } else {
    throw new Error(`CSRF refresh failed with status ${result.status}`);
  }
}

testCsrfRefreshRetry().catch((err) => {
  console.error(err);
  process.exit(1);
});
