// Detailed header measurements: logo/nav/cart sizes and positions per page.
import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();

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
  ['search', '/?s=bong'],
];

console.log('page       | headerH | brandX/Y/W/H            | navX/Y/W/H              | cartX/Y/W/H');
console.log('-'.repeat(120));

for (const [label, path] of paths) {
  await p.goto('http://localhost:8090' + path, { waitUntil: 'load', timeout: 25000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => {
    const header = document.querySelector('.site-header');
    const brand  = document.querySelector('.site-brand, .site-brand--logo');
    const nav    = document.querySelector('.primary-nav');
    const cart   = document.querySelector('.cart-summary');
    const rect = el => el ? { x: Math.round(el.getBoundingClientRect().x), y: Math.round(el.getBoundingClientRect().y), w: Math.round(el.getBoundingClientRect().width), h: Math.round(el.getBoundingClientRect().height) } : null;
    return { header: rect(header), brand: rect(brand), nav: rect(nav), cart: rect(cart) };
  });
  const f = obj => obj ? `${obj.x}/${obj.y}/${obj.w}/${obj.h}` : '-';
  console.log(`${label.padEnd(10)} | ${String(info.header?.h || '?').padStart(7)} | ${f(info.brand).padEnd(23)} | ${f(info.nav).padEnd(23)} | ${f(info.cart)}`);
}

await b.close();
