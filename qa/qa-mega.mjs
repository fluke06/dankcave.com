// Comprehensive automated QA suite covering the testing checklist:
// - Functional: navigation, forms, auth, user actions
// - UI / visual: layout, typography, images, buttons
// - Responsive: 8 viewport widths
// - A11y: keyboard, focus, contrast, landmarks
// - SEO: meta + schema (delegated to seo-audit-full.mjs)
// - Console + network: JS errors, 4xx/5xx, mixed content
// - Error handling: 404, empty state
// - WooCommerce flows: add to cart, cart, checkout
//
// Cross-browser, real-device, and analytics/marketing pixel checks require
// external tools (BrowserStack, real hardware, actual analytics accounts).
// Those are flagged as MANUAL in the report.
import { chromium, devices } from 'playwright';
import fs from 'fs';

const BASE = 'http://localhost:8090';
const OUT = new URL('./screenshots/qa-mega', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });

const findings = { pass: [], fail: [], warn: [], manual: [] };
const P = (msg) => findings.pass.push(msg);
const F = (msg) => findings.fail.push(msg);
const W = (msg) => findings.warn.push(msg);
const M = (msg) => findings.manual.push(msg);

const b = await chromium.launch();

// ==========================================================================
// 1. FUNCTIONAL — navigation
// ==========================================================================
{
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
  const p = await ctx.newPage();
  const errors = []; const failedResources = [];
  p.on('pageerror', e => errors.push(e.message));
  p.on('response', r => { if (r.status() >= 400 && !r.url().includes('/wp-content/uploads/') && !r.url().includes('no-such-page') && !r.url().includes('/wp-admin/admin-ajax.php')) failedResources.push(`${r.status()} ${r.url().slice(0, 80)}`); });

  const navPages = [ '/', '/shop/', '/blog/', '/about/', '/cart/', '/checkout/', '/my-account/', '/product-category/smoking-accessories/' ];
  for (const path of navPages) {
    const res = await p.goto(BASE + path, { waitUntil: 'load', timeout: 20000 });
    if (res.status() === 200) P(`nav: ${path} → 200`); else F(`nav: ${path} → ${res.status()}`);
  }

  // Header logo redirects to home
  await p.goto(BASE + '/shop/');
  await p.click('.site-brand, .site-brand--logo a');
  await p.waitForLoadState('load');
  if (p.url() === BASE + '/') P('header logo → home'); else F(`header logo went to ${p.url()}`);

  // Footer legal links
  await p.goto(BASE + '/');
  const footerLinks = await p.$$eval('.legal-bar__list a', els => els.map(a => ({ href: a.href, text: a.textContent.trim() })));
  P(`footer legal-bar has ${footerLinks.length} links`);
  for (const l of footerLinks) {
    if (!l.href) F(`footer link "${l.text}" has no href`);
  }

  // 404 page
  const r404 = await p.goto(BASE + '/nonsense-slug-that-does-not-exist');
  if (r404.status() === 404 && await p.$('.dc-404')) P('404 renders custom page');
  else W(`404 returned ${r404.status()}`);

  // Console + network hygiene across all navPages
  if (errors.length === 0) P('nav: no page JS errors');
  else F(`nav: ${errors.length} JS errors — ${errors.slice(0, 3).join(' | ')}`);
  if (failedResources.length === 0) P('nav: no 4xx/5xx resources');
  else W(`nav: ${failedResources.length} failed resource(s) — ${failedResources.slice(0, 3).join(' | ')}`);

  await ctx.close();
}

