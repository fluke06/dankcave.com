// Verify the My Account templates render logged-out and logged-in.
// Run: node account-check.mjs

import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = process.env.PW_BASE_URL || 'http://localhost:8090';
const USERNAME = process.env.PW_USERNAME || 'admin';
const PASSWORD = process.env.PW_PASSWORD || 'admin';

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

async function loggedOut(viewport, label) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  attach(p, `login-${label}`);
  const res = await p.goto(BASE + '/my-account/', { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => ({
    hasLoginCard: !!document.querySelector('.dc-login-card'),
    hasLoginForm: !!document.querySelector('form.login'),
    hasTitle: !!document.querySelector('.dc-account__title'),
    hasEyebrow: !!document.querySelector('.dc-account__eyebrow'),
    hasLostPw: !!document.querySelector('.woocommerce-LostPassword'),
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }));
  await p.screenshot({ path: `${OUT}/account-login-${label}.png`, fullPage: true });
  console.log(`\n[login-${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  eyebrow: ${info.hasEyebrow}  title: ${info.hasTitle}  login-card: ${info.hasLoginCard}  login-form: ${info.hasLoginForm}  lost-pw: ${info.hasLostPw}`);
  await ctx.close();
}

async function loggedIn(viewport, label) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  attach(p, `dash-${label}`);

  // Log in via wp-login
  await p.goto(BASE + '/wp-login.php', { waitUntil: 'load', timeout: 20000 });
  await p.fill('#user_login', USERNAME);
  await p.fill('#user_pass', PASSWORD);
  await Promise.all([
    p.waitForNavigation({ waitUntil: 'load', timeout: 20000 }),
    p.click('#wp-submit'),
  ]);

  const res = await p.goto(BASE + '/my-account/', { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => ({
    hasAccount: !!document.querySelector('.dc-account'),
    hasHeader: !!document.querySelector('.dc-account__title'),
    hasNav: !!document.querySelector('.dc-account-nav'),
    navLinks: document.querySelectorAll('.dc-account-nav__link').length,
    hasAvatar: !!document.querySelector('.dc-account-nav__avatar'),
    hasContent: !!document.querySelector('.woocommerce-MyAccount-content'),
    hasStats: document.querySelectorAll('.dc-dash-stat').length,
    hasRecentTitle: !!document.querySelector('.dc-dash-orders__title'),
    hasPanels: document.querySelectorAll('.dc-dash-panel').length,
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }));
  await p.screenshot({ path: `${OUT}/account-dash-${label}.png`, fullPage: true });
  console.log(`\n[dash-${label}]  HTTP ${res.status()}  overflow ${info.overflow}px`);
  console.log(`  account: ${info.hasAccount}  header: ${info.hasHeader}  nav: ${info.hasNav} (${info.navLinks} links, avatar ${info.hasAvatar})`);
  console.log(`  content: ${info.hasContent}  stats: ${info.hasStats}  recent-title: ${info.hasRecentTitle}  panels: ${info.hasPanels}`);

  // Also check orders endpoint
  const res2 = await p.goto(BASE + '/my-account/orders/', { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(400);
  await p.screenshot({ path: `${OUT}/account-orders-${label}.png`, fullPage: true });
  console.log(`  /orders HTTP ${res2.status()}`);

  await ctx.close();
}

await loggedOut({ viewport: { width: 1280, height: 900 } }, 'desktop');
await loggedIn({ viewport: { width: 1280, height: 900 } }, 'desktop');
await loggedIn({ ...devices['iPhone 13'], deviceScaleFactor: 2 }, 'mobile');

await b.close();

console.log(`\nFailed resources (4xx/5xx): ${failedResources.length}`);
failedResources.slice(0, 15).forEach(r => console.log(' -', ...r));
console.log(`\nErrors: ${errors.length}`);
errors.slice(0, 5).forEach(e => console.log(' -', ...e));
process.exit(errors.length ? 1 : 0);
