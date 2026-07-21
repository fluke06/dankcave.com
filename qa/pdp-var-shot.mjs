import { chromium, devices } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/pdp-var', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/', { waitUntil: 'load' });
await p.waitForTimeout(600);
await p.screenshot({ path: `${OUT}/desktop-full.png`, fullPage: true });
// summary column close-up
const el = await p.$('.pdp-summary');
if (el) {
  const box = await el.boundingBox();
  await p.screenshot({ path: `${OUT}/summary-column.png`, clip: { x: Math.max(0, box.x - 20), y: Math.max(0, box.y - 20), width: Math.min(1280, box.width + 40), height: Math.min(900, box.height + 40) } });
}
// blog index featured card
await p.goto('http://localhost:8090/blog/', { waitUntil: 'load' });
await p.waitForTimeout(500);
const feat = await p.$('.dc-blog-featured');
if (feat) {
  const box = await feat.boundingBox();
  await p.screenshot({ path: `${OUT}/blog-featured.png`, clip: { x: box.x, y: box.y, width: box.width, height: box.height } });
}
// single post hero
await p.goto('http://localhost:8090/?p=17726', { waitUntil: 'load' });
await p.waitForTimeout(500);
await p.screenshot({ path: `${OUT}/single-post-top.png`, clip: { x: 0, y: 0, width: 1280, height: 800 } });
await b.close();
