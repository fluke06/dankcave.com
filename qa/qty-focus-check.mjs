import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/qty-focus', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.goto('http://localhost:8090/cart/', { waitUntil: 'load', timeout: 30000 });
await p.waitForTimeout(600);

// Focus the quantity input
const input = await p.$('.dc-cart-line__qty input.qty');
await input.focus();
await p.waitForTimeout(300);

const cs = await p.evaluate(() => {
  const inp = document.querySelector('.dc-cart-line__qty input.qty');
  const wrap = document.querySelector('.dc-cart-line__qty .quantity');
  return {
    inputOutline: getComputedStyle(inp).outline,
    inputOutlineStyle: getComputedStyle(inp).outlineStyle,
    inputBoxShadow: getComputedStyle(inp).boxShadow,
    wrapBoxShadow: getComputedStyle(wrap).boxShadow,
  };
});
console.log(JSON.stringify(cs, null, 2));

const box = await input.boundingBox();
await p.screenshot({ path: `${OUT}/focused.png`, clip: { x: box.x - 30, y: box.y - 30, width: box.width + 60, height: box.height + 60 } });
await b.close();
