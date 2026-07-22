// Verify at 320px (smallest common) + shop cards visible via scroll.
import { chromium, devices } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/mobile-final', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();

// 320px — iPhone 5-era small screens
const ctx = await b.newContext({ viewport: { width: 320, height: 568 }, hasTouch: true, isMobile: true, userAgent: devices['iPhone SE'].userAgent });
const p = await ctx.newPage();

await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p.waitForTimeout(500);
// Scroll to the first product card so it's in view
const cardHandle = await p.$('.product-card');
if (cardHandle) {
  await cardHandle.scrollIntoViewIfNeeded();
  await p.waitForTimeout(300);
  await p.screenshot({ path: `${OUT}/shop-card-320.png`, fullPage: false });
  const info = await p.evaluate(() => {
    const a = document.querySelector('.product-card__hover-actions');
    const cs = a ? getComputedStyle(a) : {};
    const rect = a?.getBoundingClientRect();
    return {
      opacity: cs.opacity,
      pointerEvents: cs.pointerEvents,
      visible: rect && rect.width > 0 && rect.height > 0,
      btnCount: document.querySelectorAll('.product-card__hover-btn').length,
    };
  });
  console.log('shop card @320:', JSON.stringify(info));
}

// Compare modal at 320
await p.evaluate(() => {
  document.querySelectorAll('.product-card [data-dc-compare]').forEach((btn, i) => { if (i < 3) btn.click(); });
});
await p.waitForTimeout(500);
await p.click('[data-dc-compare-open]');
await p.waitForTimeout(1500);
await p.screenshot({ path: `${OUT}/compare-320.png`, fullPage: false });
const ov = await p.evaluate(() => {
  const modal = document.getElementById('dc-compare-modal');
  return Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth;
});
console.log('page overflow @320 with compare open:', ov);

// Check body scroll lock
const bodyLocked = await p.evaluate(() => document.body.classList.contains('dc-drawer-open'));
console.log('body locked when modal open:', bodyLocked);

// Quickview at 320
await p.click('button.dc-compare-modal__close');
await p.waitForTimeout(400);
await p.evaluate(async () => {
  const r = await fetch('/wp-admin/admin-ajax.php?action=dankcave_quickview&product_id=18135');
  const j = await r.json();
  const qv = document.getElementById('dc-quickview');
  qv.querySelector('[data-dc-quickview-body]').innerHTML = j.data.html;
  qv.hidden = false;
  qv.setAttribute('data-open', 'true');
  if (window.jQuery) window.jQuery(qv.querySelector('form.variations_form')).wc_variation_form();
});
await p.waitForTimeout(700);
await p.screenshot({ path: `${OUT}/quickview-320.png`, fullPage: false });

// Cart drawer at 320
await p.evaluate(() => {
  const qv = document.getElementById('dc-quickview');
  qv.hidden = true;
  qv.removeAttribute('data-open');
});
await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.waitForTimeout(400);
await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(400);
await p.click('.cart-summary');
await p.waitForTimeout(500);
await p.screenshot({ path: `${OUT}/cart-drawer-320.png`, fullPage: false });

await b.close();
console.log('done →', OUT);
