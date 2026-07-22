import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(300);
// Call the JS function directly with a known variable product ID (18135)
await p.evaluate(async () => {
  const url = '/wp-admin/admin-ajax.php?action=dankcave_quickview&product_id=18135';
  const r = await fetch(url, { credentials: 'same-origin' });
  const json = await r.json();
  const qv = document.getElementById('dc-quickview');
  const body = qv.querySelector('[data-dc-quickview-body]');
  body.innerHTML = json.data.html;
  qv.hidden = false;
  qv.setAttribute('data-open', 'true');
  document.body.classList.add('dc-drawer-open');
  if (window.jQuery) {
    const form = body.querySelector('form.variations_form');
    if (form) window.jQuery(form).wc_variation_form();
  }
});
await p.waitForTimeout(1500);
const info = await p.evaluate(() => {
  const qv = document.getElementById('dc-quickview');
  return {
    title: qv.querySelector('.dc-quickview__title')?.textContent,
    hasForm: !!qv.querySelector('form.variations_form'),
    selects: Array.from(qv.querySelectorAll('.variations select')).map(s => ({ name: s.name, options: s.options.length })),
  };
});
console.log(JSON.stringify(info, null, 2));
await p.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/quickview/quickview-variable.png' });
await b.close();
