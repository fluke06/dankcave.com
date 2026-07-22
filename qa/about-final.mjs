import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/about-us/', { waitUntil: 'load' });
await p.waitForTimeout(500);
await p.evaluate(async () => {
  const step = 400;
  const total = document.body.scrollHeight;
  for (let y = 0; y <= total; y += step) {
    window.scrollTo(0, y);
    await new Promise(r => setTimeout(r, 100));
  }
  window.scrollTo(0, 0);
  await Promise.all(Array.from(document.querySelectorAll('img[data-src]')).map(img => new Promise(res => {
    if (img.dataset.src) img.src = img.dataset.src;
    if (img.complete) return res();
    img.onload = img.onerror = res;
  })));
});
await p.waitForTimeout(600);
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/patterns/about-final.png', fullPage: true });
// Also screenshot just the top area to see breadcrumb
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/patterns/about-top.png', clip: { x: 0, y: 0, width: 1280, height: 500 } });
console.log('URL:', await p.url());
await b.close();
