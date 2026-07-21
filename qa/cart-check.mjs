// Verify the cart template renders empty + populated correctly.
// Run: node cart-check.mjs

import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = process.env.PW_BASE_URL || 'http://localhost:8090';
const PRODUCT_ID = process.env.PW_PRODUCT_ID || '17009';

const b = await chromium.launch();
const errors = [];
const failedResources = [];

async function screenshot(page, label) {
  await page.screenshot({ path: `${OUT}/cart-${label}.png`, fullPage: true });
}

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

async function checkEmpty(viewport, label) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  attach(p, `empty-${label}`);
  const res = await p.goto(BASE + '/cart/', { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => ({
    hasEmpty: !!document.querySelector('.dc-cart-empty, .cart-empty'),
    hasReturnLink: !!document.querySelector('a[href*="/shop/"]'),
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }));
  await screenshot(p, `empty-${label}`);
  console.log(`\n[empty-${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  empty-state: ${info.hasEmpty}  return-link: ${info.hasReturnLink}`);
  await ctx.close();
}

async function checkFull(viewport, label) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  attach(p, `full-${label}`);

  // Prime the cart with an item via ?add-to-cart
  await p.goto(BASE + `/?add-to-cart=${PRODUCT_ID}`, { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);

  const res = await p.goto(BASE + '/cart/', { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => ({
    hasCart: !!document.querySelector('.dc-cart'),
    hasHeader: !!document.querySelector('.dc-cart__title'),
    hasBlurb: !!document.querySelector('.dc-cart__blurb'),
    lineCount: document.querySelectorAll('.dc-cart-line').length,
    hasQtyInput: !!document.querySelector('.dc-cart-line__qty input.qty'),
    hasRemoveLink: !!document.querySelector('.dc-cart-line__remove'),
    hasSummary: !!document.querySelector('.dc-summary-card'),
    hasTotal: !!document.querySelector('.dc-summary-card__row--total'),
    hasCheckoutCta: !!document.querySelector('.dc-summary-card__cta'),
    hasCoupon: !!document.querySelector('.dc-cart__coupon-input'),
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }));
  await screenshot(p, `full-${label}`);
  console.log(`\n[full-${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  cart: ${info.hasCart}  header: ${info.hasHeader}  blurb: ${info.hasBlurb}`);
  console.log(`  lines: ${info.lineCount}  qty-input: ${info.hasQtyInput}  remove: ${info.hasRemoveLink}  coupon: ${info.hasCoupon}`);
  console.log(`  summary: ${info.hasSummary}  total: ${info.hasTotal}  checkout-cta: ${info.hasCheckoutCta}`);
  await ctx.close();
}

await checkEmpty({ viewport: { width: 1280, height: 900 } }, 'desktop');
await checkFull({ viewport: { width: 1280, height: 900 } }, 'desktop');
await checkFull({ ...devices['iPhone 13'], deviceScaleFactor: 2 }, 'mobile');

await b.close();

console.log(`\nFailed resources (4xx/5xx): ${failedResources.length}`);
failedResources.slice(0, 15).forEach(r => console.log(' -', ...r));
console.log(`\nErrors: ${errors.length}`);
errors.slice(0, 5).forEach(e => console.log(' -', ...e));
process.exit(errors.length ? 1 : 0);
