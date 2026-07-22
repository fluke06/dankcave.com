import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p.waitForTimeout(500);
// Directly open the variable-product quickview via JS to bypass click intercepting
await p.evaluate(() => {
  // Trigger openQuickView for product 18135 (variable — High Hemp Organic Wraps CBD+)
  document.querySelector('[data-dc-quickview][data-product-id]')?.click();
});
await p.waitForTimeout(500);
// Now inject the correct variable product ID via click on the OPTIONS button, but click via JS
await p.evaluate(() => {
  const optBtns = document.querySelectorAll('.product-card__add--needs-options');
  if (optBtns.length) optBtns[0].dispatchEvent(new MouseEvent('click', { bubbles: true }));
});
await p.waitForSelector('.dc-quickview__title', { timeout: 8000 });
await p.waitForTimeout(1200);
const info = await p.evaluate(() => {
  const qv = document.getElementById('dc-quickview');
  return {
    title: qv.querySelector('.dc-quickview__title')?.textContent?.trim(),
    hasVariationsForm: !!qv.querySelector('form.variations_form'),
    selectCount: qv.querySelectorAll('.variations select').length,
    hasAddBtn: !!qv.querySelector('.single_add_to_cart_button'),
  };
});
console.log('variable QV:', JSON.stringify(info));
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/quickview/quickview-variable.png' });
await b.close();
