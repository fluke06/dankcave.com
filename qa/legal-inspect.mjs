import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/shipping/', { waitUntil: 'load' });
await p.waitForTimeout(500);
const info = await p.evaluate(() => {
  const h2 = document.querySelector('.dc-legal__section .dc-legal__h2');
  const section = document.querySelector('.dc-legal__section');
  if (!h2 || !section) return { error: 'not found' };
  const before = window.getComputedStyle(h2, '::before');
  return {
    h2Text: h2.textContent.trim().slice(0, 40),
    sectionCounterIncrement: window.getComputedStyle(section).counterIncrement,
    dcLegalCounterReset: window.getComputedStyle(document.querySelector('.dc-legal')).counterReset,
    h2BeforeContent: before.content,
    h2BeforeDisplay: before.display,
    h2BeforeBg: before.backgroundColor,
    h2BeforeColor: before.color,
    titleTag: document.querySelector('.dc-legal__title')?.tagName,
    titleClasses: document.querySelector('.dc-legal__title')?.className,
  };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
