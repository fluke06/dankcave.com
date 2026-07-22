import { chromium } from 'playwright';
import fs from 'fs';
fs.mkdirSync('/Users/christiandizon/Sites/dankcave/qa/screenshots/legal', { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
p.on('pageerror', e => console.log('JS ERR', e.message));
for (const slug of ['shipping', 'returns', 'privacy-policy', 'terms']) {
  await p.goto('http://localhost:8090/' + slug + '/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  await p.screenshot({ path: `/Users/christiandizon/Sites/dankcave/qa/screenshots/legal/${slug}.png`, fullPage: true });
  const info = await p.evaluate(() => ({
    h1: document.querySelector('h1')?.textContent?.trim(),
    sections: document.querySelectorAll('.dc-legal__section').length,
    firstH2: document.querySelector('.dc-legal__h2')?.textContent?.trim(),
    hasProgress: !!document.querySelector('.dc-legal__progress'),
    overflow: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth,
  }));
  console.log(slug, JSON.stringify(info));
}
await b.close();
