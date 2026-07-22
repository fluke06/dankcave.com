// Comprehensive Playwright audit — hits every interactive function on the
// site and reports pass/fail. Groups: header/nav, home, shop, PDP, cart drawer,
// cart page, checkout, my account, blog, about + patterns, contact + FAQ,
// wishlist, compare, search, footer, 404.
import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots/all-fn', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = 'http://localhost:8090';

const results = [];
function pass(g, msg)  { results.push({ g, s: 'PASS', m: msg }); process.stdout.write('✓'); }
function fail(g, msg)  { results.push({ g, s: 'FAIL', m: msg }); process.stdout.write('✗'); }
function note(g, msg)  { results.push({ g, s: 'NOTE', m: msg }); process.stdout.write('·'); }

const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push({ url: p.url(), msg: e.message }));

async function shot(name) { await p.screenshot({ path: `${OUT}/${name}.png` }).catch(() => {}); }
async function fullShot(name) { await p.screenshot({ path: `${OUT}/${name}.png`, fullPage: true }).catch(() => {}); }
async function overflow() { return p.evaluate(() => Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth); }

// ============================================================================
// HEADER + NAV
// ============================================================================
async function testHeader() {
  const g = 'header';
  await p.goto(BASE + '/', { waitUntil: 'load' });
  await p.waitForTimeout(400);

  // Logo
  const logo = await p.$('.custom-logo-link');
  logo ? pass(g, 'logo present') : fail(g, 'logo missing');

  // Primary nav items
  const navItems = await p.$$eval('.primary-nav a', els => els.map(e => e.textContent.trim()));
  if (JSON.stringify(navItems) === JSON.stringify(['Shop', 'Journal', 'About', 'Contact'])) pass(g, `nav: ${navItems.join(', ')}`);
  else fail(g, `nav mismatch: ${JSON.stringify(navItems)}`);

  // Cart pill visible
  const cart = await p.$('.cart-summary');
  cart ? pass(g, 'cart pill visible') : fail(g, 'cart pill missing');

  // Search pill opens modal
  await p.click('[data-search-open]');
  await p.waitForTimeout(300);
  const searchOpen = await p.evaluate(() => document.getElementById('search-modal')?.getAttribute('data-open') === 'true');
  searchOpen ? pass(g, 'search modal opens') : fail(g, 'search modal did not open');
  await shot('search-modal-open');

  // Search live results
  await p.fill('[data-search-input]', 'zig');
  await p.waitForTimeout(700);
  const searchResults = await p.$$eval('[data-search-results] a, [data-search-results] li', els => els.length);
  searchResults > 0 ? pass(g, `search returned ${searchResults} hits`) : note(g, 'no search hits for "zig"');

  // Close via close button
  await p.click('[data-search-close]').catch(() => {});
  await p.waitForTimeout(300);
  const searchClosed = await p.evaluate(() => document.getElementById('search-modal')?.hidden === true);
  searchClosed ? pass(g, 'search modal closes') : fail(g, 'search modal did not close');
}

// ============================================================================
// HOMEPAGE
// ============================================================================
async function testHome() {
  const g = 'home';
  await p.goto(BASE + '/', { waitUntil: 'load' });
  await p.waitForTimeout(600);
  const ov = await overflow();
  ov <= 0 ? pass(g, 'no horizontal overflow') : fail(g, `overflow ${ov}px`);
  await fullShot('home');
}

// ============================================================================
// SHOP ARCHIVE
// ============================================================================
async function testShop() {
  const g = 'shop';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(700);
  const cards = await p.$$('.product-card');
  cards.length > 0 ? pass(g, `${cards.length} product cards`) : fail(g, 'no product cards');

  // Sort dropdown
  const sort = await p.$('.woocommerce-ordering select, .dc-shop-sort select');
  sort ? pass(g, 'sort dropdown present') : note(g, 'no sort dropdown found');

  // Filter sidebar
  const filters = await p.$('.shop-filters');
  filters ? pass(g, 'filters sidebar present') : fail(g, 'no filters sidebar');

  await fullShot('shop');
}

