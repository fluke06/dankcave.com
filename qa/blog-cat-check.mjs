import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

for (const slug of ['bongs', 'vaporizer', 'delta8', 'rolling-papers']) {
  await p.goto('http://localhost:8090/category/' + slug + '/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  await p.evaluate(async () => {
    const step = 500; const total = document.body.scrollHeight;
    for (let y = 0; y <= total; y += step) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 60)); }
    window.scrollTo(0, 0);
    await Promise.all(Array.from(document.querySelectorAll('img[data-src]')).map(img => new Promise(res => {
      if (img.dataset.src) img.src = img.dataset.src;
      if (img.complete) return res(); img.onload = img.onerror = res;
    })));
  });
  await p.waitForTimeout(400);
  const info = await p.evaluate(() => ({
    heading: document.querySelector('.dc-blog__title')?.textContent?.trim(),
    activeChip: document.querySelector('.dc-blog__chip.is-active')?.textContent?.trim(),
    postsInGrid: document.querySelectorAll('.dc-blog__grid > *').length,
    hasFeatured: !!document.querySelector('.dc-blog-featured'),
    empty: !!document.querySelector('.dc-blog__empty'),
  }));
  await p.screenshot({ path: `/Users/christiandizon/Sites/dankcave/qa/screenshots/blog-cat/${slug}.png`, fullPage: true });
  console.log(`${slug}: ` + JSON.stringify(info));
}
if (errors.length) console.log('JS errors:', errors.slice(0, 3));
await b.close();
