import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/toast', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.goto('http://localhost:8090/cart/', { waitUntil: 'load' });
await p.waitForTimeout(500);

// Close-up of the notice bar
const notice = await p.$('.woocommerce-message');
if (notice) {
  const box = await notice.boundingBox();
  await p.screenshot({
    path: `${OUT}/cart-toast.png`,
    clip: { x: box.x - 20, y: box.y - 20, width: Math.min(1280, box.width + 40), height: box.height + 40 },
  });
}

// Dump all applied styles on the notice
const cs = await p.evaluate(() => {
  const n = document.querySelector('.woocommerce-message');
  if (!n) return null;
  const s = getComputedStyle(n);
  return {
    background: s.backgroundColor,
    color: s.color,
    padding: s.padding,
    borderLeft: s.borderLeft,
    border: s.border,
    borderTop: s.borderTop,
    borderRadius: s.borderRadius,
    boxShadow: s.boxShadow,
    outline: s.outline,
    outlineWidth: s.outlineWidth,
    fontSize: s.fontSize,
    fontFamily: s.fontFamily.slice(0, 40),
    display: s.display,
    marginLeft: s.marginLeft,
    // Check the ::before pseudo-element
    beforeContent: getComputedStyle(n, '::before').content,
    beforeDisplay: getComputedStyle(n, '::before').display,
    beforeBg: getComputedStyle(n, '::before').backgroundColor,
    beforeLeft: getComputedStyle(n, '::before').left,
  };
});
console.log(JSON.stringify(cs, null, 2));

// Try adding to cart from shop archive too
await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p.waitForTimeout(400);
await p.screenshot({ path: `${OUT}/shop-with-toast.png`, clip: { x: 0, y: 0, width: 1280, height: 400 } });

await b.close();
