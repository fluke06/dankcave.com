// Verify the empty theme scaffold loads without errors.
// Run: node scaffold-check.mjs

import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = process.env.PW_BASE_URL || 'http://localhost:8090';

const b = await chromium.launch();
const errors = [];
const failedResources = [];

async function check(label, viewport) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  p.on('console', m => {
    if (m.type() !== 'error') return;
    const t = m.text();
    // Legacy media 404s aren't theme-code problems; skip them.
    if (t.includes('the server responded with a status of 404')) return;
    errors.push([label, 'console.error', t.slice(0, 200)]);
  });
  p.on('pageerror', e => errors.push([label, 'pageerror', e.message.slice(0, 200)]));
  p.on('response', r => {
    if (r.status() < 400) return;
    // Ignore legacy media references in wp-content/uploads/ — those are files
    // that existed on the old site but weren't in the backup. Not theme problems.
    if (r.url().includes('/wp-content/uploads/')) return;
    failedResources.push([label, r.status(), r.url()]);
  });
  const res = await p.goto(BASE + '/', { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => ({
    title: document.title,
    hasHeader: !!document.querySelector('.site-header'),
    hasBrand: !!document.querySelector('.site-brand'),
    hasPrimaryNav: !!document.querySelector('.primary-nav'),
    hasCart: !!document.querySelector('.cart-summary'),
    hasNewsletter: !!document.querySelector('.newsletter-band'),
    hasLegalBar: !!document.querySelector('.legal-bar'),
    hasMobileToggle: !!document.querySelector('.site-header__toggle'),
    navItemCount: document.querySelectorAll('.primary-nav__list li').length,
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
    bodyClasses: document.body.className.slice(0, 300),
  }));
  await p.screenshot({ path: `${OUT}/scaffold-${label}.png`, fullPage: true });
  console.log(`\n[${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  title: ${info.title}`);
  console.log(`  body.class: ${info.bodyClasses}`);
  console.log(`  header: ${info.hasHeader}  brand: ${info.hasBrand}  nav: ${info.hasPrimaryNav} (${info.navItemCount} items)  cart: ${info.hasCart}  mobile-toggle: ${info.hasMobileToggle}`);
  console.log(`  footer: newsletter=${info.hasNewsletter}  legal-bar=${info.hasLegalBar}`);
  await ctx.close();
}

await check('desktop', { viewport: { width: 1280, height: 900 } });
await check('mobile', { ...devices['iPhone 13'], deviceScaleFactor: 2 });

await b.close();

console.log(`\nFailed resources (4xx/5xx): ${failedResources.length}`);
failedResources.slice(0, 15).forEach(r => console.log(' -', ...r));

console.log(`\nErrors: ${errors.length}`);
errors.slice(0, 5).forEach(e => console.log(' -', ...e));
process.exit(errors.length ? 1 : 0);
