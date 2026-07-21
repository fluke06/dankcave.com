import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
await p.goto('http://localhost:8090/checkout/', { waitUntil: 'load', timeout: 30000 });
await p.waitForTimeout(600);

// Inspect the computed & applied CSS on the billing_email input
const info = await p.evaluate(() => {
  const el = document.querySelector('#billing_email');
  const cs = getComputedStyle(el);
  return {
    ok: !!el,
    box: el ? el.getBoundingClientRect() : null,
    background: cs.backgroundColor,
    color: cs.color,
    border: cs.border,
    borderRadius: cs.borderRadius,
    padding: cs.padding,
    fontSize: cs.fontSize,
    boxShadow: cs.boxShadow,
    outlineWidth: cs.outlineWidth,
    parentDisplay: el ? getComputedStyle(el.parentElement).display : null,
    grandDisplay: el ? getComputedStyle(el.parentElement.parentElement).display : null,
    placeholder: el.placeholder,
    // Read the CSSRules that match this element by walking each stylesheet
    matchedRules: (function() {
      const matched = [];
      for (const sheet of document.styleSheets) {
        try {
          const rules = sheet.cssRules;
          for (const rule of rules) {
            if (rule.selectorText && el.matches(rule.selectorText)) {
              matched.push({ sel: rule.selectorText, href: sheet.href ? sheet.href.split('/').slice(-2).join('/') : 'inline' });
            }
          }
        } catch (_) {} // cross-origin stylesheet
      }
      return matched;
    })(),
  };
});
console.log(JSON.stringify(info, null, 2));
await b.close();
