import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/', { waitUntil: 'load' });
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
  const select = document.querySelector('.dc-quickview__cart .variations select');
  const row = document.querySelector('.dc-quickview__cart .woocommerce-variation-add-to-cart');
  const selRect = select.getBoundingClientRect();
  const rowRect = row.getBoundingClientRect();
  const gap = rowRect.top - selRect.bottom;

  // Walk between the two and log each element with its height
  const elements = [];
  let el = select.closest('.variations');
  while (el && el !== row) {
    const r = el.getBoundingClientRect();
    const cs = getComputedStyle(el);
    elements.push({
      tag: el.tagName + '.' + String(el.className || '').replace(/\s+/g, '.').slice(0, 60),
      h: Math.round(r.height),
      mt: cs.marginTop,
      mb: cs.marginBottom,
      pt: cs.paddingTop,
      pb: cs.paddingBottom,
      display: cs.display,
    });
    // Walk forward: try nextSibling, else parent's nextSibling
    let next = el.nextElementSibling;
    while (!next && el.parentElement) {
      el = el.parentElement;
      next = el.nextElementSibling;
    }
    el = next;
  }
  return { gap: Math.round(gap), elements };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
