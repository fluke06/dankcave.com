// Full audit — screenshots every template we have shipped and dumps computed
// styles for a few consistency checkpoints (h1 sizes, section padding, header
// treatment). Run: node full-audit.mjs
import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots/audit', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = 'http://localhost:8090';
const PRODUCT_ID = '17009';
const PDP_PATH   = '/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/';
const POST_ID    = '17726';
const PAGE_URL   = '/privacy-policy-2/';
const USER = 'dcqa', PASS = 'dcqa2026!';

const b = await chromium.launch();
const report = [];

async function cap(label, path, viewport, opts = {}) {
  const ctx = await b.newContext(viewport);
  const p = await ctx.newPage();
  if (opts.login) {
    await p.goto(BASE + '/wp-login.php', { waitUntil: 'load', timeout: 20000 });
    await p.fill('#user_login', USER);
    await p.fill('#user_pass', PASS);
    await Promise.all([p.waitForNavigation({ waitUntil: 'load', timeout: 45000 }), p.click('#wp-submit')]);
  }
  if (opts.addToCart) {
    await p.goto(BASE + `/?add-to-cart=${PRODUCT_ID}`, { waitUntil: 'load', timeout: 20000 });
  }
  await p.goto(BASE + path, { waitUntil: 'load', timeout: 25000 });
  await p.waitForTimeout(500);
  const style = await p.evaluate(() => {
    const h1 = document.querySelector('h1');
    const section = document.querySelector('main > section, main > article, .pdp, .dc-cart, .dc-checkout, .dc-account, .dc-blog, .dc-post, .dc-page, .dc-search, .dc-404, .wc-archive');
    const header = document.querySelector('.site-header');
    const footer = document.querySelector('.newsletter-band, footer');
    const getPad = el => el ? (getComputedStyle(el).padding || '') : null;
    const getFs  = el => el ? getComputedStyle(el).fontSize : null;
    return {
      h1_fs: getFs(h1),
      h1_text: (h1?.textContent || '').slice(0, 60).trim(),
      section_pad: section ? getComputedStyle(section).paddingTop + ' ' + getComputedStyle(section).paddingLeft : null,
      section_tag: section ? section.className.slice(0, 40) : null,
      header_bg: header ? getComputedStyle(header).backgroundColor : null,
      body_bg: getComputedStyle(document.body).backgroundColor,
      overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
      footer: !!footer,
    };
  });
  await p.screenshot({ path: `${OUT}/${label}.png`, fullPage: true });
  report.push({ label, ...style });
  await ctx.close();
}

const desk = { viewport: { width: 1280, height: 900 } };
const mob  = { ...devices['iPhone 13'], deviceScaleFactor: 2 };

await cap('01-home-desk',       '/',                       desk);
await cap('01-home-mob',        '/',                       mob);
await cap('02-shop-desk',       '/shop/',                  desk);
await cap('02-shop-mob',        '/shop/',                  mob);
await cap('03-pdp-desk',        PDP_PATH,                  desk);
await cap('03-pdp-mob',         PDP_PATH,                  mob);
await cap('04-cart-empty-desk', '/cart/',                  desk);
await cap('04-cart-full-desk',  '/cart/',                  desk, { addToCart: true });
await cap('04-cart-full-mob',   '/cart/',                  mob,  { addToCart: true });
await cap('05-checkout-desk',   '/checkout/',              desk, { addToCart: true });
await cap('05-checkout-mob',    '/checkout/',              mob,  { addToCart: true });
await cap('06-account-out-desk','/my-account/',            desk);
await cap('06-account-in-desk', '/my-account/',            desk, { login: true });
await cap('06-account-orders',  '/my-account/orders/',     desk, { login: true });
await cap('07-blog-desk',       '/blog/',                  desk);
await cap('07-blog-mob',        '/blog/',                  mob);
await cap('08-post-desk',       '/?p=' + POST_ID,          desk);
await cap('08-post-mob',        '/?p=' + POST_ID,          mob);
await cap('09-page-desk',       PAGE_URL,                  desk);
await cap('09-page-mob',        PAGE_URL,                  mob);
await cap('10-404-desk',        '/no-such-page',           desk);
await cap('11-search-desk',     '/?s=bong',                desk);

await b.close();

console.log('label | h1_fs | section_pad | header_bg | body_bg | overflow');
console.log('-'.repeat(120));
report.forEach(r => {
  console.log(`${r.label.padEnd(22)} | ${(r.h1_fs || '-').padEnd(6)} | ${(r.section_pad || '-').slice(0, 20).padEnd(20)} | ${(r.header_bg || '-').padEnd(20)} | ${(r.body_bg || '-').padEnd(18)} | ${r.overflow}`);
});
