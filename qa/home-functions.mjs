// Home / front page: comprehensive function audit.
// Checks hero (headline + CTA), pick-your-poison product row, shop-by-category
// tile grid, editorial band with video/overlay, popular+trending row, new
// products row, blog row (3-up), trust band stats. Also verifies section
// heads have "See all →" links, product cards work identically to shop
// archive, and mobile stacks cleanly.
import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots/home-fn', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = 'http://localhost:8090';

const results = [];
function pass(g, m) { results.push({ g, s: 'PASS', m }); process.stdout.write('✓'); }
function fail(g, m) { results.push({ g, s: 'FAIL', m }); process.stdout.write('✗'); }
function note(g, m) { results.push({ g, s: 'NOTE', m }); process.stdout.write('·'); }

const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push({ url: p.url(), msg: e.message }));
async function shot(n) { await p.screenshot({ path: `${OUT}/${n}.png` }).catch(() => {}); }
async function fullShot(n) { await p.screenshot({ path: `${OUT}/${n}.png`, fullPage: true }).catch(() => {}); }

// ============================================================================
// PAGE LOAD
// ============================================================================
async function testLoad() {
  const g = 'load';
  const resp = await p.goto(BASE + '/', { waitUntil: 'load' });
  await p.waitForTimeout(1000);
  // Trigger lazy load images
  await p.evaluate(async () => {
    const step = 500;
    const total = document.body.scrollHeight;
    for (let y = 0; y <= total; y += step) {
      window.scrollTo(0, y);
      await new Promise(r => setTimeout(r, 80));
    }
    window.scrollTo(0, 0);
    await Promise.all(Array.from(document.querySelectorAll('img[data-src]')).map(img => new Promise(res => {
      if (img.dataset.src) img.src = img.dataset.src;
      if (img.complete) return res();
      img.onload = img.onerror = res;
    })));
  });
  await p.waitForTimeout(600);
  resp.status() === 200 ? pass(g, 'home returns 200') : fail(g, `status ${resp.status()}`);
  const ov = await p.evaluate(() => Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth);
  ov <= 0 ? pass(g, 'no horizontal overflow') : fail(g, `overflow ${ov}px`);
  await fullShot('01-home-full');
}

// ============================================================================
// HERO
// ============================================================================
async function testHero() {
  const g = 'hero';
  const hero = await p.$('.hero');
  hero ? pass(g, 'hero section present') : fail(g, 'hero missing');

  // Display headline
  const h1 = await p.$eval('.hero h1', el => el.textContent.trim()).catch(() => null);
  h1 ? pass(g, `H1: "${h1.slice(0, 60)}${h1.length > 60 ? '…' : ''}"`) : fail(g, 'no hero H1');

  // CTA
  const cta = await p.$('.hero__cta, .hero__cta-block, .hero a[href*="/shop"]');
  cta ? pass(g, 'CTA present') : fail(g, 'CTA missing');

  // Hero background/image should be eager-loaded for LCP
  const heroImg = await p.evaluate(() => {
    const img = document.querySelector('.hero img, .hero__product img, .hero__glow img');
    return img ? { loading: img.getAttribute('loading'), fetchpriority: img.getAttribute('fetchpriority'), src: img.src.split('/').pop() } : null;
  });
  if (heroImg) {
    heroImg.loading !== 'lazy' ? pass(g, `hero image eager (${heroImg.src})`) : note(g, `hero image lazy — LCP risk (${heroImg.src})`);
  } else {
    note(g, 'no hero image found (CSS background?)');
  }

  await shot('02-hero');
}

// ============================================================================
// SECTION HEADS
// ============================================================================
async function testSectionHeads() {
  const g = 'section-heads';
  const heads = await p.$$eval('.section-head', els => els.map(e => ({
    title: e.querySelector('.section-head__title')?.textContent?.trim(),
    link: e.querySelector('.section-head__link')?.getAttribute('href'),
  })));
  heads.length > 0 ? pass(g, `${heads.length} section heads: ${heads.map(h => h.title).join(' · ')}`) : fail(g, 'no section heads');
  const linked = heads.filter(h => h.link).length;
  linked === heads.length ? pass(g, 'all section heads have "see all" links') : note(g, `${linked}/${heads.length} have links`);
}

// ============================================================================
// PICK YOUR POISON / POPULAR & TRENDING / NEW PRODUCTS — product rows
// ============================================================================
async function testProductRows() {
  const g = 'product-rows';
  const grids = await p.$$('.product-grid');
  grids.length >= 3 ? pass(g, `${grids.length} product grids (expected ≥3)`) : fail(g, `only ${grids.length} product grids`);

  const totalCards = await p.$$eval('.product-grid .product-card', els => els.length);
  totalCards > 0 ? pass(g, `${totalCards} product cards across all rows`) : fail(g, 'no product cards in any row');

  // Each grid should have at least 4 cards
  const perGrid = await p.$$eval('.product-grid', grids => grids.map(g => g.querySelectorAll('.product-card').length));
  const under = perGrid.filter(n => n < 4).length;
  under === 0 ? pass(g, `each row has ≥4 cards (${perGrid.join(', ')})`) : note(g, `some rows short: ${perGrid.join(', ')}`);
}

