import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/search-modal', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(500);

// Click the search pill
await p.click('.header-search-pill');
await p.waitForTimeout(400);
await p.screenshot({ path: `${OUT}/opened.png`, fullPage: false });

const info1 = await p.evaluate(() => {
  const m = document.getElementById('search-modal');
  return { hidden: m.hidden, open: m.getAttribute('data-open'), inputFocused: document.activeElement && document.activeElement.matches('.search-modal__input') };
});
console.log('after open:', JSON.stringify(info1));

// Type a query
await p.fill('.search-modal__input', 'bong');
// Trigger input event manually so debounce fires
await p.evaluate(() => document.querySelector('.search-modal__input').dispatchEvent(new Event('input', { bubbles: true })));
await p.waitForTimeout(1500); // debounce (220) + REST fetch
await p.screenshot({ path: `${OUT}/typed.png`, fullPage: false });

const info2 = await p.evaluate(() => {
  const results = document.querySelector('[data-search-results]');
  return { hidden: results.hidden, count: results.querySelectorAll('.search-modal-item').length, sample: results.querySelector('.search-modal-item__title') ? results.querySelector('.search-modal-item__title').textContent : null };
});
console.log('after typing:', JSON.stringify(info2));

// Close with Escape
await p.keyboard.press('Escape');
await p.waitForTimeout(300);
await p.screenshot({ path: `${OUT}/closed.png`, fullPage: false });
const info3 = await p.evaluate(() => ({ hidden: document.getElementById('search-modal').hidden }));
console.log('after escape:', JSON.stringify(info3));

console.log('errors:', errors);
await b.close();
