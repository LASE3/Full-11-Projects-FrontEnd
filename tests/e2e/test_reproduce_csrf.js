const { chromium } = require('playwright');
const { execFileSync } = require('child_process');

async function testReproduce() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  
  // Seed SSO as SuperAdmin EMP-0001
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
  
  let postStatus = null;
  let postBody = null;
  page.on('response', async (res) => {
    if (res.url().includes('api/tickets.php') && res.request().method() === 'POST') {
      postStatus = res.status();
      postBody = await res.text();
      console.log(`POST api/tickets.php status: ${postStatus}`);
      console.log(`Response body: ${postBody}`);
    }
  });

  await page.goto('http://127.0.0.1/Full-11-Projects-FrontEnd/IT%20Helpdesk/Dashboard.php');
  console.log('Current page URL after goto:', page.url());
  
  // Wait for page to load
  await page.waitForSelector('button:has-text("+ Create Incident")');
  console.log('Clicking "+ Create Incident" button to open modal...');
  await page.click('button:has-text("+ Create Incident")');
  
  await page.waitForSelector('#form-create-ticket', { state: 'visible' });

  // Fill required fields
  await page.fill('#form-create-ticket input[name="title"]', 'Reproduce CSRF Failure Ticket');
  await page.fill('#form-create-ticket input[name="affected_system"]', 'Optical Bay 3');
  await page.fill('#form-create-ticket textarea[name="description"]', 'Testing CSRF rejection on submit button');
  
  // Click Submit Ticket to DB
  console.log('Clicking "Submit Ticket to DB" button...');
  await page.click('#form-create-ticket button[type="submit"]');
  
  // Wait for toast or response
  await page.waitForTimeout(2000);
  
  const toastText = await page.evaluate(() => {
    const toast = document.querySelector('.hd-toast, .vp-toast, [role="alert"]');
    return toast ? toast.textContent.trim() : null;
  });
  console.log('Toast visible on screen:', toastText);

  await browser.close();
}

testReproduce().catch(console.error);