// ============================================================================
// SHOP BY CATEGORY tile grid
// ============================================================================
async function testCategoryGrid() {
  const g = 'category-grid';
  const grid = await p.$('.category-grid');
  grid ? pass(g, 'category grid present') : fail(g, 'category grid missing');

  const tiles = await p.$$('.category-tile');
  tiles.length >= 4 ? pass(g, `${tiles.length} category tiles`) : fail(g, `only ${tiles.length} tiles`);

  const withImgs = await p.$$eval('.category-tile', els => els.filter(t => t.querySelector('img')).length);
  if (withImgs === tiles.length) pass(g, 'all tiles have images');
  else note(g, `${withImgs}/${tiles.length} tiles have images — remaining categories need thumbnails uploaded in WP admin`);

  const withLinks = await p.$$eval('.category-tile', els => els.filter(t => t.closest('a') || t.querySelector('a') || t.tagName === 'A').length);
  withLinks === tiles.length ? pass(g, 'all tiles link out') : fail(g, `${withLinks}/${tiles.length} link out`);

  const withCount = await p.$$eval('.category-tile__count', els => els.length);
  withCount > 0 ? pass(g, `${withCount} tiles show product count`) : note(g, 'no product-count badges');
}

// ============================================================================
// EDITORIAL BAND (dark video/image with overlay + CTA)
// ============================================================================
async function testEditorialBand() {
  const g = 'editorial-band';
  const band = await p.$('.editorial-band');
  band ? pass(g, 'editorial band present') : fail(g, 'editorial band missing');

  const heading = await p.$eval('.editorial-band__heading', el => el.textContent.trim()).catch(() => null);
  heading ? pass(g, `heading: "${heading.slice(0, 60)}${heading.length > 60 ? '…' : ''}"`) : fail(g, 'no heading');

  const cta = await p.$('.editorial-band__cta');
  cta ? pass(g, 'CTA present') : fail(g, 'CTA missing');

  const video = await p.$('.editorial-band__video, .editorial-band video');
  video ? pass(g, 'video/image asset present') : note(g, 'no video element (may be CSS bg)');
}

// ============================================================================
// BLOG ROW (3-up)
// ============================================================================
async function testBlogRow() {
  const g = 'blog-row';
  const row = await p.$('.blog-row');
  if (!row) { fail(g, 'no blog row'); return; }
  pass(g, 'blog row present');

  const posts = await p.$$('.blog-row article, .blog-row .blog-card, .blog-row a[href*="/blog/"], .blog-row .wp-block-post');
  posts.length >= 1 ? pass(g, `${posts.length} blog posts in row`) : fail(g, 'no blog posts in row');
}

// ============================================================================
// TRUST BAND (stats)
// ============================================================================
async function testTrustBand() {
  const g = 'trust-band';
  const band = await p.$('.home-trust');
  band ? pass(g, 'trust band present') : fail(g, 'trust band missing');

  const stats = await p.$$('.home-trust__stat');
  stats.length >= 3 ? pass(g, `${stats.length} stats`) : fail(g, `only ${stats.length} stats`);

  const values = await p.$$eval('.home-trust__stat-value', els => els.map(e => e.textContent.trim()));
  values.length > 0 ? pass(g, `stat values: ${values.join(' · ')}`) : note(g, 'no stat values');
}

// ============================================================================
// INTERACTIVE — click through from home to product / category
// ============================================================================
async function testNav() {
  const g = 'nav';
  await p.goto(BASE + '/', { waitUntil: 'load' });
  await p.waitForTimeout(500);

  // First hero CTA leads somewhere (the anchor is .hero__cta inside .hero__cta-block)
  const heroCtaHref = await p.evaluate(() => {
    const a = document.querySelector('.hero a[href]');
    return a ? a.getAttribute('href') : null;
  });
  heroCtaHref ? pass(g, `hero CTA → ${heroCtaHref}`) : note(g, 'no hero anchor with href');

  // Category tile navigates
  const firstTile = await p.$('.category-tile');
  if (firstTile) {
    const href = await firstTile.evaluate(el => el.getAttribute('href') || el.closest('a')?.getAttribute('href'));
    href ? pass(g, `first category tile → ${href}`) : note(g, 'first category tile has no href');
  }

  // Section-head "see all" link
  const seeAll = await p.$('.section-head__link');
  if (seeAll) {
    const href = await seeAll.evaluate(el => el.getAttribute('href'));
    href ? pass(g, `see-all link → ${href}`) : fail(g, 'see-all link has no href');
  }
}

