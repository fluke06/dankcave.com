import { chromium } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/checkout-inline', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.goto('http://localhost:8090/checkout/', { waitUntil: 'load', timeout: 30000 });
await p.waitForTimeout(700);

// Are the OLD toast wrappers hidden?
const info = await p.evaluate(() => ({
  loginToggleHidden: document.querySelector('.site-content > .woocommerce > .woocommerce-form-login-toggle') ? getComputedStyle(document.querySelector('.site-content > .woocommerce > .woocommerce-form-login-toggle')).display : 'not found',
  couponToggleHidden: document.querySelector('.site-content > .woocommerce > .woocommerce-form-coupon-toggle') ? getComputedStyle(document.querySelector('.site-content > .woocommerce > .woocommerce-form-coupon-toggle')).display : 'not found',
  inlineLoginPresent: !!document.querySelector('.dc-checkout-inline-actions'),
  inlineCouponPresent: !!document.querySelector('.dc-review__coupon'),
}));
console.log('DOM state:', JSON.stringify(info, null, 2));

// Screenshot page top
await p.screenshot({ path: `${OUT}/top.png`, clip: { x: 0, y: 0, width: 1280, height: 900 } });

// Click "Log in" and check that inline login opens
if (info.inlineLoginPresent) {
  await p.click('[data-dc-toggle-login]');
  await p.waitForTimeout(300);
  const open = await p.evaluate(() => !document.querySelector('[data-dc-inline-login]').hasAttribute('hidden'));
  console.log('login open after click:', open);
}

// Click "Enter code" and check coupon inline opens
await p.evaluate(() => document.querySelector('[data-dc-toggle-coupon]')?.scrollIntoView({ block: 'center' }));
await p.waitForTimeout(200);
if (await p.$('[data-dc-toggle-coupon]')) {
  await p.click('[data-dc-toggle-coupon]');
  await p.waitForTimeout(300);
  const open = await p.evaluate(() => !document.querySelector('[data-dc-inline-coupon]').hasAttribute('hidden'));
  console.log('coupon open after click:', open);
}

await p.screenshot({ path: `${OUT}/toggled.png`, clip: { x: 0, y: 0, width: 1280, height: 900 } });

console.log('errors:', errors);
await b.close();
