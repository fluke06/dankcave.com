import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

// Sign in
await p.goto('http://localhost:8090/my-account/', { waitUntil: 'load' });
await p.waitForTimeout(400);
await p.fill('#username', 'qa-tester');
await p.fill('#password', 'QaTester!2026');
await Promise.all([ p.waitForNavigation({ waitUntil: 'load' }), p.click('button[name="login"]') ]);
await p.waitForTimeout(500);

// View the order (this uses the WC "view-order" endpoint)
await p.goto('http://localhost:8090/my-account/view-order/19069/', { waitUntil: 'load' });
await p.waitForTimeout(700);
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/finishing/05a-view-order.png', fullPage: true });
console.log('view-order URL:', p.url());

// Load the "order received" (thank-you) endpoint using the order key
const key = 'wc_order_icgtcxuEWxfiA';
await p.goto('http://localhost:8090/checkout/order-received/19069/?key=' + key, { waitUntil: 'load' });
await p.waitForTimeout(700);
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/finishing/05b-order-received.png', fullPage: true });
const info = await p.evaluate(() => ({
  h1: document.querySelector('h1')?.textContent?.trim(),
  thankYou: /thank you|received/i.test(document.body.innerText),
  orderNumberVisible: document.body.innerText.match(/#\d+/)?.[0],
  hasTable: !!document.querySelector('.woocommerce-order-details, table.shop_table, .dc-order-received'),
  hasCustomerDetails: !!document.querySelector('.woocommerce-customer-details, .dc-order-customer'),
  overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
}));
console.log('order-received:', JSON.stringify(info, null, 2));
if (errors.length) console.log('JS errors:', errors.slice(0, 3));
await b.close();
