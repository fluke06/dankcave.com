// Verify page, 404 and search templates render.
// Run: node static-check.mjs

import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = process.env.PW_BASE_URL || 'http://localhost:8090';

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

async function checkPage(url, viewport, label, marker) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  attach(p, label);
  const res = await p.goto(BASE + url, { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate((sel) => ({
    hasMarker: !!document.querySelector(sel),
    hasTitle: !!document.querySelector('h1'),
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }), marker);
  await p.screenshot({ path: `${OUT}/${label}.png`, fullPage: true });
  console.log(`\n[${label}]  HTTP ${res.status()}  overflow ${info.overflow}px  marker=${info.hasMarker}  h1=${info.hasTitle}`);
  await ctx.close();
}

// Sample "about" page (id 3 is typical). Fallback: any published page.
const aboutUrl = process.env.PW_ABOUT_URL || '/about/';

await checkPage(aboutUrl,          { viewport: { width: 1280, height: 900 } }, 'static-page-desktop', '.dc-page');
await checkPage(aboutUrl,          { ...devices['iPhone 13'], deviceScaleFactor: 2 }, 'static-page-mobile', '.dc-page');
await checkPage('/no-such-page',   { viewport: { width: 1280, height: 900 } }, 'static-404-desktop',  '.dc-404');
await checkPage('/no-such-page',   { ...devices['iPhone 13'], deviceScaleFactor: 2 }, 'static-404-mobile',   '.dc-404');
await checkPage('/?s=bong',        { viewport: { width: 1280, height: 900 } }, 'static-search-desktop', '.dc-search');
await checkPage('/?s=bong',        { ...devices['iPhone 13'], deviceScaleFactor: 2 }, 'static-search-mobile', '.dc-search');

await b.close();

console.log(`\nFailed resources (4xx/5xx): ${failedResources.length}`);
failedResources.slice(0, 15).forEach(r => console.log(' -', ...r));
console.log(`\nErrors: ${errors.length}`);
errors.slice(0, 5).forEach(e => console.log(' -', ...e));
process.exit(errors.length ? 1 : 0);
