import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/pdp-featured-only', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
// Featured-image-only product (no gallery)
await p.goto('http://localhost:8090/shop/rollings/rolling-papers/zig-zag-rolling-papers-king-size/', { waitUntil: 'load' });
await p.waitForTimeout(500);
const info = await p.evaluate(() => {
  const hero = document.querySelector('.pdp-gallery__hero');
  const img = document.querySelector('.pdp-gallery__image');
  const thumbs = document.querySelectorAll('.pdp-gallery__thumb');
  return {
    heroRect: hero?.getBoundingClientRect(),
    imgSrc: img?.getAttribute('src'),
    imgWidth: img?.naturalWidth,
    imgHeight: img?.naturalHeight,
    thumbCount: thumbs.length,
  };
});
console.log(JSON.stringify(info, null, 2));
await p.screenshot({ path: `${OUT}/desktop.png`, fullPage: true });
await b.close();