// ============================================================================
// PRODUCT CARD HOVER ACTIONS (desktop hover, plus wishlist/compare)
// ============================================================================
async function testCardActions() {
  const g = 'card-actions';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);

  // Wishlist toggle updates localStorage
  await p.evaluate(() => localStorage.setItem('dc-wishlist', '[]'));
  await p.evaluate(() => document.querySelector('.product-card [data-dc-wishlist]')?.click());
  await p.waitForTimeout(300);
  const wl = await p.evaluate(() => JSON.parse(localStorage.getItem('dc-wishlist') || '[]'));
  wl.length === 1 ? pass(g, 'wishlist toggle writes localStorage') : fail(g, `wishlist toggle failed (${wl.length})`);

  // Compare toggle
  await p.evaluate(() => localStorage.setItem('dc-compare', '[]'));
  await p.evaluate(() => document.querySelector('.product-card [data-dc-compare]')?.click());
  await p.waitForTimeout(400);
  const cmp = await p.evaluate(() => JSON.parse(localStorage.getItem('dc-compare') || '[]'));
  cmp.length === 1 ? pass(g, 'compare toggle writes localStorage') : fail(g, `compare toggle failed (${cmp.length})`);

  // Compare tray visible after add
  const tray = await p.evaluate(() => document.getElementById('dc-compare-tray')?.getAttribute('data-visible') === 'true');
  tray ? pass(g, 'compare tray becomes visible') : fail(g, 'compare tray not visible after adding');

  // Quickview click opens modal
  await p.evaluate(() => document.querySelector('.product-card [data-dc-quickview]')?.click());
  await p.waitForTimeout(1200);
  const qvOpen = await p.evaluate(() => document.getElementById('dc-quickview')?.getAttribute('data-open') === 'true');
  qvOpen ? pass(g, 'quickview opens on eye click') : fail(g, 'quickview did not open');
  await shot('quickview-open');
  await p.evaluate(() => document.querySelector('[data-dc-quickview-close]')?.click());
  await p.waitForTimeout(300);
}

// ============================================================================
// PDP — gallery, variations, quantity, add-to-cart, accordions
// ============================================================================
async function testPDP() {
  const g = 'pdp';
  await p.goto(BASE + '/shop/rollings/rolling-papers/zig-zag-rolling-papers-king-size/', { waitUntil: 'load' });
  await p.waitForTimeout(700);

  // Gallery hero
  const hero = await p.$('.pdp-gallery__image');
  hero ? pass(g, 'gallery hero present') : fail(g, 'gallery hero missing');

  // Add-to-cart button
  const atc = await p.$('button.single_add_to_cart_button');
  atc ? pass(g, 'add-to-cart button present') : fail(g, 'add-to-cart button missing');

  // Quantity input
  const qty = await p.$('input.qty');
  qty ? pass(g, 'quantity input present') : fail(g, 'quantity input missing');

  // Accordions open+close smoothly (already verified separately; here just verify presence)
  const accs = await p.$$('.pdp-accordion');
  accs.length >= 1 ? pass(g, `${accs.length} PDP accordions`) : fail(g, 'no PDP accordions');

  await fullShot('pdp');
}

// ============================================================================
// CART DRAWER slides in on AJAX add-to-cart
// ============================================================================
async function testCartDrawer() {
  const g = 'cart-drawer';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(600);

  // Click a simple product's Add button — should AJAX-add and open drawer
  const btn = await p.$('.product-card__add:not(.product-card__add--needs-options)');
  if (!btn) { note(g, 'no simple product add button found on shop archive'); return; }
  await btn.click();
  await p.waitForTimeout(1500);
  const open = await p.evaluate(() => document.getElementById('dc-cart-drawer')?.getAttribute('data-open') === 'true');
  open ? pass(g, 'cart drawer opens on add-to-cart') : fail(g, 'cart drawer did not open after add');
  await shot('cart-drawer-open');

  // Item count
  const count = await p.evaluate(() => document.querySelector('[data-dc-drawer-count]')?.textContent);
  Number(count) >= 1 ? pass(g, `drawer shows ${count} item(s)`) : fail(g, `drawer count is ${count}`);

  // Close via backdrop
  await p.evaluate(() => document.querySelector('[data-dc-drawer-close]')?.click());
  await p.waitForTimeout(400);
  const closed = await p.evaluate(() => document.getElementById('dc-cart-drawer')?.hidden === true);
  closed ? pass(g, 'drawer closes') : fail(g, 'drawer did not close');
}

