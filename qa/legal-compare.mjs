// Side-by-side compare: render each design reference (.dc.html file) next to
// my WordPress rendering at the same viewport, then screenshot both.
import { chromium } from 'playwright';
import fs from 'fs';

const OUT = '/Users/christiandizon/Sites/dankcave/qa/screenshots/legal-compare';
fs.mkdirSync(OUT, { recursive: true });

const pairs = [
  { name: 'shipping', design: '/Users/christiandizon/Sites/toolplaybook/Dankcave - Shipping.dc.html', live: 'http://localhost:8090/shipping/' },
  { name: 'returns',  design: '/Users/christiandizon/Sites/toolplaybook/Dankcave - Returns.dc.html',  live: 'http://localhost:8090/returns/' },
  { name: 'privacy',  design: '/Users/christiandizon/Sites/toolplaybook/Dankcave - Privacy.dc.html',  live: 'http://localhost:8090/privacy-policy/' },
  { name: 'terms',    design: '/Users/christiandizon/Sites/toolplaybook/Dankcave - Terms.dc.html',    live: 'http://localhost:8090/terms/' },
];

const b = await chromium.launch();

for (const pair of pairs) {
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
  // Design
  const dp = await ctx.newPage();
  await dp.goto('file://' + pair.design, { waitUntil: 'load' });
  await dp.waitForTimeout(600);
  await dp.screenshot({ path: `${OUT}/${pair.name}-design.png`, fullPage: true });
  // My rendering
  const lp = await ctx.newPage();
  await lp.goto(pair.live, { waitUntil: 'load' });
  await lp.waitForTimeout(600);
  await lp.screenshot({ path: `${OUT}/${pair.name}-live.png`, fullPage: true });

  // Measure key elements on both
  const measure = async (p, sel) => p.evaluate(s => {
    const el = document.querySelector(s);
    if (!el) return null;
    const cs = getComputedStyle(el);
    return { fs: cs.fontSize, fw: cs.fontWeight, lh: cs.lineHeight, ls: cs.letterSpacing, text: el.textContent.trim().slice(0, 40) };
  }, sel);

  const d_h1 = await measure(dp, 'h1');
  const l_h1 = await measure(lp, 'h1');
  const d_h2 = await measure(dp, 'h2');
  const l_h2 = await measure(lp, '.dc-legal__h2');
  console.log(`\n== ${pair.name} ==`);
  console.log(' design h1:', d_h1);
  console.log(' live   h1:', l_h1);
  console.log(' design h2:', d_h2);
  console.log(' live   h2:', l_h2);

  await ctx.close();
}
await b.close();
