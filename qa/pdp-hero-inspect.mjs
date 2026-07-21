import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
// Use an out-of-stock product to see what user is seeing
await p.goto('http://localhost:8090/shop/rollings/pre-rolled-cones/', { waitUntil: 'load' });
const links = await p.$$('.product-card__link');
if (links.length) {
  const href = await links[0].getAttribute('href');
  console.log('sample PDP:', href);
  await p.goto(href, { waitUntil: 'load' });
  await p.waitForTimeout(600);
  const info = await p.evaluate(() => {
    const hero = document.querySelector('.pdp-gallery__hero');
    const img = document.querySelector('.pdp-gallery__image');
    return {
      heroBox: hero ? hero.getBoundingClientRect() : null,
      heroBg: hero ? getComputedStyle(hero).background : null,
      imgSrc: img ? img.src : null,
      imgBox: img ? img.getBoundingClientRect() : null,
      imgNaturalW: img ? img.naturalWidth : null,
      imgNaturalH: img ? img.naturalHeight : null,
      imgObjectFit: img ? getComputedStyle(img).objectFit : null,
      imgMaxHeight: img ? getComputedStyle(img).maxHeight : null,
    };
  });
  console.log(JSON.stringify(info, null, 2));
}
await b.close();