// ============================================================================
// CART PAGE
// ============================================================================
async function testCart() {
  const g = 'cart';
  await p.goto(BASE + '/cart/', { waitUntil: 'load' });
  await p.waitForTimeout(600);
  const items = await p.$$('.woocommerce-cart-form__cart-item, .cart_item, .dc-cart-item');
  items.length > 0 ? pass(g, `${items.length} cart lines`) : fail(g, 'no cart items rendered');

  const totalsBlock = await p.$('.cart_totals, .dc-cart-totals, .cart-collaterals');
  totalsBlock ? pass(g, 'totals block present') : fail(g, 'no totals block');

  await fullShot('cart');
}

// ============================================================================
// CHECKOUT
// ============================================================================
async function testCheckout() {
  const g = 'checkout';
  await p.goto(BASE + '/checkout/', { waitUntil: 'load' });
  await p.waitForTimeout(900);

  const billing = await p.$('.dc-checkout-card--billing');
  billing ? pass(g, 'billing/contact-shipping card present') : fail(g, 'billing card missing');

  const shippingCard = await p.$('.dc-checkout-card--shipping-method');
  !shippingCard ? pass(g, 'shipping-method card removed (as intended)') : fail(g, 'shipping-method card still present');

  const orderReview = await p.$('#order_review');
  orderReview ? pass(g, 'order review present') : fail(g, 'order review missing');

  const placeOrder = await p.$('#place_order');
  placeOrder ? pass(g, 'place-order button present') : fail(g, 'place-order button missing');

  // Verify email field is priority 5 (i.e. visible near top)
  const emailY = await p.evaluate(() => document.getElementById('billing_email')?.getBoundingClientRect().top);
  emailY < 700 ? pass(g, `email field near top (y=${Math.round(emailY)})`) : note(g, `email field y=${Math.round(emailY)}`);

  await fullShot('checkout');
}

// ============================================================================
// MY ACCOUNT (login page when signed out)
// ============================================================================
async function testAccount() {
  const g = 'account';
  await p.goto(BASE + '/my-account/', { waitUntil: 'load' });
  await p.waitForTimeout(600);
  const loginForm = await p.$('.woocommerce-form-login, form.login, #customer_login');
  loginForm ? pass(g, 'login form rendered') : fail(g, 'login form missing');
  await fullShot('my-account');
}

// ============================================================================
// BLOG / JOURNAL
// ============================================================================
async function testBlog() {
  const g = 'blog';
  await p.goto(BASE + '/blog/', { waitUntil: 'load' });
  await p.waitForTimeout(600);
  const posts = await p.$$('article, .post, .blog-card');
  posts.length > 0 ? pass(g, `${posts.length} blog posts`) : fail(g, 'no blog posts');
  await fullShot('blog');
}

// ============================================================================
// ABOUT — all 6 patterns present
// ============================================================================
async function testAbout() {
  const g = 'about';
  await p.goto(BASE + '/about-us/', { waitUntil: 'load' });
  await p.waitForTimeout(600);
  const patterns = await p.evaluate(() => {
    const check = ['pattern-about-hero','pattern-commitment','pattern-why','pattern-showcase','pattern-satisfaction','pattern-community'];
    const found = {};
    for (const c of check) found[c] = !!document.querySelector('.' + c);
    return found;
  });
  const missing = Object.entries(patterns).filter(([_, v]) => !v).map(([k]) => k);
  missing.length === 0 ? pass(g, 'all 6 sections render') : fail(g, `missing: ${missing.join(', ')}`);

  const crumbs = await p.$('.dc-landing-crumbs');
  crumbs ? pass(g, 'breadcrumbs present') : fail(g, 'breadcrumbs missing');
}

