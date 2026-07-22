// Finishing audit: order-received, password reset, register form, empty-state
// pages (empty cart, empty search, empty account section). These are the flows
// no one has screenshotted yet.
import { chromium } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots/finishing', import.meta.url).pathname;
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

// ============================================================================
// EMPTY CART
// ============================================================================
async function testEmptyCart() {
  const g = 'empty-cart';
  // First, ensure cart is empty
  await p.goto(BASE + '/cart/?empty-cart', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  await p.goto(BASE + '/cart/', { waitUntil: 'load' });
  await p.waitForTimeout(600);

  const emptyMsg = await p.evaluate(() => {
    const el = document.querySelector('.cart-empty, .dc-cart-empty, .woocommerce-info');
    return el ? el.textContent.trim().slice(0, 100) : null;
  });
  emptyMsg ? pass(g, `empty message: "${emptyMsg}"`) : fail(g, 'no empty-cart message');

  const cta = await p.evaluate(() => Array.from(document.querySelectorAll('a')).find(a => /shop|browse|continue/i.test(a.textContent))?.textContent?.trim());
  cta ? pass(g, `CTA present: "${cta}"`) : note(g, 'no return-to-shop CTA');

  const h1 = await p.$eval('h1', el => el.textContent.trim()).catch(() => null);
  h1 ? pass(g, `H1: "${h1}"`) : note(g, 'no H1 on empty cart');

  await p.screenshot({ path: `${OUT}/01-empty-cart.png`, fullPage: true });
}

// ============================================================================
// EMPTY SEARCH
// ============================================================================
async function testEmptySearch() {
  const g = 'empty-search';
  await p.goto(BASE + '/?s=zzzzzzznonexistentquery123', { waitUntil: 'load' });
  await p.waitForTimeout(600);

  const h1 = await p.$eval('h1', el => el.textContent.trim()).catch(() => null);
  h1 ? pass(g, `H1: "${h1}"`) : note(g, 'no H1 on search page');

  const noResults = await p.evaluate(() => {
    const body = document.body.innerText;
    return /no (products|results|articles)|nothing (matched|found)/i.test(body) ? 'yes' : null;
  });
  noResults ? pass(g, 'no-results message present') : note(g, 'no explicit empty message');

  const searchAgain = await p.$('input[type="search"], input[name="s"]');
  searchAgain ? pass(g, 'search-again field present') : note(g, 'no search-again field');

  await p.screenshot({ path: `${OUT}/02-empty-search.png`, fullPage: true });
}

// ============================================================================
// LOGIN + REGISTER form
// ============================================================================
async function testLoginRegister() {
  const g = 'login-register';
  // Sign out first (via WP logout link)
  await p.goto(BASE + '/my-account/', { waitUntil: 'load' });
  await p.waitForTimeout(400);
  const loggedIn = await p.evaluate(() => document.body.classList.contains('logged-in'));
  if (loggedIn) {
    const logoutHref = await p.evaluate(() => {
      const a = Array.from(document.querySelectorAll('a')).find(el => /log ?out/i.test(el.textContent) && /_wpnonce/.test(el.href));
      return a?.href;
    });
    if (logoutHref) await p.goto(logoutHref, { waitUntil: 'load' });
    await p.waitForTimeout(400);
  }

  await p.goto(BASE + '/my-account/', { waitUntil: 'load' });
  await p.waitForTimeout(600);

  const loginForm = await p.$('form.woocommerce-form-login');
  loginForm ? pass(g, 'login form present') : fail(g, 'no login form');

  const registerForm = await p.$('form.woocommerce-form-register');
  registerForm ? pass(g, 'register form present') : note(g, 'no register form (may be disabled in WC settings)');

  const forgotLink = await p.evaluate(() => Array.from(document.querySelectorAll('a')).find(a => /lost|forgot/i.test(a.textContent))?.href);
  forgotLink ? pass(g, `lost-password link → ${forgotLink}`) : note(g, 'no lost-password link');

  await p.screenshot({ path: `${OUT}/03-login-register.png`, fullPage: true });
}

// ============================================================================
// LOST PASSWORD form
// ============================================================================
async function testLostPassword() {
  const g = 'lost-password';
  await p.goto(BASE + '/my-account/lost-password/', { waitUntil: 'load' });
  await p.waitForTimeout(600);

  const form = await p.$('form.woocommerce-ResetPassword, form.lost_reset_password');
  form ? pass(g, 'lost-password form present') : fail(g, 'no lost-password form');

  const usernameField = await p.$('input[name="user_login"]');
  usernameField ? pass(g, 'username/email field present') : fail(g, 'no username field');

  const submitBtn = await p.$('button[type="submit"], input[type="submit"]');
  submitBtn ? pass(g, 'submit button present') : fail(g, 'no submit');

  await p.screenshot({ path: `${OUT}/04-lost-password.png`, fullPage: true });
}

// ============================================================================
// ORDER-RECEIVED (needs an order to link to; we'll create one)
// ============================================================================
async function testOrderReceived() {
  const g = 'order-received';

  // Grab the most recent order via wp-cli-esque REST or via the account
  // orders page. We'll just navigate the account and click the first order.
  await p.goto(BASE + '/my-account/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  // If not logged in, sign in as qa-tester
  const loggedIn = await p.evaluate(() => document.body.classList.contains('logged-in'));
  if (!loggedIn) {
    await p.fill('#username', 'qa-tester');
    await p.fill('#password', 'QaTester!2026');
    await Promise.all([ p.waitForNavigation({ waitUntil: 'load' }), p.click('button[name="login"]') ]);
  }
  await p.goto(BASE + '/my-account/orders/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const viewBtn = await p.$('a.view, a.woocommerce-button--view, .order-view');
  if (!viewBtn) {
    note(g, 'no existing orders for qa-tester — order-received template not tested');
    return;
  }
  await Promise.all([ p.waitForNavigation({ waitUntil: 'load' }), viewBtn.click() ]);
  await p.waitForTimeout(600);
  const orderNumber = await p.evaluate(() => document.body.innerText.match(/Order\s*#?\s*(\d+)/i)?.[1]);
  orderNumber ? pass(g, `viewing order #${orderNumber}`) : note(g, 'no order number found');

  // Check for order details, table, totals
  const orderTable = await p.$('.woocommerce-order-details, .dc-order-received, table.shop_table');
  orderTable ? pass(g, 'order details table present') : fail(g, 'no order details table');

  await p.screenshot({ path: `${OUT}/05-order-view.png`, fullPage: true });
}

// ============================================================================
// EMPTY MY-ACCOUNT SECTIONS
// ============================================================================
async function testEmptyAccountSections() {
  const g = 'empty-account';
  // Log in as qa-tester (fresh user, likely no orders/downloads)
  await p.goto(BASE + '/my-account/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const loggedIn = await p.evaluate(() => document.body.classList.contains('logged-in'));
  if (!loggedIn) {
    await p.fill('#username', 'qa-tester');
    await p.fill('#password', 'QaTester!2026');
    await Promise.all([ p.waitForNavigation({ waitUntil: 'load' }), p.click('button[name="login"]') ]);
    await p.waitForTimeout(500);
  }

  // Downloads empty state
  await p.goto(BASE + '/my-account/downloads/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const dlEmpty = await p.evaluate(() => /no downloads/i.test(document.body.innerText) ? 'yes' : null);
  dlEmpty ? pass(g, 'downloads shows no-downloads message') : note(g, 'no explicit empty downloads message');
  await p.screenshot({ path: `${OUT}/06-empty-downloads.png` });

  // Orders empty state
  await p.goto(BASE + '/my-account/orders/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const orderEmpty = await p.evaluate(() => /no order|no purchases|hasn't placed/i.test(document.body.innerText) ? 'yes' : null);
  orderEmpty ? pass(g, 'orders shows empty state') : note(g, 'orders may have prior orders or no message');
  await p.screenshot({ path: `${OUT}/07-empty-orders.png` });
}

// ============================================================================
// 404
// ============================================================================
async function test404() {
  const g = '404';
  const resp = await p.goto(BASE + '/definitely-not-a-real-page-' + Date.now(), { waitUntil: 'load' });
  await p.waitForTimeout(400);
  resp.status() === 404 ? pass(g, '404 status') : fail(g, `status ${resp.status()}`);

  const h1 = await p.$eval('h1', el => el.textContent.trim()).catch(() => null);
  h1 ? pass(g, `H1: "${h1}"`) : note(g, 'no H1');

  const linksToShop = await p.evaluate(() => Array.from(document.querySelectorAll('a')).some(a => /\/shop\/|start shopping|browse/i.test(a.textContent + a.href)));
  linksToShop ? pass(g, 'links to shop present') : note(g, 'no shop-return link');

  await p.screenshot({ path: `${OUT}/08-404.png`, fullPage: true });
}

// ============================================================================
// RUN
// ============================================================================
const groups = [
  ['empty-cart',      testEmptyCart],
  ['empty-search',    testEmptySearch],
  ['login-register',  testLoginRegister],
  ['lost-password',   testLostPassword],
  ['order-received',  testOrderReceived],
  ['empty-account',   testEmptyAccountSections],
  ['404',             test404],
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
console.log(`FINISHING RESULTS: ${pass_} pass · ${fail_} fail · ${note_} note`);
console.log('='.repeat(72));
for (const r of results.filter(r => r.s !== 'PASS')) console.log(`  [${r.s}] ${r.g}: ${r.m}`);
if (errors.length) {
  console.log('\nJS ERRORS:');
  for (const e of errors.slice(0, 5)) console.log(`  ${e.url.split('/').slice(3).join('/')}: ${e.msg.slice(0, 100)}`);
}
