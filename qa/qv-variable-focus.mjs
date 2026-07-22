import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(300);
// Open QV for variable product 18135 via direct injection
await p.evaluate(async () => {
  const r = await fetch('/wp-admin/admin-ajax.php?action=dankcave_quickview&product_id=18135', { credentials: 'same-origin' });
  const j = await r.json();
  const qv = document.getElementById('dc-quickview');
  qv.querySelector('[data-dc-quickview-body]').innerHTML = j.data.html;
  qv.hidden = false;
  qv.setAttribute('data-open', 'true');
  document.body.classList.add('dc-drawer-open');
  if (window.jQuery) window.jQuery(qv.querySelector('form.variations_form')).wc_variation_form();
});
await p.waitForSelector('.variations select', { timeout: 5000 });
await p.waitForTimeout(500);
// Focus the select
await p.focus('.variations select');
await p.waitForTimeout(300);
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/quickview/qv-var-focus.png' });
// Read computed styles
const cs = await p.evaluate(() => {
  const sel = document.querySelector('.dc-quickview__cart .variations select');
  const qty = document.querySelector('.dc-quickview__cart .qty');
  return {
    selectOutline: getComputedStyle(sel).outline,
    selectBoxShadow: getComputedStyle(sel).boxShadow,
    selectBorder: getComputedStyle(sel).border,
    qtyOutline: getComputedStyle(qty).outline,
  };
});
console.log(JSON.stringify(cs, null, 2));
await b.close();