// ============================================================================
// CONTACT — form + FAQ accordion
// ============================================================================
async function testContact() {
  const g = 'contact';
  await p.goto(BASE + '/contact-us/', { waitUntil: 'load' });
  await p.waitForTimeout(700);

  const form = await p.$('.wpcf7 form');
  form ? pass(g, 'CF7 form rendered') : fail(g, 'CF7 form missing');

  const submitBtn = await p.$('.wpcf7-submit');
  submitBtn ? pass(g, 'send-message button present') : fail(g, 'send-message button missing');

  const accs = await p.$$('details.pattern-contact__acc');
  accs.length === 5 ? pass(g, 'all 5 FAQ items') : fail(g, `${accs.length} FAQ items (expected 5)`);

  // Click a closed one and verify smooth animation via multiple height samples
  const anim = await p.evaluate(async () => {
    const acc = document.querySelectorAll('details.pattern-contact__acc')[1];
    const body = acc?.querySelector('.dc-acc__body');
    if (!acc || !body) return null;
    acc.querySelector('summary').click();
    const samples = [];
    const start = performance.now();
    while (performance.now() - start < 400) {
      samples.push(Math.round(body.getBoundingClientRect().height));
      await new Promise(r => requestAnimationFrame(r));
    }
    return { start: samples[0], end: samples[samples.length - 1], steps: new Set(samples).size };
  });
  if (anim && anim.steps >= 5 && anim.end > anim.start) pass(g, `FAQ smooth open (${anim.steps} distinct heights, ${anim.start}→${anim.end}px)`);
  else fail(g, `FAQ animation not smooth: ${JSON.stringify(anim)}`);

  // Also breadcrumbs
  const crumbs = await p.$('.dc-landing-crumbs');
  crumbs ? pass(g, 'breadcrumbs present') : fail(g, 'breadcrumbs missing');
}

// ============================================================================
// COMPARE — tray + modal + per-column remove
// ============================================================================
async function testCompare() {
  const g = 'compare';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  // Reset + add 3
  await p.evaluate(() => localStorage.setItem('dc-compare', '[]'));
  await p.evaluate(() => {
    document.querySelectorAll('.product-card [data-dc-compare]').forEach((btn, i) => { if (i < 3) btn.click(); });
  });
  await p.waitForTimeout(600);
  const trayVisible = await p.evaluate(() => document.getElementById('dc-compare-tray')?.getAttribute('data-visible') === 'true');
  trayVisible ? pass(g, 'tray visible with 3 items') : fail(g, 'tray not visible');

  // Open modal
  await p.click('[data-dc-compare-open]');
  await p.waitForTimeout(1200);
  const modalOpen = await p.evaluate(() => document.getElementById('dc-compare-modal')?.getAttribute('data-open') === 'true');
  modalOpen ? pass(g, 'compare modal opens') : fail(g, 'compare modal did not open');

  // Best-price badge + chips
  const bestBadge = await p.$('.dc-compare-table__badge--best');
  bestBadge ? pass(g, '"Best price" badge visible') : fail(g, 'best-price badge missing');
  const chips = await p.$$('.dc-compare-table__chip');
  chips.length >= 3 ? pass(g, `${chips.length} stock chips`) : fail(g, `only ${chips.length} chips`);

  await shot('compare-modal');

  // Per-column remove
  const rmBefore = (await p.$$('[data-dc-compare-remove]')).length;
  await p.evaluate(() => document.querySelector('[data-dc-compare-remove]')?.click());
  await p.waitForTimeout(1200);
  const rmAfter = (await p.$$('[data-dc-compare-remove]')).length;
  rmAfter === rmBefore - 1 ? pass(g, `per-column remove works (${rmBefore}→${rmAfter})`) : fail(g, `remove failed (${rmBefore}→${rmAfter})`);

  // Close modal
  await p.click('button.dc-compare-modal__close');
  await p.waitForTimeout(400);
}

