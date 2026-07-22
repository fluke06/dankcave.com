import { chromium, devices } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ ...devices['iPhone 13'], deviceScaleFactor: 2 });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
const info = await p.evaluate(() => {
  const results = {
    hoverNone: window.matchMedia('(hover: none)').matches,
    pointerCoarse: window.matchMedia('(pointer: coarse)').matches,
    ua: navigator.userAgent,
    maxTouch: navigator.maxTouchPoints,
  };
  const a = document.querySelector('.product-card__hover-actions');
  if (a) {
    const cs = getComputedStyle(a);
    results.actionsOpacity = cs.opacity;
    results.actionsTransform = cs.transform;
    results.actionsPointerEvents = cs.pointerEvents;
  }
  const links = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
    .filter(l => l.href.includes('theme.css'))
    .map(l => l.href);
  results.themeCss = links;
  return results;
});
console.log(JSON.stringify(info, null, 2));
await b.close();
