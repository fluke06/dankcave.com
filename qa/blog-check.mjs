// Verify blog index + single post render.
// Run: node blog-check.mjs

import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = process.env.PW_BASE_URL || 'http://localhost:8090';
const POST_ID = process.env.PW_POST_ID || '17726';

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

async function checkIndex(viewport, label) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  attach(p, `index-${label}`);
  const res = await p.goto(BASE + '/blog/', { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => ({
    hasBlog: !!document.querySelector('.dc-blog'),
    hasTitle: !!document.querySelector('.dc-blog__title'),
    hasIntro: !!document.querySelector('.dc-blog__intro'),
    hasChips: !!document.querySelector('.dc-blog__chips'),
    chipCount: document.querySelectorAll('.dc-blog__chip').length,
    hasFeatured: !!document.querySelector('.dc-blog-featured'),
    gridCards: document.querySelectorAll('.dc-blog__grid .blog-card').length,
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }));
  await p.screenshot({ path: `${OUT}/blog-index-${label}.png`, fullPage: true });
  console.log(`\n[index-${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  blog: ${info.hasBlog}  title: ${info.hasTitle}  intro: ${info.hasIntro}`);
  console.log(`  chips: ${info.hasChips} (${info.chipCount})  featured: ${info.hasFeatured}  grid-cards: ${info.gridCards}`);
  await ctx.close();
}

async function checkSingle(viewport, label) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  attach(p, `single-${label}`);
  const res = await p.goto(BASE + '/?p=' + POST_ID, { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => ({
    hasPost: !!document.querySelector('.dc-post'),
    hasHero: !!document.querySelector('.dc-post__hero'),
    hasTitle: !!document.querySelector('.dc-post__title'),
    hasBody: !!document.querySelector('.dc-post__body'),
    hasRelated: !!document.querySelector('.dc-post-related'),
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }));
  await p.screenshot({ path: `${OUT}/blog-single-${label}.png`, fullPage: true });
  console.log(`\n[single-${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  post: ${info.hasPost}  hero: ${info.hasHero}  title: ${info.hasTitle}  body: ${info.hasBody}  related: ${info.hasRelated}`);
  await ctx.close();
}

await checkIndex({ viewport: { width: 1280, height: 900 } }, 'desktop');
await checkIndex({ ...devices['iPhone 13'], deviceScaleFactor: 2 }, 'mobile');
await checkSingle({ viewport: { width: 1280, height: 900 } }, 'desktop');
await checkSingle({ ...devices['iPhone 13'], deviceScaleFactor: 2 }, 'mobile');

await b.close();

console.log(`\nFailed resources (4xx/5xx): ${failedResources.length}`);
failedResources.slice(0, 15).forEach(r => console.log(' -', ...r));
console.log(`\nErrors: ${errors.length}`);
errors.slice(0, 5).forEach(e => console.log(' -', ...e));
process.exit(errors.length ? 1 : 0);
