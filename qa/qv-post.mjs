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
  const rows = [];
  document.querySelectorAll('.dc-quickview__cart form > *').forEach(el => {
    const rect = el.getBoundingClientRect();
    rows.push({ tag: el.tagName + '.' + String(el.className||'').split(' ')[0], top: Math.round(rect.top), bottom: Math.round(rect.bottom), height: Math.round(rect.height), mt: getComputedStyle(el).marginTop, mb: getComputedStyle(el).marginBottom, display: getComputedStyle(el).display });
  });
  return rows;
});
console.log(JSON.stringify(info, null, 2));
await b.close();
