import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();

await p.goto('http://localhost:8090/my-account/', { waitUntil: 'load' });
await p.waitForTimeout(400);
await p.fill('#username', 'qa-tester');
await p.fill('#password', 'QaTester!2026');
await Promise.all([ p.waitForNavigation({ waitUntil: 'load' }).catch(() => {}), p.click('button[name="login"]') ]);
await p.waitForTimeout(700);

console.log('logged in:', await p.evaluate(() => document.body.classList.contains('logged-in')));

// Check what /edit-address/ shows
await p.goto('http://localhost:8090/my-account/edit-address/', { waitUntil: 'load' });
await p.waitForTimeout(500);
const linksOnEditAddress = await p.$$eval('.woocommerce-Address a, .u-column1 a, .u-column2 a, main a', els => els.slice(0, 15).map(e => ({ text: e.textContent.trim().slice(0, 40), href: e.href })));
console.log('links on edit-address:', JSON.stringify(linksOnEditAddress, null, 2));

// Try the billing URL
await p.goto('http://localhost:8090/my-account/edit-address/billing/', { waitUntil: 'load' });
await p.waitForTimeout(700);
const forms = await p.$$eval('form', fs => fs.map(f => ({ id: f.id, cls: f.className.slice(0, 60), fieldCount: f.querySelectorAll('input, select, textarea').length })));
console.log('forms on billing edit:', JSON.stringify(forms, null, 2));
console.log('logged in on billing edit:', await p.evaluate(() => document.body.classList.contains('logged-in')));
console.log('URL now:', p.url());

await b.close();
