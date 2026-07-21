// Verify the single-product template renders with all expected markers.
// Run: node pdp-check.mjs

import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = process.env.PW_BASE_URL || 'http://localhost:8090';
const URL_PATH = process.env.PW_PDP_PATH || '/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/';

const b = await chromium.launch();
const errors = [];
const failedResources = [];

async function check(label, viewport) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
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

  const res = await p.goto(BASE + URL_PATH, { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);

  const info = await p.evaluate(() => ({
    title: document.title,
    hasPdp: !!document.querySelector('.pdp'),
    hasBreadcrumb: !!document.querySelector('.shop-breadcrumb'),
    hasGallery: !!document.querySelector('.pdp-gallery'),
    hasThumbs: document.querySelectorAll('.pdp-gallery__thumb').length,
    hasHeroImage: !!document.querySelector('.pdp-gallery__image'),
    hasSummary: !!document.querySelector('.pdp-summary'),
    productTitle: (document.querySelector('.pdp-summary__title') || {}).textContent || '',
    hasPrice: !!document.querySelector('.pdp-summary__price'),
    hasAddToCart: !!document.querySelector('.pdp-summary__cart .single_add_to_cart_button'),
    accordionCount: document.querySelectorAll('.pdp-accordion').length,
    hasReviews: !!document.querySelector('.pdp-reviews'),
    hasRelated: !!document.querySelector('.pdp-related'),
    relatedCards: document.querySelectorAll('.pdp-related .product-card').length,
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }));

  await p.screenshot({ path: `${OUT}/pdp-${label}.png`, fullPage: true });
  console.log(`\n[${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  title: ${info.title}`);
  console.log(`  product: ${info.productTitle.trim()}`);
  console.log(`  pdp: ${info.hasPdp}  breadcrumb: ${info.hasBreadcrumb}  gallery: ${info.hasGallery} (${info.hasThumbs} thumbs, hero: ${info.hasHeroImage})`);
  console.log(`  summary: ${info.hasSummary}  price: ${info.hasPrice}  add-to-cart: ${info.hasAddToCart}  accordions: ${info.accordionCount}`);
  console.log(`  reviews: ${info.hasReviews}  related: ${info.hasRelated} (${info.relatedCards} cards)`);
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
