import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/cart-drawer', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

// Land on shop archive
await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p.waitForTimeout(600);

// Find a simple product's Add button and click it — WC should fire ajax add-to-cart
const addBtn = await p.$('a.add_to_cart_button.ajax_add_to_cart');
if (addBtn) {
  const href = await addBtn.getAttribute('href');
  console.log('ajax add button href:', href);
  await addBtn.click();
  await p.waitForTimeout(1500); // ajax + drawer open
} else {
  console.log('no ajax add button found; hitting cart pill instead');
  await p.click('.cart-summary');
  await p.waitForTimeout(500);
}

const info = await p.evaluate(() => {
  const drawer = document.getElementById('dc-cart-drawer');
  const items = document.querySelectorAll('.dc-cart-drawer-item');
  return {
    drawerHidden: drawer.hidden,
    drawerOpen: drawer.getAttribute('data-open'),
    itemCount: items.length,
    firstTitle: items[0] ? items[0].querySelector('.dc-cart-drawer-item__name')?.textContent?.trim() : null,
    subtotal: document.querySelector('.dc-cart-drawer__subtotal-val')?.textContent?.trim(),
  };
});
console.log('drawer state:', JSON.stringify(info));

await p.screenshot({ path: `${OUT}/drawer-open.png` });

// Close via close button
await p.click('.dc-cart-drawer__close');
await p.waitForTimeout(500);
const after = await p.evaluate(() => document.getElementById('dc-cart-drawer').hidden);
console.log('after close, hidden:', after);

console.log('errors:', errors);
await b.close();
