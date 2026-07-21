// Close-up of every input in the theme so I can compare against the design.
import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/inputs', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();

async function shot(url, sel, label, opts = {}) {
  if (opts.addCart) await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
  await p.goto('http://localhost:8090' + url, { waitUntil: 'load', timeout: 25000 });
  await p.waitForTimeout(500);
  if (opts.click) await p.click(opts.click);
  await p.waitForTimeout(300);
  const el = await p.$(sel);
  if (!el) { console.log('miss', sel); return; }
  await el.scrollIntoViewIfNeeded();
  await p.waitForTimeout(200);
  const box = await el.boundingBox();
  if (!box || box.width < 4) { console.log('tiny', sel); return; }
  const pad = 30;
  await p.screenshot({
    path: `${OUT}/${label}.png`,
    clip: {
      x: Math.max(0, box.x - pad),
      y: Math.max(0, box.y - pad),
      width: Math.min(1280 - Math.max(0, box.x - pad), box.width + pad * 2),
      height: box.height + pad * 2,
    },
  });
  console.log(label, box.width + 'x' + Math.round(box.height));
}

await shot('/', '.search-modal__input', '01-search-modal-input', { click: '.header-search-pill' });
await shot('/checkout/', '#billing_email', '02-checkout-email', { addCart: true });
await shot('/checkout/', '#billing_first_name', '03-checkout-name');
await shot('/checkout/', '#billing_state', '04-checkout-state-select');
await shot('/cart/', '.dc-cart__coupon-input', '05-cart-coupon', { addCart: true });
await shot('/?s=bong', '#dc-search-input', '06-search-results-input');
await shot('/my-account/', '#username', '07-login-username');
await shot('/', '.newsletter-band input', '08-newsletter-email');

await b.close();
