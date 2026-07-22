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
const t = await p.evaluate(() => {
  const sel = document.querySelector('.dc-quickview__cart .variations select');
  const row = document.querySelector('.woocommerce-variation-add-to-cart');
  const table = document.querySelector('.dc-quickview__cart .variations');
  const wrap = document.querySelector('.single_variation_wrap');
  return {
    selBottom: Math.round(sel.getBoundingClientRect().bottom),
    tableBottom: Math.round(table.getBoundingClientRect().bottom),
    wrapTop: Math.round(wrap.getBoundingClientRect().top),
    rowTop: Math.round(row.getBoundingClientRect().top),
    tableMB: getComputedStyle(table).marginBottom,
    wrapMT: getComputedStyle(wrap).marginTop,
    wrapPT: getComputedStyle(wrap).paddingTop,
  };
});
console.log(JSON.stringify(t, null, 2));
await b.close();
