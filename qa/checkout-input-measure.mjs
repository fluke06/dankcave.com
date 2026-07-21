import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.goto('http://localhost:8090/checkout/', { waitUntil: 'load', timeout: 30000 });
await p.waitForTimeout(600);
const info = await p.evaluate(() => {
  const wrapper = document.querySelector('.woocommerce-billing-fields__field-wrapper');
  const firstP = document.querySelector('#billing_first_name_field');
  const firstSpan = document.querySelector('#billing_first_name_field .woocommerce-input-wrapper');
  const firstInput = document.querySelector('#billing_first_name');
  const w = el => el ? Math.round(el.getBoundingClientRect().width) : null;
  const cs = el => el ? { display: getComputedStyle(el).display, width: getComputedStyle(el).width } : null;
  return {
    wrapperWidth: w(wrapper),
    wrapperDisplay: cs(wrapper)?.display,
    pWidth: w(firstP),
    pDisplay: cs(firstP)?.display,
    spanWidth: w(firstSpan),
    spanDisplay: cs(firstSpan)?.display,
    inputWidth: w(firstInput),
    inputDisplay: cs(firstInput)?.display,
    inputWidthStyle: cs(firstInput)?.width,
  };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
