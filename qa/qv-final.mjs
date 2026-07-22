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
  const table = document.querySelector('.dc-quickview__cart .variations');
  const tr = table.querySelector('tr');
  const td = table.querySelector('td');
  const sel = table.querySelector('select');
  return {
    table_display: getComputedStyle(table).display,
    tr_display: getComputedStyle(tr).display,
    td_display: getComputedStyle(td).display,
    td_padding: getComputedStyle(td).padding,
    td_height: Math.round(td.getBoundingClientRect().height),
    sel_height: Math.round(sel.getBoundingClientRect().height),
    tr_height: Math.round(tr.getBoundingClientRect().height),
    table_height: Math.round(table.getBoundingClientRect().height),
  };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
