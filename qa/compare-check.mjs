import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/compare', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p.waitForTimeout(600);

// Hover first card, screenshot showing tooltip
const first = await p.$('.product-card');
await first.hover();
await p.waitForTimeout(300);
const wishBtn = await first.$('[data-dc-wishlist]');
await wishBtn.hover();
await p.waitForTimeout(400);
await p.screenshot({ path: `${OUT}/tooltip.png`, clip: { x: 0, y: 0, width: 700, height: 700 } });

// Click compare buttons on two different products
const compareBtns = await p.$$('[data-dc-compare]');
if (compareBtns.length >= 2) {
  await compareBtns[0].evaluate(el => el.click());
  await p.waitForTimeout(300);
  await compareBtns[3].evaluate(el => el.click()); // pick a different card's compare button
  await p.waitForTimeout(700); // wait for tray fetch
}

// Screenshot tray
await p.evaluate(() => window.scrollTo(0, 0));
await p.waitForTimeout(200);
await p.screenshot({ path: `${OUT}/tray.png` });

const trayState = await p.evaluate(() => {
  const tray = document.getElementById('dc-compare-tray');
  return {
    hidden: tray.hidden,
    visible: tray.getAttribute('data-visible'),
    count: tray.querySelector('[data-dc-compare-count]')?.textContent,
    thumbCount: tray.querySelectorAll('.dc-compare-tray__thumb').length,
  };
});
console.log('tray:', JSON.stringify(trayState));

// Click "Compare →" in tray
await p.click('[data-dc-compare-open]');
await p.waitForSelector('.dc-compare-table', { timeout: 5000 });
await p.waitForTimeout(500);
await p.screenshot({ path: `${OUT}/modal.png` });

const modalState = await p.evaluate(() => {
  const modal = document.getElementById('dc-compare-modal');
  return {
    hidden: modal.hidden,
    open: modal.getAttribute('data-open'),
    productCols: modal.querySelectorAll('.dc-compare-table__head').length,
    rows: modal.querySelectorAll('.dc-compare-table tbody tr').length,
  };
});
console.log('modal:', JSON.stringify(modalState));

console.log('errors:', errors);
await b.close();
