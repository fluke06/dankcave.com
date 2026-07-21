// Close-up screenshots of every button style across the site.
import { chromium } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots/buttons', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const BASE = 'http://localhost:8090';

const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();

async function shot(url, sel, filename, opts = {}) {
  await p.goto(BASE + url, { waitUntil: 'load', timeout: 25000 });
  if (opts.addToCart) {
    await p.goto(BASE + '/?add-to-cart=17009', { waitUntil: 'load', timeout: 20000 });
    await p.goto(BASE + url, { waitUntil: 'load', timeout: 20000 });
  }
  await p.waitForTimeout(500);
  const el = await p.$(sel);
  if (!el) { console.log('MISS', sel, 'on', url); return; }
  const box = await el.boundingBox();
  if (!box || box.width < 4 || box.height < 4) { console.log('NO/TINY BOX', sel); return; }
  // Scroll the element into view first
  await el.scrollIntoViewIfNeeded();
  await p.waitForTimeout(200);
  const box2 = await el.boundingBox();
  if (!box2) { console.log('LOST BOX', sel); return; }
  const pad = 40;
  const clipX = Math.max(0, box2.x - pad);
  const clipY = Math.max(0, box2.y - pad);
  const clipW = Math.min(1280 - clipX, box2.width + pad * 2);
  const clipH = Math.min(900 - clipY, box2.height + pad * 2);
  if (clipW < 10 || clipH < 10) { console.log('CLIP TOO SMALL', sel, box2); return; }
  await p.screenshot({
    path: `${OUT}/${filename}.png`,
    clip: { x: clipX, y: clipY, width: clipW, height: clipH },
  });
  // Also dump computed style for the button
  const styles = await p.evaluate((s) => {
    const el = document.querySelector(s);
    if (!el) return null;
    const cs = getComputedStyle(el);
    return {
      bg: cs.backgroundColor,
      color: cs.color,
      borderRadius: cs.borderRadius,
      padding: cs.padding,
      fontSize: cs.fontSize,
      fontWeight: cs.fontWeight,
      textTransform: cs.textTransform,
    };
  }, sel);
  console.log(filename, JSON.stringify(styles));
}

await shot('/',                       '.hero__cta',                 '01-hero-split-cta');
await shot('/',                       '.product-card__add',         '02-add-card');
await shot('/',                       '.section-head__link',        '03-see-all-link');
await shot('/',                       '.editorial-band__cta',       '04-editorial-cta');
await shot('/',                       '.newsletter-band button, .newsletter-band__placeholder button', '05-newsletter-join');
await shot('/shop/',                  '.shop-filters__pill.is-active','06-filter-pill-active');
await shot('/shop/',                  '.shop-filters__pill:not(.is-active)','06b-filter-pill-inactive');
await shot('/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/', '.single_add_to_cart_button', '07-add-to-cart-pdp');
await shot('/cart/',                  '.dc-summary-card__cta',      '08-cart-checkout',    { addToCart: true });
await shot('/cart/',                  '.dc-cart__update',           '09-cart-update',      { addToCart: true });
await shot('/cart/',                  '.dc-cart__coupon-apply',     '10-cart-apply',       { addToCart: true });
await shot('/cart/',                  '.dc-cart-line__remove',      '11-cart-remove',      { addToCart: true });
await shot('/my-account/',            '.dc-login-card__submit',     '12-login-submit');
await shot('/blog/',                  '.dc-blog__chip.is-active',   '13-blog-chip-active');
await shot('/blog/',                  '.dc-blog__chip:not(.is-active)','13b-blog-chip-inactive');
await shot('/no-such-page',           '.dc-404__cta--primary',      '14-404-primary');
await shot('/no-such-page',           '.dc-404__cta:not(.dc-404__cta--primary)','14b-404-secondary');
await shot('/?s=bong',                '.dc-search__form button',    '15-search-submit');

await b.close();