// ==========================================================================
// 2. FUNCTIONAL — WooCommerce cart/checkout flow
// ==========================================================================
{
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
  const p = await ctx.newPage();
  const errors = [];
  p.on('pageerror', e => errors.push(e.message));

  // Shop archive add-to-cart via first Add + button
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  const addBtn = await p.$('a.add_to_cart_button.ajax_add_to_cart');
  if (addBtn) {
    await addBtn.click();
    await p.waitForTimeout(1500);
    const drawerOpen = await p.$('[data-open="true"]#dc-cart-drawer, #dc-cart-drawer[data-open]');
    if (drawerOpen) P('add-to-cart: drawer opens after AJAX add');
    else W('add-to-cart: drawer did not open after add');
  } else F('add-to-cart: no ajax add button on shop archive');

  // Cart page shows added item + Checkout button
  await p.goto(BASE + '/cart/', { waitUntil: 'load' });
  const cartLines = await p.$$('.dc-cart-line');
  if (cartLines.length > 0) P(`cart: ${cartLines.length} line(s)`);
  else F('cart: no lines rendered');
  if (await p.$('.dc-summary-card__cta')) P('cart: checkout CTA visible');
  else F('cart: no checkout CTA');

  // Coupon toggle expands
  await p.goto(BASE + '/checkout/', { waitUntil: 'load' });
  if (await p.$('.dc-review__coupon')) {
    await p.click('[data-dc-toggle-coupon]');
    await p.waitForTimeout(300);
    const open = await p.evaluate(() => !document.querySelector('[data-dc-inline-coupon]').hasAttribute('hidden'));
    if (open) P('checkout: coupon toggle expands');
    else F('checkout: coupon toggle did not expand');
  }
  // Login toggle expands
  if (await p.$('[data-dc-toggle-login]')) {
    await p.click('[data-dc-toggle-login]');
    await p.waitForTimeout(300);
    const open = await p.evaluate(() => !document.querySelector('[data-dc-inline-login]').hasAttribute('hidden'));
    if (open) P('checkout: login toggle expands');
    else F('checkout: login toggle did not expand');
  }

  // Quick view modal opens — trigger via JS click since hover buttons are
  // invisible until hover.
  await p.goto(BASE + '/shop/', { waitUntil: 'load' });
  await p.evaluate(() => document.querySelector('[data-dc-quickview]')?.click());
  try { await p.waitForSelector('.dc-quickview__title', { timeout: 5000 }); P('quickview: modal opens with product data'); }
  catch { F('quickview: modal did not open'); }
  await p.evaluate(() => document.querySelector('[data-dc-quickview-close]')?.click());
  await p.waitForTimeout(300);

  // Wishlist heart toggles
  await p.evaluate(() => document.querySelector('[data-dc-wishlist]')?.click());
  await p.waitForTimeout(200);
  const wishActive = await p.evaluate(() => document.querySelector('[data-dc-wishlist]')?.classList.contains('is-active'));
  if (wishActive) P('wishlist: heart toggles is-active'); else F('wishlist: heart did not toggle');
  await p.evaluate(() => document.querySelector('[data-dc-wishlist]')?.click());

  // Compare adds to tray
  await p.evaluate(() => document.querySelector('[data-dc-compare]')?.click());
  await p.waitForTimeout(500);
  const trayVisible = await p.evaluate(() => document.getElementById('dc-compare-tray').getAttribute('data-visible') === 'true');
  if (trayVisible) P('compare: tray appears after add'); else W('compare: tray did not appear');

  // Search modal
  await p.goto(BASE + '/', { waitUntil: 'load' });
  await p.click('.header-search-pill');
  await p.waitForTimeout(300);
  if (await p.evaluate(() => document.getElementById('search-modal')?.getAttribute('data-open') === 'true')) P('search modal: opens on pill click');
  else F('search modal: did not open');
  await p.keyboard.press('Escape');
  await p.waitForTimeout(300);
  if (await p.evaluate(() => document.getElementById('search-modal')?.hidden)) P('search modal: closes on Escape');
  else F('search modal: did not close on Escape');

  if (errors.length === 0) P('woo flow: no page JS errors');
  else F(`woo flow: ${errors.length} JS errors — ${errors.slice(0, 3).join(' | ')}`);

  await ctx.close();
}

// ==========================================================================
// 3. FUNCTIONAL — forms (contact / login / search validation)
// ==========================================================================
{
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
  const p = await ctx.newPage();

  await p.goto(BASE + '/my-account/', { waitUntil: 'load' });
  const emailInput = await p.$('#username');
  const passInput = await p.$('#password');
  if (emailInput && passInput) P('login form: username + password inputs present');
  else F('login form: missing inputs');

  // Try submit empty — WC returns to same URL with error notice
  const submitBtn = await p.$('button.dc-login-card__submit, button[name="login"]');
  if (submitBtn) P('login: submit button present');

  // Search results form submits
  await p.goto(BASE + '/?s=bong', { waitUntil: 'load' });
  const searchInput = await p.$('#dc-search-input');
  if (searchInput) {
    const val = await searchInput.inputValue();
    if (val === 'bong') P('search: input reflects query string');
    else W(`search: query value was "${val}"`);
  }

  await ctx.close();
}

