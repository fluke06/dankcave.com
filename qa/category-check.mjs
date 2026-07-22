import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/category', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

// From home, click a "Shop by category" tile
await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(500);
const tile = await p.$('.category-tile');
if (tile) {
  const href = await tile.getAttribute('href');
  console.log('home tile href:', href);
  const [nav] = await Promise.all([
    p.waitForNavigation({ waitUntil: 'load', timeout: 15000 }),
    tile.click(),
  ]);
  console.log('after click url:', p.url(), 'status:', nav.status());
  await p.waitForTimeout(500);
  await p.screenshot({ path: `${OUT}/from-home-tile.png` });
  const info = await p.evaluate(() => ({
    title: document.querySelector('h1')?.textContent?.trim(),
    isArchive: !!document.querySelector('.wc-archive'),
    productCount: document.querySelectorAll('.product-card').length,
    is404: !!document.querySelector('.dc-404'),
  }));
  console.log('page state:', JSON.stringify(info));
}

// Also check filter-sidebar category link on shop
await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p.waitForTimeout(400);
const sidebarLink = await p.$('.shop-filters__row');
if (sidebarLink) {
  const href = await sidebarLink.getAttribute('href');
  console.log('sidebar category href:', href);
  await p.goto(href, { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const info = await p.evaluate(() => ({
    url: window.location.href,
    title: document.querySelector('h1')?.textContent?.trim(),
    productCount: document.querySelectorAll('.product-card').length,
  }));
  console.log('sidebar dest:', JSON.stringify(info));
  await p.screenshot({ path: `${OUT}/from-filter-sidebar.png` });
}

console.log('errors:', errors);
await b.close();
