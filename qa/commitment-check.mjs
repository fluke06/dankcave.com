import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
p.on('pageerror', e => console.log('JS ERR', e.message));
await p.goto('http://localhost:8090/about-us-3/', { waitUntil: 'load' });
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
await p.waitForTimeout(800);

// Screenshot just the commitment section
const el = await p.$('.pattern-commitment');
if (el) {
  await el.scrollIntoViewIfNeeded();
  await p.waitForTimeout(300);
  await el.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/patterns/commitment-only.png' });
}

// Log tile info
const info = await p.evaluate(() => {
  const tiles = document.querySelectorAll('.pattern-commitment .pattern-tile');
  return Array.from(tiles).map(t => {
    const img = t.querySelector('img');
    const r = t.getBoundingClientRect();
    const ir = img ? img.getBoundingClientRect() : null;
    return {
      label: t.querySelector('.pattern-tile__label')?.textContent,
      tileWH: `${Math.round(r.width)}x${Math.round(r.height)}`,
      imgWH: ir ? `${Math.round(ir.width)}x${Math.round(ir.height)}` : 'no-img',
      imgSrc: img ? img.src.split('/').pop() : 'no-img',
      imgNaturalWH: img ? `${img.naturalWidth}x${img.naturalHeight}` : 'no-img',
    };
  });
});
console.log(JSON.stringify(info, null, 2));

// Also inner container width
const layout = await p.evaluate(() => {
  const inner = document.querySelector('.pattern-commitment__inner');
  const copy = document.querySelector('.pattern-commitment__copy');
  const tiles = document.querySelector('.pattern-commitment__tiles');
  return {
    inner: inner ? `${Math.round(inner.getBoundingClientRect().width)}px` : '?',
    copy: copy ? `${Math.round(copy.getBoundingClientRect().width)}px` : '?',
    tiles: tiles ? `${Math.round(tiles.getBoundingClientRect().width)}px` : '?',
    tilesCS: tiles ? getComputedStyle(tiles).gridTemplateColumns : '?',
  };
});
console.log('layout:', JSON.stringify(layout));

await b.close();
