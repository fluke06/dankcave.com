import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/checkout-detail', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.goto('http://localhost:8090/checkout/', { waitUntil: 'networkidle', timeout: 30000 });
await p.waitForTimeout(800);

// Contact/shipping card close-up
const billing = await p.$('.dc-checkout-card--billing');
if (billing) {
  const box = await billing.boundingBox();
  await p.screenshot({ path: `${OUT}/billing-card.png`, clip: { x: box.x - 10, y: box.y - 10, width: Math.min(1280, box.width + 20), height: box.height + 20 } });
}
// Shipping method card close-up
const shipMethod = await p.$('.dc-checkout-card--shipping-method');
if (shipMethod) {
  await shipMethod.scrollIntoViewIfNeeded();
  await p.waitForTimeout(300);
  const box = await shipMethod.boundingBox();
  await p.screenshot({ path: `${OUT}/shipping-method-card.png`, clip: { x: box.x - 10, y: box.y - 10, width: Math.min(1280, box.width + 20), height: box.height + 20 } });
}
// Payment card close-up
await p.evaluate(() => window.scrollTo(0, 0));
await p.waitForTimeout(300);
const paymentEl = await p.$('.wc_payment_methods');
if (paymentEl) {
  await paymentEl.scrollIntoViewIfNeeded();
  await p.waitForTimeout(400);
  const box = await paymentEl.boundingBox();
  await p.screenshot({ path: `${OUT}/payment-card.png`, clip: { x: box.x - 10, y: box.y - 10, width: Math.min(1280 - box.x + 10, box.width + 20), height: box.height + 20 } });
}
// List which cards are on the page and their y-order
const info = await p.evaluate(() => {
  const cards = document.querySelectorAll('.dc-checkout-card, .wc_payment_methods');
  return Array.from(cards).map(c => ({
    cls: (c.className.match(/(dc-checkout-card--[a-z-]+|wc_payment_methods)/) || [])[0] || c.className.slice(0, 40),
    y: Math.round(c.getBoundingClientRect().y + window.scrollY),
    h: Math.round(c.getBoundingClientRect().height),
  })).sort((a, b) => a.y - b.y);
});
console.log(JSON.stringify(info, null, 2));

await b.close();