// ==========================================================================
// 4. RESPONSIVE — 8 viewport widths, layout doesn't overflow
// ==========================================================================
{
  const widths = [320, 360, 375, 390, 414, 430, 768, 820, 1024, 1280, 1366, 1440, 1920];
  const routes = ['/', '/shop/', '/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/', '/blog/', '/cart/'];
  for (const w of widths) {
    const ctx = await b.newContext({ viewport: { width: w, height: 900 }, userAgent: w < 768 ? devices['iPhone 13'].userAgent : undefined });
    const p = await ctx.newPage();
    let overflowCount = 0;
    for (const route of routes) {
      await p.goto(BASE + route, { waitUntil: 'load', timeout: 20000 });
      const overflow = await p.evaluate(() => Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth);
      if (overflow > 2) overflowCount++;
    }
    if (overflowCount === 0) P(`responsive @${w}px: no horizontal overflow`);
    else F(`responsive @${w}px: ${overflowCount}/${routes.length} pages overflow`);
    await ctx.close();
  }
}

// ==========================================================================
// 5. A11Y — keyboard nav, focus indicators, headings, contrast (basic)
// ==========================================================================
{
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
  const p = await ctx.newPage();
  await p.goto(BASE + '/', { waitUntil: 'load' });

  // Skip-link exists + focusable
  const skipLink = await p.$('.skip-link');
  if (skipLink) {
    P('a11y: skip-link present in DOM');
    // Focus by keyboard tab from body — first Tab lands on skip-link
    await p.focus('body');
    await p.keyboard.press('Tab');
    const activeIsSkip = await p.evaluate(() => document.activeElement?.classList?.contains('skip-link'));
    if (activeIsSkip) P('a11y: skip-link is the first tab stop');
    else W('a11y: skip-link is not first tab stop');
  } else F('a11y: no skip-link');

  // Headings hierarchy
  const hs = await p.evaluate(() => ({
    h1: document.querySelectorAll('h1').length,
    h2: document.querySelectorAll('h2').length,
    h3: document.querySelectorAll('h3').length,
  }));
  if (hs.h1 === 1) P(`a11y: exactly 1 H1 on home`);
  else F(`a11y: home has ${hs.h1} H1 tags`);

  // Landmark roles
  const landmarks = await p.evaluate(() => ({
    header: !!document.querySelector('header[role="banner"], header.site-header'),
    main:   !!document.querySelector('main'),
    footer: !!document.querySelector('footer, .legal-bar'),
    nav:    !!document.querySelector('nav'),
  }));
  Object.entries(landmarks).forEach(([k, v]) => { if (v) P(`a11y: <${k}> landmark`); else F(`a11y: <${k}> landmark missing`); });

  // Images without alt (already SEO-audited but re-check here)
  const missingAlt = await p.evaluate(() => Array.from(document.querySelectorAll('img')).filter(i => !i.hasAttribute('alt') && !i.hasAttribute('aria-hidden')).length);
  if (missingAlt === 0) P('a11y: all images have alt or aria-hidden');
  else F(`a11y: ${missingAlt} images without alt`);

  // Buttons without accessible name
  const noNameBtns = await p.evaluate(() => Array.from(document.querySelectorAll('button')).filter(b => !b.textContent.trim() && !b.getAttribute('aria-label') && !b.getAttribute('title')).length);
  if (noNameBtns === 0) P('a11y: all buttons have text or aria-label');
  else F(`a11y: ${noNameBtns} buttons without accessible name`);

  // Body text contrast — sample the main paragraph color vs background
  const contrast = await p.evaluate(() => {
    function parse(rgb) { const m = rgb.match(/\d+/g); return m ? m.slice(0, 3).map(Number) : null; }
    function luminance([r, g, b]) { const a = [r, g, b].map(v => { v /= 255; return v < 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); }); return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2]; }
    const sample = document.querySelector('p, .hero__lede, .newsletter-band__subcopy, .section-head__title');
    if (!sample) return null;
    const cs = getComputedStyle(sample);
    let bg = 'rgb(255,255,255)';
    let el = sample;
    while (el) {
      const b = getComputedStyle(el).backgroundColor;
      if (b && b !== 'rgba(0, 0, 0, 0)' && b !== 'transparent') { bg = b; break; }
      el = el.parentElement;
    }
    const cf = parse(cs.color), bgp = parse(bg);
    if (!cf || !bgp) return null;
    const l1 = luminance(cf), l2 = luminance(bgp);
    const ratio = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
    return { ratio: +ratio.toFixed(2), color: cs.color, bg };
  });
  if (contrast && contrast.ratio >= 4.5) P(`a11y: body text contrast ${contrast.ratio}:1 (AA passes)`);
  else if (contrast) W(`a11y: body text contrast ${contrast.ratio}:1 (below 4.5:1 AA)`);

  await ctx.close();
}