// ============================================================================
// FOOTER links
// ============================================================================
async function testFooter() {
  const g = 'footer';
  await p.goto(BASE + '/', { waitUntil: 'load' });
  await p.waitForTimeout(400);
  const newsletter = await p.$('.newsletter-band');
  newsletter ? pass(g, 'newsletter band present') : fail(g, 'newsletter band missing');

  const legalLinks = await p.$$('.legal-bar__nav a');
  legalLinks.length >= 4 ? pass(g, `${legalLinks.length} legal links`) : fail(g, `only ${legalLinks.length} legal links`);
}

// ============================================================================
// 404
// ============================================================================
async function test404() {
  const g = '404';
  const resp = await p.goto(BASE + '/nonexistent-page-xyz-' + Date.now(), { waitUntil: 'load' });
  await p.waitForTimeout(400);
  resp.status() === 404 ? pass(g, 'returns 404 status') : fail(g, `status ${resp.status()}`);
  const hasHomeLink = await p.evaluate(() => Array.from(document.querySelectorAll('a')).some(a => a.href.endsWith('/')));
  hasHomeLink ? pass(g, 'links to home visible') : note(g, 'no home link found on 404');
}

// ============================================================================
// MOBILE — quick smoke on iPhone 13
// ============================================================================
async function testMobile() {
  const g = 'mobile';
  const ctxM = await b.newContext({ ...devices['iPhone 13'], deviceScaleFactor: 2 });
  const pm = await ctxM.newPage();
  pm.on('pageerror', e => errors.push({ url: pm.url(), msg: e.message }));

  await pm.goto(BASE + '/', { waitUntil: 'load' });
  await pm.waitForTimeout(500);
  const ov = await pm.evaluate(() => Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth);
  ov <= 0 ? pass(g, 'home no overflow @390') : fail(g, `home overflow ${ov}`);

  // Hamburger opens mobile nav
  await pm.click('.site-header__toggle');
  await pm.waitForTimeout(400);
  const navOpen = await pm.evaluate(() => document.getElementById('primary-nav-mobile')?.hidden === false);
  navOpen ? pass(g, 'hamburger opens mobile nav') : fail(g, 'hamburger failed');

  // Card actions visible on touch
  await pm.goto(BASE + '/shop/', { waitUntil: 'load' });
  await pm.waitForTimeout(500);
  const cardActionsVisible = await pm.evaluate(() => {
    const el = document.querySelector('.product-card__hover-actions');
    if (!el) return false;
    return getComputedStyle(el).opacity !== '0';
  });
  cardActionsVisible ? pass(g, 'card actions visible on touch') : fail(g, 'card actions hidden on touch');

  await ctxM.close();
}

// ============================================================================
// RUN
// ============================================================================
const groups = [
  ['header',       testHeader],
  ['home',         testHome],
  ['shop',         testShop],
  ['card-actions', testCardActions],
  ['pdp',          testPDP],
  ['cart-drawer',  testCartDrawer],
  ['cart',         testCart],
  ['checkout',     testCheckout],
  ['account',      testAccount],
  ['blog',         testBlog],
  ['about',        testAbout],
  ['contact',      testContact],
  ['compare',      testCompare],
  ['footer',       testFooter],
  ['404',          test404],
  ['mobile',       testMobile],
];

for (const [name, fn] of groups) {
  process.stdout.write(`\n[${name}] `);
  try { await fn(); } catch (e) { fail(name, `crash: ${e.message.slice(0, 100)}`); }
}
console.log('\n');

await b.close();

const pass_ = results.filter(r => r.s === 'PASS').length;
const fail_ = results.filter(r => r.s === 'FAIL').length;
const note_ = results.filter(r => r.s === 'NOTE').length;

console.log('='.repeat(72));
console.log(`RESULTS: ${pass_} pass · ${fail_} fail · ${note_} note`);
console.log('='.repeat(72));

for (const r of results.filter(r => r.s !== 'PASS')) {
  console.log(`  [${r.s}] ${r.g}: ${r.m}`);
}
if (errors.length) {
  console.log('\nJS ERRORS during run:');
  for (const e of errors.slice(0, 10)) console.log(`  ${e.url.split('/').slice(3).join('/')}: ${e.msg.slice(0, 100)}`);
}

process.exit(fail_ === 0 ? 0 : 1);
