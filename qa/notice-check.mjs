import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.goto('http://localhost:8090/cart/', { waitUntil: 'load' });
await p.waitForTimeout(500);
const info = await p.evaluate(() => {
  const wrap = document.querySelector('.site-content > .woocommerce > .woocommerce-notices-wrapper');
  const msg  = document.querySelector('.woocommerce-message');
  const wc   = document.querySelector('.site-content > .woocommerce');
  return {
    wrap_rect: wrap ? wrap.getBoundingClientRect() : null,
    wrap_ml: wrap ? getComputedStyle(wrap).marginLeft : null,
    wrap_mr: wrap ? getComputedStyle(wrap).marginRight : null,
    msg_rect: msg ? msg.getBoundingClientRect() : null,
    msg_ml: msg ? getComputedStyle(msg).marginLeft : null,
    wc_rect: wc ? wc.getBoundingClientRect() : null,
    wc_display: wc ? getComputedStyle(wc).display : null,
  };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
