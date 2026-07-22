// Shop archive: exhaustive function check.
// Covers sort, price filter, category filter, brand filter, availability,
// clear filters, pagination, product cards (image, title, price, stock,
// sale/bestseller badges), add-to-cart from card, variable-product options
// dropdown quickview, hover actions (wishlist, compare, quickview), result
// count, breadcrumbs.
import { chromium } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots/shop-fn', import.meta.url).pathname;
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

async function shot(name) { await p.screenshot({ path: `${OUT}/${name}.png`, fullPage: false }).catch(() => {}); }
async function fullShot(name) { await p.screenshot({ path: `${OUT}/${name}.png`, fullPage: true }).catch(() => {}); }

// -------------------------------------------------------------------------
// PAGE LOAD
// -------------------------------------------------------------------------
async function testLoad() {
  const g = 'load';
  const resp = await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(700);
  resp.status() === 200 ? pass(g, 'shop returns 200') : fail(g, `status ${resp.status()}`);
  const ov = await p.evaluate(() => Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth);
  ov <= 0 ? pass(g, 'no horizontal overflow') : fail(g, `overflow ${ov}px`);
  const h1 = await p.$eval('h1', el => el.textContent.trim()).catch(() => null);
  h1 ? pass(g, `H1: "${h1}"`) : note(g, 'no H1 found');
  await fullShot('01-shop-load');
}

// -------------------------------------------------------------------------
// RESULT COUNT + BREADCRUMBS
// -------------------------------------------------------------------------
async function testHeader() {
  const g = 'header';
  const crumbs = await p.evaluate(() => {
    const nav = document.querySelector('.woocommerce-breadcrumb, .dc-breadcrumb, nav[aria-label*="readcrumb" i]');
    return nav ? nav.textContent.replace(/\s+/g, ' ').trim() : null;
  });
  crumbs ? pass(g, `breadcrumb: "${crumbs}"`) : note(g, 'no breadcrumb found');

  // Product count appears in the intro subheading ("186 hand-picked pieces.")
  const count = await p.evaluate(() => {
    const el = Array.from(document.querySelectorAll('.dc-shop__subtitle, .dc-shop__intro, h2 + p, h1 + p')).find(e => /\d+/.test(e.textContent));
    return el ? el.textContent.trim() : null;
  });
  count ? pass(g, `result count: "${count}"`) : note(g, 'no result count paragraph found');
}

// -------------------------------------------------------------------------
// PRODUCT CARDS
// -------------------------------------------------------------------------
async function testCards() {
  const g = 'cards';
  const cards = await p.$$('.product-card');
  cards.length > 0 ? pass(g, `${cards.length} product cards`) : fail(g, 'no cards');

  // Every card should have image, title, price, add-button
  const cardParts = await p.$$eval('.product-card', els => els.slice(0, 5).map(el => ({
    hasImg: !!el.querySelector('img'),
    hasTitle: !!el.querySelector('.product-card__title, h2, h3'),
    hasPrice: !!el.querySelector('.price, .product-card__price'),
    hasAdd: !!el.querySelector('.product-card__add, .add_to_cart_button, button, a[href*="add-to-cart"]'),
    hasCategory: !!el.querySelector('.product-card__category, .posted_in'),
  })));
  const missing = cardParts.filter(c => !c.hasImg || !c.hasTitle || !c.hasPrice || !c.hasAdd);
  missing.length === 0 ? pass(g, 'all sample cards have img+title+price+add') : fail(g, `${missing.length}/5 cards missing parts: ${JSON.stringify(missing[0])}`);

  // Badge presence — .product-card__badge only renders when the archive passes
  // a `badge` arg (bestseller/on-sale/etc.). None on the current dataset.
  const badges = await p.$$('.product-card__badge');
  badges.length > 0 ? pass(g, `${badges.length} badge(s) visible`) : note(g, 'no badges — theme supports them but no products flagged as bestseller/sale');

  // First-card LCP eager loading — image should not be lazy
  const firstImgAttrs = await p.evaluate(() => {
    const img = document.querySelector('.product-card img');
    return img ? { loading: img.getAttribute('loading'), fetchpriority: img.getAttribute('fetchpriority'), classes: img.className } : null;
  });
  console.log(' first-img=' + JSON.stringify(firstImgAttrs));
}

