import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p.waitForTimeout(500);
// Add 2 items to compare
await p.evaluate(() => {
  const btns = document.querySelectorAll('[data-dc-compare]');
  btns[0]?.click();
  btns[3]?.click();
});
await p.waitForTimeout(800);
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/compare/tray-remove.png' });
const info = await p.evaluate(() => {
  const thumb = document.querySelector('.dc-compare-tray__thumb');
  const remove = document.querySelector('.dc-compare-tray__thumb-remove');
  if (!thumb || !remove) return null;
  return {
    thumbRect: thumb.getBoundingClientRect(),
    removeRect: remove.getBoundingClientRect(),
    thumbOverflow: getComputedStyle(thumb).overflow,
    removeZ: getComputedStyle(remove).zIndex,
  };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
