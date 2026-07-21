// Verify the checkout template renders with all expected markers.
// Run: node checkout-check.mjs

import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = process.env.PW_BASE_URL || 'http://localhost:8090';
const PRODUCT_ID = process.env.PW_PRODUCT_ID || '17009';

const b = await chromium.launch();
const errors = [];
const failedResources = [];

async function attach(p, label) {
  p.on('console', m => {
    if (m.type() !== 'error') return;
    const t = m.text();
    if (t.includes('the server responded with a status of 404')) return;
    errors.push([label, 'console.error', t.slice(0, 200)]);
  });
  p.on('pageerror', e => errors.push([label, 'pageerror', e.message.slice(0, 200)]));
  p.on('response', r => {
    if (r.status() < 400) return;
    if (r.url().includes('/wp-content/uploads/')) return;
    failedResources.push([label, r.status(), r.url()]);
  });
}

async function check(viewport, label) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  attach(p, label);

  await p.goto(BASE + `/?add-to-cart=${PRODUCT_ID}`, { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const res = await p.goto(BASE + '/checkout/', { waitUntil: 'networkidle', timeout: 30000 });
  await p.waitForTimeout(800);

  const info = await p.evaluate(() => ({
    hasCheckout: !!document.querySelector('.dc-checkout'),
    hasHeader: !!document.querySelector('.dc-checkout__title'),
    hasStepper: !!document.querySelector('.dc-checkout__steps'),
    stepperItems: document.querySelectorAll('.dc-checkout-step').length,
    hasForm: !!document.querySelector('form.woocommerce-checkout'),
    hasBilling: !!document.querySelector('.woocommerce-billing-fields'),
    hasShipping: !!document.querySelector('.woocommerce-shipping-fields'),
    hasAside: !!document.querySelector('.dc-checkout__aside'),
    hasReviewItems: document.querySelectorAll('.dc-review-item').length,
    hasTotals: !!document.querySelector('.dc-review__row--total'),
    hasPlaceOrder: !!document.querySelector('#place_order'),
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }));

  await p.screenshot({ path: `${OUT}/checkout-${label}.png`, fullPage: true });
  console.log(`\n[${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  checkout: ${info.hasCheckout}  header: ${info.hasHeader}  stepper: ${info.hasStepper} (${info.stepperItems} steps)`);
  console.log(`  form: ${info.hasForm}  billing: ${info.hasBilling}  shipping: ${info.hasShipping}`);
  console.log(`  aside: ${info.hasAside}  review-items: ${info.hasReviewItems}  totals: ${info.hasTotals}  place-order: ${info.hasPlaceOrder}`);
  await ctx.close();
}

await check({ viewport: { width: 1280, height: 900 } }, 'desktop');
await check({ ...devices['iPhone 13'], deviceScaleFactor: 2 }, 'mobile');

await b.close();

console.log(`\nFailed resources (4xx/5xx): ${failedResources.length}`);
failedResources.slice(0, 15).forEach(r => console.log(' -', ...r));
console.log(`\nErrors: ${errors.length}`);
errors.slice(0, 5).forEach(e => console.log(' -', ...e));
process.exit(errors.length ? 1 : 0);
