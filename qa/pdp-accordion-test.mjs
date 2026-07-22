import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
p.on('pageerror', e => console.log('JS ERR', e.message));
await p.goto('http://localhost:8090/shop/rollings/rolling-papers/zig-zag-rolling-papers-king-size/', { waitUntil: 'load' });
await p.waitForTimeout(600);
const info = await p.evaluate(async () => {
  const accs = document.querySelectorAll('details.pdp-accordion');
  if (!accs.length) return { error: 'no pdp accordion found' };
  const acc = accs[0];
  const body = acc.querySelector('.pdp-accordion__body');
  if (!body) return { error: 'no body', accCount: accs.length };
  const initHeight = body.getBoundingClientRect().height;
  const initOpen = acc.hasAttribute('open');
  const samples = [];
  acc.querySelector('summary').click();
  const start = performance.now();
  while (performance.now() - start < 500) {
    samples.push({ t: Math.round(performance.now() - start), h: Math.round(body.getBoundingClientRect().height) });
    await new Promise(r => requestAnimationFrame(r));
  }
  return { initOpen, initHeight, samples: samples.filter((_, i) => i % 2 === 0), nowOpen: acc.hasAttribute('open') };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