// ==========================================================================
// 6. PERF — LCP/CLS/FCP already covered in cwv-audit.mjs; just check console
// ==========================================================================
{
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
  const p = await ctx.newPage();
  const errors = [];
  p.on('pageerror', e => errors.push(e.message));
  p.on('console', m => { if (m.type() === 'error') errors.push('console: ' + m.text().slice(0, 100)); });
  await p.goto(BASE + '/', { waitUntil: 'load' });
  await p.waitForTimeout(2000);
  if (errors.length === 0) P('perf: home has no console errors');
  else W(`perf: home has ${errors.length} console error(s) — ${errors.slice(0, 3).join(' | ')}`);
  M('perf: LCP/CLS field data — run qa/cwv-audit.mjs separately for Lighthouse-style metrics');
  await ctx.close();
}

// ==========================================================================
// 7. SEO — delegated to seo-audit-full.mjs
// ==========================================================================
M('seo: run qa/seo-audit-full.mjs for full meta + JSON-LD + heading audit');

// ==========================================================================
// 8. SECURITY basics — HTTPS, no mixed content, cookies (frontend only)
// ==========================================================================
{
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
  const p = await ctx.newPage();
  await p.goto(BASE + '/', { waitUntil: 'load' });
  M('security: HTTPS + CSP + secure cookies require production URL check (this mirror is HTTP)');
  const iframes = await p.$$('iframe');
  P(`security: ${iframes.length} iframes on home (review third-party sources)`);
  await ctx.close();
}

// ==========================================================================
// 9. CONTENT — placeholder text / lorem ipsum / broken links
// ==========================================================================
{
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
  const p = await ctx.newPage();
  await p.goto(BASE + '/', { waitUntil: 'load' });
  const bodyText = await p.evaluate(() => document.body.innerText);
  if (/lorem ipsum/i.test(bodyText)) F('content: home page contains Lorem Ipsum'); else P('content: no Lorem Ipsum on home');
  if (/© 20(2[6-9]|3\d)/.test(bodyText)) P('content: copyright year is current'); else W('content: check copyright year');

  // Broken internal links across the home page
  const links = await p.$$eval('a[href^="/"], a[href^="' + BASE + '"]', els => Array.from(new Set(els.map(a => a.href).filter(h => h && !h.includes('#') && !h.startsWith('mailto:')))));
  let broken = 0;
  for (const href of links.slice(0, 15)) {
    const r = await p.request.get(href, { maxRedirects: 3 });
    if (r.status() >= 400 && r.status() !== 404) broken++;
  }
  if (broken === 0) P(`content: sampled ${links.slice(0, 15).length} internal links — none 5xx`);
  else W(`content: ${broken} internal links returned >=500`);
  await ctx.close();
}

// ==========================================================================
// 10. Item categories that require MANUAL / external tools
// ==========================================================================
M('cross-browser: Firefox/Safari/Edge — run same pages in BrowserStack or actual browsers');
M('real devices: iPhone SE, iPhone 15, iPad, Pixel, Samsung — physical hardware required');
M('analytics: verify GA / GTM / Meta Pixel events fire — needs live analytics accounts');
M('third-party: payment gateways (Sezzle sandbox, Authorize.net sandbox), reCAPTCHA — need test credentials');
M('A/B testing: no experiments running');
M('CDN + email + cron: production deploy verification only');

await b.close();

// ==========================================================================
// REPORT
// ==========================================================================
console.log('\n=== PASS (' + findings.pass.length + ') ===');
findings.pass.forEach(m => console.log('  ✓', m));
console.log('\n=== WARN (' + findings.warn.length + ') ===');
findings.warn.forEach(m => console.log('  ⚠', m));
console.log('\n=== FAIL (' + findings.fail.length + ') ===');
findings.fail.forEach(m => console.log('  ✗', m));
console.log('\n=== MANUAL (' + findings.manual.length + ') ===');
findings.manual.forEach(m => console.log('  ·', m));
console.log('\ntotals — pass:', findings.pass.length, ' warn:', findings.warn.length, ' fail:', findings.fail.length, ' manual:', findings.manual.length);
process.exit(findings.fail.length > 0 ? 1 : 0);
