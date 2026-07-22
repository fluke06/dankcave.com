import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.waitForTimeout(400);
await p.goto('http://localhost:8090/checkout/', { waitUntil: 'load' });
await p.waitForTimeout(700);
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/patterns/checkout-no-shipping.png', fullPage: true });
const info = await p.evaluate(() => ({
  hasShippingCard: !!document.querySelector('.dc-checkout-card--shipping-method'),
  hasBillingCard: !!document.querySelector('.dc-checkout-card--billing'),
  hasShippingRow: !!Array.from(document.querySelectorAll('.dc-review__row span')).find(s => s.textContent.trim() === 'Shipping'),
}));
console.log(JSON.stringify(info));
await b.close();
