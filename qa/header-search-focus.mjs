import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/header-search-focus', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 400 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(300);
// Tab through until search pill is focused
await p.click('body');
await p.keyboard.press('Tab'); await p.keyboard.press('Tab'); await p.keyboard.press('Tab');
await p.keyboard.press('Tab'); await p.keyboard.press('Tab'); await p.keyboard.press('Tab');
await p.waitForTimeout(200);
// Explicitly focus for a reliable close-up
await p.evaluate(() => document.querySelector('.header-search-pill').focus());
await p.waitForTimeout(200);
const el = await p.$('.header-search-pill');
const box = await el.boundingBox();
await p.screenshot({ path: `${OUT}/focused.png`, clip: { x: box.x - 20, y: box.y - 20, width: box.width + 40, height: box.height + 40 } });
const cs = await p.evaluate(() => {
  const el = document.querySelector('.header-search-pill');
  const s = getComputedStyle(el);
  return { outline: s.outline, outlineWidth: s.outlineWidth, boxShadow: s.boxShadow };
});
console.log(JSON.stringify(cs));
await b.close();
