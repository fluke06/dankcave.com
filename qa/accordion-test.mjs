// Verify accordion smoothness — open FAQ on Contact + PDP tab, measure the
// transition happens across multiple frames instead of snapping.
import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
p.on('pageerror', e => console.log('JS ERR', e.message));

// FAQ accordion on Contact page
await p.goto('http://localhost:8090/contact-us/', { waitUntil: 'load' });
await p.waitForTimeout(600);

// Sample every 60ms and log the height of the closed FAQ item's body during open
const heights = await p.evaluate(async () => {
  const details = document.querySelectorAll('details.pattern-contact__acc');
  // Pick the SECOND one which starts closed
  const acc = details[1];
  const body = acc.querySelector('.dc-acc__body');
  if (!acc || !body) return { error: 'no accordion body found', count: details.length, hasBody: !!body };
  const samples = [];
  const summary = acc.querySelector('summary');
  summary.click();
  const start = performance.now();
  while (performance.now() - start < 500) {
    samples.push({ t: Math.round(performance.now() - start), h: Math.round(body.getBoundingClientRect().height) });
    await new Promise(r => requestAnimationFrame(r));
  }
  return { open: acc.hasAttribute('open'), samples: samples.filter((_, i) => i % 3 === 0) };
});
console.log('FAQ open transition heights:', JSON.stringify(heights, null, 2));

// Screenshot open state
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/patterns/faq-open.png', clip: { x: 640, y: 350, width: 640, height: 500 } });

await b.close();
