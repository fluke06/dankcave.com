import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/cart-thumb', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.goto('http://localhost:8090/cart/', { waitUntil: 'load', timeout: 30000 });
await p.waitForTimeout(500);
const line = await p.$('.dc-cart-line');
if (line) {
  const box = await line.boundingBox();
  await p.screenshot({ path: `${OUT}/cart-line.png`, clip: { x: box.x, y: box.y - 10, width: Math.min(1280 - box.x, box.width + 20), height: box.height + 20 } });
}
await p.goto('http://localhost:8090/checkout/', { waitUntil: 'load', timeout: 30000 });
await p.waitForTimeout(600);
const rev = await p.$('.dc-review-item');
if (rev) {
  const box = await rev.boundingBox();
  await p.screenshot({ path: `${OUT}/checkout-review-item.png`, clip: { x: box.x - 10, y: box.y - 10, width: Math.min(1280 - box.x + 10, box.width + 20), height: box.height + 20 } });
}
const info = await p.evaluate(() => {
  const t = document.querySelector('.dc-review-item__thumb');
  const media = document.querySelector('.dc-review-item__thumb-media');
  const img = document.querySelector('.dc-review-item__thumb img');
  const qty = document.querySelector('.dc-review-item__qty');
  return {
    thumbOverflow: t ? getComputedStyle(t).overflow : null,
    mediaHasClip: media ? getComputedStyle(media).overflow : null,
    imgBox: img ? img.getBoundingClientRect() : null,
    qtyBox: qty ? qty.getBoundingClientRect() : null,
    thumbBox: t ? t.getBoundingClientRect() : null,
  };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
