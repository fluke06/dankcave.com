// Zoom into mobile header area on the home page.
import { chromium, devices } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/mobile-header', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();

// iPhone 13 = 390x844
const ctx = await b.newContext({ ...devices['iPhone 13'], deviceScaleFactor: 2 });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(500);

// Full page for context
await p.screenshot({ path: `${OUT}/home-fullpage.png`, fullPage: true });

// Just header + hero top
await p.screenshot({ path: `${OUT}/header-and-hero.png`, clip: { x: 0, y: 0, width: 390, height: 400 } });

// Just the header alone
const h = await p.$('.site-header');
if (h) {
  const box = await h.boundingBox();
  await p.screenshot({ path: `${OUT}/header-only.png`, clip: { x: 0, y: 0, width: 390, height: Math.ceil(box.height + 20) } });
  console.log('header box:', box);
}

// Test hamburger toggle
await p.click('.site-header__toggle');
await p.waitForTimeout(400);
await p.screenshot({ path: `${OUT}/header-menu-open.png`, clip: { x: 0, y: 0, width: 390, height: 500 } });

// Also computed styles of key elements
const info = await p.evaluate(() => {
  const header = document.querySelector('.site-header');
  const inner = document.querySelector('.site-header__inner');
  const brand = document.querySelector('.site-brand');
  const brandText = document.querySelector('.site-brand__text');
  const cart = document.querySelector('.cart-summary');
  const toggle = document.querySelector('.site-header__toggle');
  const mob = document.querySelector('.primary-nav-mobile');
  const g = el => el ? { display: getComputedStyle(el).display, w: el.getBoundingClientRect().width, h: el.getBoundingClientRect().height } : null;
  return {
    header: g(header),
    inner: g(inner),
    brand: g(brand),
    brandText: brandText ? { fs: getComputedStyle(brandText).fontSize, family: getComputedStyle(brandText).fontFamily.slice(0, 40) } : null,
    cart: g(cart),
    toggle: g(toggle),
    mob: g(mob),
    vpWidth: window.innerWidth,
  };
});
console.log(JSON.stringify(info, null, 2));

await b.close();
