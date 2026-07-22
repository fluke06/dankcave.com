import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/shipping/', { waitUntil: 'load' });
await p.waitForTimeout(500);
// Screenshot the FIRST section heading tightly
const h2 = await p.$('.dc-legal__section .dc-legal__h2');
if (h2) {
  const box = await h2.boundingBox();
  await p.screenshot({
    path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/legal/heading-zoom.png',
    clip: { x: Math.max(0, box.x - 20), y: Math.max(0, box.y - 20), width: Math.min(700, box.width + 40), height: box.height + 40 },
  });
  console.log('h2 bbox:', box);
}
// Actual runtime content — read ::before content resolved via getBoundingClientRect on before
const runtime = await p.evaluate(() => {
  const h2 = document.querySelector('.dc-legal__section .dc-legal__h2');
  const style = getComputedStyle(h2, '::before');
  // Can't read ::before rect directly but we can check what's rendered by cloning
  const range = document.createRange();
  const children = Array.from(h2.childNodes);
  return {
    content: style.content,
    display: style.display,
    width: style.width,
    height: style.height,
    // Try to read the actual computed number via a helper element
  };
});
console.log(runtime);
// Screenshot with a red border on ::before via injected style
await p.addStyleTag({ content: '.dc-legal__h2::before { outline: 2px solid red; outline-offset: 0; }' });
await p.waitForTimeout(200);
if (h2) {
  const box = await h2.boundingBox();
  await p.screenshot({
    path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/legal/heading-outlined.png',
    clip: { x: Math.max(0, box.x - 20), y: Math.max(0, box.y - 20), width: Math.min(700, box.width + 40), height: box.height + 40 },
  });
}
await b.close();
