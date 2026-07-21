// Overlay a vertical guide line at x=48 on each page top viewport so you can
// see whether the brand, section header, and content edges line up.
import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/gutter', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();

const paths = [
  ['home', '/'],
  ['shop', '/shop/'],
  ['pdp', '/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/'],
  ['cart', '/cart/'],
  ['checkout', '/checkout/'],
  ['account', '/my-account/'],
  ['blog', '/blog/'],
  ['post', '/?p=17726'],
  ['page', '/privacy-policy-2/'],
  ['404', '/no-such-page'],
  ['search', '/?s=bong'],
];

async function shoot(label, path, addCart) {
  const ctx = await b.newContext({ viewport: { width: 1280, height: 500 } });
  const p = await ctx.newPage();
  if (addCart) await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
  await p.goto('http://localhost:8090' + path, { waitUntil: 'load', timeout: 25000 });
  await p.waitForTimeout(500);
  // Overlay a magenta vertical line at x=48
  await p.evaluate(() => {
    const g = document.createElement('div');
    g.style.cssText = 'position:fixed;top:0;bottom:0;left:47px;width:2px;background:magenta;z-index:99999;pointer-events:none';
    document.body.appendChild(g);
    const g2 = document.createElement('div');
    g2.style.cssText = 'position:fixed;top:0;bottom:0;right:47px;width:2px;background:magenta;z-index:99999;pointer-events:none';
    document.body.appendChild(g2);
  });
  await p.screenshot({ path: `${OUT}/${label}.png` });
  await ctx.close();
}

for (const [label, path] of paths) {
  await shoot(label, path, ['cart', 'checkout'].includes(label));
  console.log('shot', label);
}
await b.close();