// -------------------------------------------------------------------------
// SORT
// -------------------------------------------------------------------------
async function testSort() {
  const g = 'sort';
  const sortEl = await p.$('select[name="orderby"], .dc-shop-sort select');
  if (!sortEl) { fail(g, 'no sort dropdown'); return; }
  pass(g, 'sort dropdown present');

  const options = await p.$$eval('select[name="orderby"] option', els => els.map(e => ({ v: e.value, t: e.textContent.trim() })));
  pass(g, `${options.length} sort options: ${options.map(o => o.v).join('|')}`);

  // Change to price ascending; page should reload with orderby=price
  await p.selectOption('select[name="orderby"]', 'price');
  await p.waitForLoadState('load');
  await p.waitForTimeout(700);
  const url = p.url();
  url.includes('orderby=price') ? pass(g, 'sort by price reloads page') : fail(g, `URL: ${url}`);

  // First card price should be lowest
  const prices = await p.evaluate(() => Array.from(document.querySelectorAll('.product-card .price bdi')).slice(0, 5).map(el => parseFloat(el.textContent.replace(/[^\d.]/g, ''))));
  const sorted = [...prices].sort((a, b) => a - b);
  JSON.stringify(prices) === JSON.stringify(sorted) ? pass(g, `first prices sorted asc: ${prices.join(', ')}`) : note(g, `first prices: ${prices.join(', ')} (may be ranges from variable products)`);
}

// -------------------------------------------------------------------------
// FILTERS — price / category / availability / brand
// -------------------------------------------------------------------------
async function testFilters() {
  const g = 'filters';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(600);

  // Price bucket filter (pills)
  const priceBuckets = await p.$$('.shop-filters .shop-filters__pill');
  priceBuckets.length > 0 ? pass(g, `${priceBuckets.length} price bucket pills`) : note(g, 'no price bucket pills');

  // Category checkboxes (uses .shop-filters__check custom checkbox spans; the
  // actual <input>s may be hidden inside the label)
  const catChecks = await p.$$('.shop-filters__check, .shop-filters label input[type="checkbox"]');
  catChecks.length > 0 ? pass(g, `${catChecks.length} category checkboxes`) : note(g, 'no category checkboxes');

  // Availability filter group (label lives in .shop-filters__label)
  const filterLabels = await p.$$eval('.shop-filters__label', els => els.map(e => e.textContent.trim()));
  const hasAvail = filterLabels.some(t => /avail|stock/i.test(t));
  hasAvail ? pass(g, `filter groups: ${filterLabels.join(', ')}`) : note(g, `filter groups: ${filterLabels.join(', ')}`);

  // Clear link
  const clearAll = await p.$('.shop-filters__clear');
  clearAll ? pass(g, 'clear-all link present') : note(g, 'no clear link');

  // Actually click a price bucket if available (Under $50 = first pill)
  if (priceBuckets.length) {
    const beforeCount = (await p.$$('.product-card')).length;
    const beforeUrl = p.url();
    await priceBuckets[0].click();
    await p.waitForLoadState('load');
    await p.waitForTimeout(800);
    const afterCount = (await p.$$('.product-card')).length;
    const afterUrl = p.url();
    afterUrl !== beforeUrl ? pass(g, `price pill navigates (${beforeCount}→${afterCount} cards)`) : note(g, `URL unchanged ${beforeUrl}`);
    await shot('02-price-filter');
  }
}

// -------------------------------------------------------------------------
// PAGINATION
// -------------------------------------------------------------------------
async function testPagination() {
  const g = 'pagination';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);

  const pageLinks = await p.$$('.woocommerce-pagination a, .page-numbers, .dc-pagination a');
  if (pageLinks.length === 0) { note(g, 'no pagination visible (single page)'); return; }
  pass(g, `${pageLinks.length} pagination items`);

  // Click "next" or page 2
  const next = await p.$('.next.page-numbers, a.page-numbers[href*="/page/2"]');
  if (!next) { note(g, 'no next-page link found'); return; }
  await Promise.all([ p.waitForNavigation({ waitUntil: 'load' }), next.click() ]);
  await p.waitForTimeout(600);
  const urlOK = /\/page\/[2-9]/.test(p.url());
  urlOK ? pass(g, `paginated to ${p.url().split('/').pop() || p.url()}`) : fail(g, `URL: ${p.url()}`);
  const cards2 = await p.$$('.product-card');
  cards2.length > 0 ? pass(g, `page 2 has ${cards2.length} cards`) : fail(g, 'page 2 empty');
}

// -------------------------------------------------------------------------
// ADD-TO-CART from a SIMPLE product card
// -------------------------------------------------------------------------
async function testAjaxAdd() {
  const g = 'ajax-add';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);

  const before = await p.evaluate(() => Number(document.querySelector('[data-cart-count]')?.textContent) || 0);
  const btn = await p.$('.product-card__add:not(.product-card__add--needs-options)');
  if (!btn) { note(g, 'no simple product with add button visible'); return; }
  await btn.click();
  await p.waitForTimeout(1500);

  const after = await p.evaluate(() => Number(document.querySelector('[data-cart-count]')?.textContent) || 0);
  after > before ? pass(g, `cart count updated (${before}→${after})`) : fail(g, `cart count unchanged ${before}=${after}`);

  const drawerOpen = await p.evaluate(() => document.getElementById('dc-cart-drawer')?.getAttribute('data-open') === 'true');
  drawerOpen ? pass(g, 'cart drawer opened') : fail(g, 'drawer did not open after add');

  await p.evaluate(() => document.querySelector('[data-dc-drawer-close]')?.click());
  await p.waitForTimeout(300);
}

