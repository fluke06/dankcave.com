import { chromium, devices } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();

await p.goto('http://localhost:8090/', { waitUntil: 'load' });
await p.waitForTimeout(400);
await p.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
await p.waitForTimeout(400);

// Screenshot the footer area
const newsletter = await p.$('.newsletter-band');
if (newsletter) {
  await newsletter.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/social-icons-desktop.png' });
}

const info = await p.evaluate(() => {
  const links = Array.from(document.querySelectorAll('.dc-social__link')).map(a => ({
    href: a.getAttribute('href'),
    aria: a.getAttribute('aria-label'),
    target: a.getAttribute('target'),
  }));
  return { count: links.length, links };
});
console.log('desktop:', JSON.stringify(info, null, 2));

// Mobile
const ctxM = await b.newContext({ ...devices['iPhone 13'], deviceScaleFactor: 2 });
const pm = await ctxM.newPage();
await pm.goto('http://localhost:8090/', { waitUntil: 'load' });
await pm.waitForTimeout(400);
await pm.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
await pm.waitForTimeout(400);
const mobileFooter = await pm.$('.newsletter-band');
if (mobileFooter) await mobileFooter.screenshot({ path: '/Users/christiandizon/Sites/dankcave/qa/screenshots/social-icons-mobile.png' });

await b.close();
