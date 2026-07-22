// Render + screenshot the About + Contact pages at desktop and mobile,
// plus check the resolved HTML has all 7 pattern sections.
import { chromium, devices } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/patterns', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();

const pages = [
  { name: 'about',   url: 'http://localhost:8090/about-us-3/', expect: ['pattern-about-hero','pattern-commitment','pattern-why','pattern-showcase','pattern-satisfaction','pattern-community'] },
  { name: 'contact', url: 'http://localhost:8090/contact-us/', expect: ['pattern-contact'] },
];

for (const spec of pages) {
  for (const [dev, label] of [[{ viewport: { width: 1280, height: 900 } }, 'desktop'], [{ ...devices['iPhone 13'], deviceScaleFactor: 2 }, 'mobile']]) {
    const ctx = await b.newContext(dev);
    const p = await ctx.newPage();
    const jsErrors = [];
    p.on('pageerror', e => jsErrors.push(e.message));
    await p.goto(spec.url, { waitUntil: 'load' });
    await p.waitForTimeout(500);
    // Scroll the whole page to trigger Smush lazy-load, wait for every img to load, then top.
    await p.evaluate(async () => {
      const step = 400;
      const total = document.body.scrollHeight;
      for (let y = 0; y <= total; y += step) {
        window.scrollTo(0, y);
        await new Promise(r => setTimeout(r, 80));
      }
      window.scrollTo(0, 0);
      // Force any remaining lazy imgs to load
      await Promise.all( Array.from(document.querySelectorAll('img[data-src]')).map(img => new Promise(res => {
        if (img.dataset.src) img.src = img.dataset.src;
        if (img.complete) return res();
        img.onload = img.onerror = res;
      })) );
    });
    await p.waitForTimeout(800);
    await p.screenshot({ path: `${OUT}/${spec.name}-${label}.png`, fullPage: true });
    const status = await p.evaluate((expect) => {
      const found = {};
      for (const cls of expect) found[cls] = !!document.querySelector('.' + cls);
      const overflow = Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth;
      return { found, overflow };
    }, spec.expect);
    console.log(`${spec.name}@${label}: overflow=${status.overflow}px`, JSON.stringify(status.found));
    if (jsErrors.length) console.log(`  JS errors:`, jsErrors.slice(0, 2));
    await ctx.close();
  }
}
await b.close();
console.log('\nScreenshots →', OUT);
