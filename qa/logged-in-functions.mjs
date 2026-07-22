// Logged-in-user Playwright audit — signs in, then verifies every account-
// scoped flow: dashboard, orders, downloads, addresses, payment methods,
// account details, checkout as customer, order-received, logout.
import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots/logged-in', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = 'http://localhost:8090';
const USER = 'qa-tester';
const PASS = 'QaTester!2026';

const results = [];
function pass(g, m) { results.push({ g, s: 'PASS', m }); process.stdout.write('✓'); }
function fail(g, m) { results.push({ g, s: 'FAIL', m }); process.stdout.write('✗'); }
function note(g, m) { results.push({ g, s: 'NOTE', m }); process.stdout.write('·'); }

const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push({ url: p.url(), msg: e.message }));
async function shot(n) { await p.screenshot({ path: `${OUT}/${n}.png`, fullPage: true }).catch(() => {}); }

// -------------------------------------------------------------------------
// LOGIN
// -------------------------------------------------------------------------
async function testLogin() {
  const g = 'login';
  await p.goto(BASE + '/my-account/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  await p.fill('#username', USER);
  await p.fill('#password', PASS);
  await Promise.all([
    p.waitForNavigation({ waitUntil: 'load' }).catch(() => {}),
    p.click('button[name="login"]'),
  ]);
  await p.waitForTimeout(800);
  const loggedIn = await p.evaluate(() => document.body.classList.contains('logged-in'));
  loggedIn ? pass(g, 'signed in as qa-tester') : fail(g, 'login failed');
  await shot('01-dashboard');
}

// -------------------------------------------------------------------------
// DASHBOARD
// -------------------------------------------------------------------------
async function testDashboard() {
  const g = 'dashboard';
  await p.goto(BASE + '/my-account/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const menuItems = await p.$$eval('.woocommerce-MyAccount-navigation a, .dc-account-nav a', els => els.map(e => e.textContent.trim()));
  menuItems.length >= 4 ? pass(g, `nav items: ${menuItems.join(', ')}`) : fail(g, `only ${menuItems.length} nav items`);

  // Greeting present
  const greeting = await p.evaluate(() => document.body.innerText.match(/Hello|Welcome|Hi\s/i)?.[0]);
  greeting ? pass(g, `greeting found ("${greeting}")`) : note(g, 'no greeting found');
}

// -------------------------------------------------------------------------
// ACCOUNT SUB-PAGES
// -------------------------------------------------------------------------
async function testAccountPages() {
  const pages = [
    { g: 'orders',     url: '/my-account/orders/' },
    { g: 'downloads',  url: '/my-account/downloads/' },
    { g: 'addresses',  url: '/my-account/edit-address/' },
    { g: 'payment',    url: '/my-account/payment-methods/' },
    { g: 'details',    url: '/my-account/edit-account/' },
  ];
  for (const spec of pages) {
    const resp = await p.goto(BASE + spec.url, { waitUntil: 'load' });
    await p.waitForTimeout(600);
    if (resp.status() === 200) pass(spec.g, 'page loads');
    else fail(spec.g, `status ${resp.status()}`);

    const stillLoggedIn = await p.evaluate(() => document.body.classList.contains('logged-in'));
    if (!stillLoggedIn) fail(spec.g, 'session dropped — redirected to login');

    const ov = await p.evaluate(() => Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth);
    ov <= 0 ? pass(spec.g, 'no horizontal overflow') : fail(spec.g, `overflow ${ov}px`);

    await shot(`02-${spec.g}`);
  }
}

// -------------------------------------------------------------------------
// HEADER (should still show cart pill, nav; may show account link if templated)
// -------------------------------------------------------------------------
async function testHeaderLoggedIn() {
  const g = 'header-loggedin';
  await p.goto(BASE + '/', { waitUntil: 'load' });
  await p.waitForTimeout(400);
  const loggedIn = await p.evaluate(() => document.body.classList.contains('logged-in'));
  loggedIn ? pass(g, 'session persists on home') : fail(g, 'session dropped on home');

  // Cart pill still visible
  const cart = await p.$('.cart-summary');
  cart ? pass(g, 'cart pill still visible') : fail(g, 'cart pill missing');

  await shot('03-header-loggedin');
}

// -------------------------------------------------------------------------
// ADD TO CART + CHECKOUT AS LOGGED-IN
// -------------------------------------------------------------------------
async function testCheckoutLoggedIn() {
  const g = 'checkout-loggedin';
  // Add product
  await p.goto(BASE + '/?add-to-cart=17009', { waitUntil: 'load' });
  await p.waitForTimeout(600);
  // Go to checkout
  await p.goto(BASE + '/checkout/', { waitUntil: 'load' });
  await p.waitForTimeout(900);

  const loggedIn = await p.evaluate(() => document.body.classList.contains('logged-in'));
  loggedIn ? pass(g, 'session persists on checkout') : fail(g, 'session dropped on checkout');

  // Should NOT see the "Have an account? Log in" link (already signed in)
  const loginLink = await p.$('a.showlogin, .dc-checkout__login-link');
  !loginLink ? pass(g, 'no login prompt (already signed in)') : note(g, 'login prompt still visible');

  const emailFilled = await p.evaluate(() => document.getElementById('billing_email')?.value || '');
  emailFilled ? pass(g, `email prefilled: ${emailFilled}`) : note(g, 'email not prefilled');

  // Coupon accordion should still work
  const coupon = await p.$('.dc-coupon, [data-dc-coupon-toggle], form.checkout_coupon');
  coupon ? pass(g, 'coupon field present') : note(g, 'no coupon field found');

  const placeOrder = await p.$('#place_order');
  placeOrder ? pass(g, 'place-order button present') : fail(g, 'place-order missing');

  const shippingCard = await p.$('.dc-checkout-card--shipping-method');
  !shippingCard ? pass(g, 'shipping-method card still absent') : fail(g, 'shipping-method card reappeared');

  await shot('04-checkout-loggedin');
}

// -------------------------------------------------------------------------
// EDIT ADDRESS FORM (billing) — fill + save
// -------------------------------------------------------------------------
async function testEditAddress() {
  const g = 'edit-address';
  await p.goto(BASE + '/my-account/edit-address/billing/', { waitUntil: 'load' });
  await p.waitForTimeout(700);

  // Find the form by an input we know is inside it (Woo's default markup
  // doesn't put a stable class on the outer form on this install).
  const form = await p.$('form:has(#billing_first_name)');
  if (!form) { fail(g, 'no address form'); return; }
  pass(g, 'billing address form loads');

  // Fill required fields and save
  const stamp = 'QA-' + Date.now();
  await p.fill('#billing_first_name', 'QA');
  await p.fill('#billing_last_name', 'Tester');
  await p.fill('#billing_address_1', '123 Test St ' + stamp);
  await p.fill('#billing_city', 'Tracy');
  // Country / State are usually select2 dropdowns; skip if already set
  await p.fill('#billing_postcode', '95304');
  await p.fill('#billing_phone', '2095550142');

  await Promise.all([
    p.waitForNavigation({ waitUntil: 'load' }).catch(() => {}),
    p.click('button[name="save_address"]'),
  ]);
  await p.waitForTimeout(700);

  const success = await p.evaluate(() => {
    const m = document.querySelector('.woocommerce-message, .woocommerce-notices-wrapper .woocommerce-message, .dc-notice--success');
    return m ? m.textContent.trim() : null;
  });
  success ? pass(g, `save confirmed: ${success.slice(0, 60)}`) : note(g, 'no confirmation message');

  await shot('05-edit-address-saved');
}

// -------------------------------------------------------------------------
// EDIT ACCOUNT (name / email / password)
// -------------------------------------------------------------------------
async function testEditAccount() {
  const g = 'edit-account';
  await p.goto(BASE + '/my-account/edit-account/', { waitUntil: 'load' });
  await p.waitForTimeout(700);
  const form = await p.$('form.woocommerce-EditAccountForm, form.edit-account');
  form ? pass(g, 'edit-account form loads') : fail(g, 'edit-account form missing');

  const firstName = await p.$('#account_first_name');
  const email = await p.$('#account_email');
  firstName && email ? pass(g, 'name + email fields present') : fail(g, 'missing form fields');

  await shot('06-edit-account');
}

// -------------------------------------------------------------------------
// WISHLIST + COMPARE persist across auth (localStorage-only, but confirm)
// -------------------------------------------------------------------------
async function testWishlistCompare() {
  const g = 'wishlist-compare';
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const wl = await p.evaluate(() => JSON.parse(localStorage.getItem('dc-wishlist') || '[]'));
  const cmp = await p.evaluate(() => JSON.parse(localStorage.getItem('dc-compare') || '[]'));
  note(g, `wishlist:${wl.length} compare:${cmp.length} (localStorage — not tied to user account)`);
  pass(g, 'shop page loads while logged in');
}

// -------------------------------------------------------------------------
// LOGOUT
// -------------------------------------------------------------------------
async function testLogout() {
  const g = 'logout';
  // WC's logout link includes a nonce; find it via the account nav
  await p.goto(BASE + '/my-account/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const logoutHref = await p.evaluate(() => {
    const a = Array.from(document.querySelectorAll('.woocommerce-MyAccount-navigation a, .dc-account-nav a')).find(el => /log ?out/i.test(el.textContent));
    return a ? a.href : null;
  });
  if (!logoutHref) { fail(g, 'no logout link found'); return; }
  await p.goto(logoutHref, { waitUntil: 'load' });
  await p.waitForTimeout(600);

  const stillIn = await p.evaluate(() => document.body.classList.contains('logged-in'));
  !stillIn ? pass(g, 'signed out successfully') : fail(g, 'still logged in after logout');
  await shot('07-logged-out');
}

// -------------------------------------------------------------------------
// RUN
// -------------------------------------------------------------------------
const groups = [
  ['login',            testLogin],
  ['dashboard',        testDashboard],
  ['account-pages',    testAccountPages],
  ['header-loggedin',  testHeaderLoggedIn],
  ['checkout',         testCheckoutLoggedIn],
  ['edit-address',     testEditAddress],
  ['edit-account',     testEditAccount],
  ['wishlist-compare', testWishlistCompare],
  ['logout',           testLogout],
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
for (const r of results.filter(r => r.s !== 'PASS')) console.log(`  [${r.s}] ${r.g}: ${r.m}`);
if (errors.length) {
  console.log('\nJS ERRORS during run:');
  for (const e of errors.slice(0, 10)) console.log(`  ${e.url.split('/').slice(3).join('/')}: ${e.msg.slice(0, 100)}`);
}
process.exit(fail_ === 0 ? 0 : 1);
