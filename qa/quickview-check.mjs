import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/quickview', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p.waitForTimeout(500);

// Hover the first card to reveal hover actions
const card = await p.$('.product-card');
await card.hover();
await p.waitForTimeout(400);
await p.screenshot({ path: `${OUT}/card-hover.png`, clip: { x: 0, y: 0, width: 640, height: 700 } });

// Click quickview eye icon on the first card
const qvBtn = await p.$('[data-dc-quickview]');
if (qvBtn) {
  await qvBtn.click();
  await p.waitForSelector('.dc-quickview__title', { timeout: 5000 });
  await p.waitForTimeout(500);
  await p.screenshot({ path: `${OUT}/quickview-first.png` });
}
let info = await p.evaluate(() => {
  const qv = document.getElementById('dc-quickview');
  const form = qv.querySelector('form.cart, form.variations_form');
  return {
    hasForm: !!form,
    isVariable: !!qv.querySelector('form.variations_form'),
    hasSelect: qv.querySelectorAll('select').length,
    title: qv.querySelector('.dc-quickview__title')?.textContent?.trim(),
    price: qv.querySelector('.dc-quickview__price')?.textContent?.trim(),
  };
});
console.log('first quickview:', JSON.stringify(info));

// Close then find a VARIABLE product (has OPTIONS -> button) and open its quickview
await p.evaluate(() => document.querySelector('[data-dc-quickview-close]').click());
await p.waitForTimeout(400);
const optBtn = await p.$('.product-card__add--needs-options');
if (optBtn) {
  await optBtn.scrollIntoViewIfNeeded();
  await optBtn.click();
  await p.waitForSelector('.dc-quickview__title', { timeout: 5000 });
  await p.waitForTimeout(700);
  await p.screenshot({ path: `${OUT}/quickview-variable.png` });
}
info = await p.evaluate(() => {
  const qv = document.getElementById('dc-quickview');
  return {
    hasForm: !!qv.querySelector('form.cart, form.variations_form'),
    isVariable: !!qv.querySelector('form.variations_form'),
    hasSelect: qv.querySelectorAll('select').length,
    title: qv.querySelector('.dc-quickview__title')?.textContent?.trim(),
    selectPresent: !!qv.querySelector('.variations select'),
  };
});
console.log('variable quickview:', JSON.stringify(info));

// Wishlist toggle
await p.evaluate(() => document.getElementById('dc-quickview').querySelector('[data-dc-quickview-close]')?.click());
await p.waitForTimeout(400);
const wishBtn = await p.$('[data-dc-wishlist]');
await wishBtn.click();
const wishState = await p.evaluate(() => document.querySelector('[data-dc-wishlist]').classList.contains('is-active'));
console.log('wishlist active after click:', wishState);

console.log('errors:', errors);
await b.close();
