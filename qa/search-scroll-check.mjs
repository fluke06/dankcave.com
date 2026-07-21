import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/search-modal', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
// Use a short viewport so results are guaranteed to overflow
const ctx = await b.newContext({ viewport: { width: 1280, height: 700 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(400);
await p.click('.header-search-pill');
await p.waitForTimeout(300);
await p.fill('.search-modal__input', 'bong');
await p.evaluate(() => document.querySelector('.search-modal__input').dispatchEvent(new Event('input', { bubbles: true })));
await p.waitForTimeout(1500);

const state1 = await p.evaluate(() => {
  const modal = document.getElementById('search-modal');
  return {
    scrollHeight: modal.scrollHeight,
    clientHeight: modal.clientHeight,
    isScrollable: modal.scrollHeight > modal.clientHeight,
    overflowY: getComputedStyle(modal).overflowY,
  };
});
console.log('before scroll:', JSON.stringify(state1));

// Take initial screenshot
await p.screenshot({ path: `${OUT}/scroll-top.png` });

// Scroll the modal down halfway
await p.evaluate(() => { document.getElementById('search-modal').scrollTop = 400; });
await p.waitForTimeout(400);
const state2 = await p.evaluate(() => ({ scrollTop: document.getElementById('search-modal').scrollTop }));
console.log('after scroll:', JSON.stringify(state2));
await p.screenshot({ path: `${OUT}/scroll-middle.png` });

// Scroll to bottom
await p.evaluate(() => { const m = document.getElementById('search-modal'); m.scrollTop = m.scrollHeight; });
await p.waitForTimeout(300);
await p.screenshot({ path: `${OUT}/scroll-bottom.png` });

console.log('errors:', errors);
await b.close();
