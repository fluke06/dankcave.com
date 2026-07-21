// Measure the actual leftmost x of the FIRST H1 or primary heading on each
// page — that's what a human perceives as the "gutter". Uniform gutters means
// all these numbers match.
import { chromium, devices } from 'playwright';
const b = await chromium.launch();

async function measure(path, opts = {}) {
  const ctx = await b.newContext(opts.mob ? { ...devices['iPhone 13'], deviceScaleFactor: 2 } : { viewport: { width: 1280, height: 900 } });
  const p = await ctx.newPage();
  if (opts.addCart) await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
  await p.goto('http://localhost:8090' + path, { waitUntil: 'load', timeout: 25000 });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => {
    const brand = document.querySelector('.site-brand');
    const h1    = document.querySelector('h1');
    const nl    = document.querySelector('.newsletter-band');
    const foot  = document.querySelector('.legal-bar__inner');
    const nlChild = nl ? nl.firstElementChild : null;
    const footChild = foot ? foot.firstElementChild : null;
    return {
      brandX:  brand ? Math.round(brand.getBoundingClientRect().x) : null,
      h1X:     h1 ? Math.round(h1.getBoundingClientRect().x) : null,
      nlX:     nlChild ? Math.round(nlChild.getBoundingClientRect().x) : null,
      footX:   footChild ? Math.round(footChild.getBoundingClientRect().x) : null,
      vw: window.innerWidth,
    };
  });
  await ctx.close();
  return { path, ...info };
}

const paths = [
  { path: '/', label: 'home' },
  { path: '/shop/', label: 'shop' },
  { path: '/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/', label: 'pdp' },
  { path: '/cart/', label: 'cart', addCart: true },
  { path: '/checkout/', label: 'checkout', addCart: true },
  { path: '/my-account/', label: 'account' },
  { path: '/blog/', label: 'blog' },
  { path: '/?p=17726', label: 'post' },
  { path: '/privacy-policy-2/', label: 'page' },
  { path: '/no-such-page', label: '404' },
  { path: '/?s=bong', label: 'search' },
];

console.log('Desktop 1280×900 — actual left-edge x for site-brand / first H1 / newsletter / legal footer');
console.log('page       | brandX | h1X  | nlX  | footX');
console.log('-'.repeat(60));
for (const t of paths) {
  const r = await measure(t.path, { addCart: t.addCart });
  console.log(`${t.label.padEnd(10)} | ${String(r.brandX).padStart(6)} | ${String(r.h1X).padStart(4)} | ${String(r.nlX).padStart(4)} | ${String(r.footX).padStart(4)}`);
}
console.log('\nMobile 390×844');
console.log('page       | brandX | h1X  | nlX  | footX');
console.log('-'.repeat(60));
for (const t of paths) {
  const r = await measure(t.path, { mob: true, addCart: t.addCart });
  console.log(`${t.label.padEnd(10)} | ${String(r.brandX).padStart(6)} | ${String(r.h1X).padStart(4)} | ${String(r.nlX).padStart(4)} | ${String(r.footX).padStart(4)}`);
}

await b.close();
