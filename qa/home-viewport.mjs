// Viewport-only (no fullPage) shots of each home section for design comparison.
import { chromium } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots/home', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = 'http://localhost:8090';

const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto(BASE + '/', { waitUntil: 'load', timeout: 30000 });
await p.waitForTimeout(600);

// Full page for reference
await p.screenshot({ path: `${OUT}/00-fullpage.png`, fullPage: true });

// Then scroll through each section
const sections = await p.evaluate(() => {
  const sels = ['.hero', '.home-pyp', '.home-sbc', '.editorial-band', '.home-popular', '.home-new', '.home-blog-row', '.home-trust', '.newsletter-band'];
  return sels.map(s => {
    const el = document.querySelector(s);
    if (!el) return { sel: s, missing: true };
    const rect = el.getBoundingClientRect();
    return { sel: s, y: rect.top + window.scrollY, h: rect.height };
  });
});

for (const s of sections) {
  if (s.missing) { console.log('missing:', s.sel); continue; }
  await p.evaluate(y => window.scrollTo(0, y), s.y);
  await p.waitForTimeout(300);
  await p.screenshot({ path: `${OUT}/${s.sel.replace(/[^a-z-]/gi, '')}.png` });
  console.log('shot', s.sel, s.y, s.h);
}

await b.close();
