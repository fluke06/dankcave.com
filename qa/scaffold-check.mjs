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
  p.on('console', m => { if (m.type() === 'error') errors.push([label, 'console.error', m.text().slice(0, 200)]); });
  p.on('pageerror', e => errors.push([label, 'pageerror', e.message.slice(0, 200)]));
  p.on('response', r => {
    if (r.status() >= 400) failedResources.push([label, r.status(), r.url()]);
  });
  const res = await p.goto(BASE + '/', { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => ({
    title: document.title,
    hasHeader: !!document.querySelector('.site-header'),
    hasFooter: !!document.querySelector('.site-footer'),
    hasBrand: !!document.querySelector('.site-brand'),
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
    bodyClasses: document.body.className.slice(0, 300),
  }));
  await p.screenshot({ path: `${OUT}/scaffold-${label}.png`, fullPage: true });
  console.log(`\n[${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  title: ${info.title}`);
  console.log(`  body.class: ${info.bodyClasses}`);
  console.log(`  header/footer/brand: ${info.hasHeader}/${info.hasFooter}/${info.hasBrand}`);
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