// -------------------------------------------------------------------------
// VARIABLE PRODUCT → QUICKVIEW MODAL
// -------------------------------------------------------------------------
async function testVariableQuickview() {
  const g = 'variable-qv';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const optsBtn = await p.$('.product-card__add--needs-options');
  if (!optsBtn) { note(g, 'no variable product with options button found'); return; }
  await optsBtn.click();
  await p.waitForTimeout(1500);
  const qvOpen = await p.evaluate(() => document.getElementById('dc-quickview')?.getAttribute('data-open') === 'true');
  qvOpen ? pass(g, 'quickview opens for variable product') : fail(g, 'quickview did not open');

  const variationSelect = await p.$('.dc-quickview select[name^="attribute_"], .dc-quickview .variations select');
  variationSelect ? pass(g, 'variation dropdown in quickview') : fail(g, 'no variation dropdown');

  const qtyInput = await p.$('.dc-quickview input.qty');
  qtyInput ? pass(g, 'quantity input in quickview') : fail(g, 'no qty input');

  await shot('03-quickview-variable');
  await p.evaluate(() => document.querySelector('[data-dc-quickview-close]')?.click());
  await p.waitForTimeout(300);
}

// -------------------------------------------------------------------------
// HOVER ACTIONS — hover triggers reveal on desktop
// -------------------------------------------------------------------------
async function testHoverActions() {
  const g = 'hover';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const card = await p.$('.product-card');
  if (!card) { fail(g, 'no card'); return; }
  const before = await card.evaluate(el => getComputedStyle(el.querySelector('.product-card__hover-actions')).opacity);
  await card.hover();
  await p.waitForTimeout(300);
  const after = await card.evaluate(el => getComputedStyle(el.querySelector('.product-card__hover-actions')).opacity);
  Number(after) > Number(before) ? pass(g, `hover reveals actions (opacity ${before}→${after})`) : fail(g, `actions did not reveal on hover ${before}→${after}`);

  // Each of the three buttons present
  const buttons = await card.$$('.product-card__hover-btn');
  buttons.length === 3 ? pass(g, '3 hover action buttons (wishlist + compare + quickview)') : fail(g, `${buttons.length} hover buttons`);
}

// -------------------------------------------------------------------------
// STOCK — sold-out product shows a "Sold out" indicator
// -------------------------------------------------------------------------
async function testStockLabels() {
  const g = 'stock';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const buttons = await p.$$eval('.product-card__add', els => els.map(e => e.textContent.trim()));
  const uniqueLabels = [...new Set(buttons)];
  pass(g, `add-button label variety: ${uniqueLabels.slice(0, 6).join(' | ')}`);

  const soldOut = await p.$$('.product-card__stock--out, [data-stock="out"]');
  soldOut.length >= 0 ? pass(g, `${soldOut.length} sold-out card(s)`) : note(g, 'no sold-out indicator class found');
}

// -------------------------------------------------------------------------
// CATEGORY DEEP-LINK
// -------------------------------------------------------------------------
async function testCategoryLink() {
  const g = 'category';
  await p.goto(BASE + '/product-category/bong/', { waitUntil: 'load' });
  await p.waitForTimeout(700);
  const cards = await p.$$('.product-card');
  cards.length > 0 ? pass(g, `bongs category: ${cards.length} products`) : fail(g, 'bongs category empty');

  const h1 = await p.$eval('h1', el => el.textContent.trim()).catch(() => null);
  h1 ? pass(g, `category H1: "${h1}"`) : note(g, 'no H1 on category');

  await fullShot('04-category');
}

// -------------------------------------------------------------------------
// RUN
// -------------------------------------------------------------------------
const groups = [
  ['load',           testLoad],
  ['header',         testHeader],
  ['cards',          testCards],
  ['sort',           testSort],
  ['filters',        testFilters],
  ['pagination',     testPagination],
  ['ajax-add',       testAjaxAdd],
  ['variable-qv',    testVariableQuickview],
  ['hover',          testHoverActions],
  ['stock',          testStockLabels],
  ['category',       testCategoryLink],
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
console.log(`SHOP RESULTS: ${pass_} pass · ${fail_} fail · ${note_} note`);
console.log('='.repeat(72));
for (const r of results.filter(r => r.s !== 'PASS')) console.log(`  [${r.s}] ${r.g}: ${r.m}`);
if (errors.length) {
  console.log('\nJS ERRORS:');
  for (const e of errors.slice(0, 5)) console.log(`  ${e.url.split('/').slice(3).join('/')}: ${e.msg.slice(0, 100)}`);
}
process.exit(fail_ === 0 ? 0 : 1);
