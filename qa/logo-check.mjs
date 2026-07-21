import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/logo', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 400 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(400);
const el = await p.$('.site-brand--logo, .site-brand');
const box = await el.boundingBox();
await p.screenshot({ path: `${OUT}/desktop.png`, clip: { x: 0, y: 0, width: 400, height: Math.max(160, box.y + box.height + 20) } });
const info = await p.evaluate(() => {
  const img = document.querySelector('.custom-logo, .site-brand--logo img');
  return img ? { src: img.currentSrc || img.src, w: img.naturalWidth, h: img.naturalHeight } : null;
});
console.log(JSON.stringify(info));
await b.close();