// ============================================================================
// PRODUCT CARD ADD-TO-CART FROM HOME (verify same as shop)
// ============================================================================
async function testCardAdd() {
  const g = 'card-add';
  await p.goto(BASE + '/', { waitUntil: 'load' });
  await p.waitForTimeout(700);
  const before = await p.evaluate(() => Number(document.querySelector('[data-cart-count]')?.textContent) || 0);

  const btn = await p.$('.product-card__add:not(.product-card__add--needs-options)');
  if (!btn) { note(g, 'no simple product add button on home'); return; }
  await btn.click();
  await p.waitForTimeout(1500);

  const after = await p.evaluate(() => Number(document.querySelector('[data-cart-count]')?.textContent) || 0);
  after > before ? pass(g, `cart updated ${before}→${after}`) : fail(g, `cart unchanged ${before}=${after}`);

  const drawer = await p.evaluate(() => document.getElementById('dc-cart-drawer')?.getAttribute('data-open') === 'true');
  drawer ? pass(g, 'cart drawer opens') : fail(g, 'drawer did not open');

  await p.evaluate(() => document.querySelector('[data-dc-drawer-close]')?.click());
  await p.waitForTimeout(300);
}

// ============================================================================
// MOBILE
// ============================================================================
async function testMobile() {
  const g = 'mobile';
  const ctxM = await b.newContext({ ...devices['iPhone 13'], deviceScaleFactor: 2 });
  const pm = await ctxM.newPage();
  pm.on('pageerror', e => errors.push({ url: pm.url(), msg: e.message }));
  await pm.goto(BASE + '/', { waitUntil: 'load' });
  await pm.waitForTimeout(700);
  const ov = await pm.evaluate(() => Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth);
  ov <= 0 ? pass(g, 'no overflow @390px') : fail(g, `overflow ${ov}px`);

  const hero = await pm.$('.hero');
  hero ? pass(g, 'hero renders on mobile') : fail(g, 'hero missing on mobile');

  // Category grid stacks
  const catTilesBox = await pm.evaluate(() => {
    const t = document.querySelector('.category-tile');
    return t ? Math.round(t.getBoundingClientRect().width) : 0;
  });
  catTilesBox > 100 ? pass(g, `category tiles fill mobile width (${catTilesBox}px)`) : note(g, `tile width ${catTilesBox}`);

  await pm.screenshot({ path: `${OUT}/03-home-mobile.png`, fullPage: true });
  await ctxM.close();
}

// ============================================================================
// PERFORMANCE (basic — DOM content ready and image counts)
// ============================================================================
async function testPerf() {
  const g = 'perf';
  await p.goto(BASE + '/', { waitUntil: 'load' });
  const metrics = await p.evaluate(() => ({
    imgs: document.images.length,
    lazyImgs: Array.from(document.images).filter(i => i.getAttribute('loading') === 'lazy').length,
    videos: document.querySelectorAll('video').length,
  }));
  pass(g, `${metrics.imgs} imgs (${metrics.lazyImgs} lazy), ${metrics.videos} videos`);
}

// ============================================================================
// RUN
// ============================================================================
const groups = [
  ['load',            testLoad],
  ['hero',            testHero],
  ['section-heads',   testSectionHeads],
  ['product-rows',    testProductRows],
  ['category-grid',   testCategoryGrid],
  ['editorial-band',  testEditorialBand],
  ['blog-row',        testBlogRow],
  ['trust-band',      testTrustBand],
  ['nav',             testNav],
  ['card-add',        testCardAdd],
  ['mobile',          testMobile],
  ['perf',            testPerf],
];

for (const [name, fn] of groups) {
  process.stdout.write(`\n[${name}] `);
  try { await fn(); } catch (e) { fail(name, `crash: ${e.message.slice(0, 120)}`); }
}
console.log('\n');

await b.close();

const pass_ = results.filter(r => r.s === 'PASS').length;
const fail_ = results.filter(r => r.s === 'FAIL').length;
const note_ = results.filter(r => r.s === 'NOTE').length;
console.log('='.repeat(72));
console.log(`HOME RESULTS: ${pass_} pass · ${fail_} fail · ${note_} note`);
console.log('='.repeat(72));
for (const r of results.filter(r => r.s !== 'PASS')) console.log(`  [${r.s}] ${r.g}: ${r.m}`);
if (errors.length) {
  console.log('\nJS ERRORS:');
  for (const e of errors.slice(0, 5)) console.log(`  ${e.url.split('/').slice(3).join('/')}: ${e.msg.slice(0, 100)}`);
}
process.exit(fail_ === 0 ? 0 : 1);
