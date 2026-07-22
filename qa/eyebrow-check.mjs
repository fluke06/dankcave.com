import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/about-us-3/', { waitUntil: 'load' });
await p.waitForTimeout(700);
const info = await p.evaluate(() => {
  const eb = document.querySelector('.pattern-commitment .pattern-eyebrow');
  if (!eb) return { found: false };
  const cs = getComputedStyle(eb);
  const r = eb.getBoundingClientRect();
  return {
    text: eb.textContent,
    color: cs.color, bg: cs.backgroundColor, display: cs.display, visibility: cs.visibility,
    fontSize: cs.fontSize, opacity: cs.opacity, margin: cs.margin,
    box: `${Math.round(r.x)},${Math.round(r.y)} ${Math.round(r.width)}x${Math.round(r.height)}`,
    className: eb.className,
    parent: eb.parentElement?.tagName + '.' + eb.parentElement?.className,
  };
});
console.log(JSON.stringify(info, null, 2));

// Check the copy container
const copyInfo = await p.evaluate(() => {
  const copy = document.querySelector('.pattern-commitment__copy');
  const kids = copy ? Array.from(copy.children).map(c => c.tagName + '.' + c.className) : [];
  return { childrenTags: kids };
});
console.log('copy children:', JSON.stringify(copyInfo, null, 2));
await b.close();
