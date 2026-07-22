import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/');
await p.evaluate(async () => {
  const r = await fetch('/wp-admin/admin-ajax.php?action=dankcave_quickview&product_id=18135');
  const j = await r.json();
  const qv = document.getElementById('dc-quickview');
  qv.querySelector('[data-dc-quickview-body]').innerHTML = j.data.html;
  qv.hidden = false;
  qv.setAttribute('data-open', 'true');
  if (window.jQuery) window.jQuery(qv.querySelector('form.variations_form')).wc_variation_form();
});
await p.waitForSelector('.variations select', { timeout: 5000 });
await p.waitForTimeout(500);
const info = await p.evaluate(() => {
  const summary = document.querySelector('.dc-quickview__summary');
  const select = document.querySelector('.dc-quickview__cart .variations select');
  const qty = document.querySelector('.woocommerce-variation-add-to-cart .quantity');
  const btn = document.querySelector('.single_add_to_cart_button');
  const row = document.querySelector('.woocommerce-variation-add-to-cart');
  return {
    summary: { left: Math.round(summary.getBoundingClientRect().left), right: Math.round(summary.getBoundingClientRect().right), width: Math.round(summary.getBoundingClientRect().width) },
    select: { left: Math.round(select.getBoundingClientRect().left), right: Math.round(select.getBoundingClientRect().right), width: Math.round(select.getBoundingClientRect().width) },
    row:    { left: Math.round(row.getBoundingClientRect().left), right: Math.round(row.getBoundingClientRect().right), width: Math.round(row.getBoundingClientRect().width), justify: getComputedStyle(row).justifyContent },
    qty:    { left: Math.round(qty.getBoundingClientRect().left), right: Math.round(qty.getBoundingClientRect().right), width: Math.round(qty.getBoundingClientRect().width) },
    btn:    { left: Math.round(btn.getBoundingClientRect().left), right: Math.round(btn.getBoundingClientRect().right), width: Math.round(btn.getBoundingClientRect().width), flex: getComputedStyle(btn).flex },
  };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
