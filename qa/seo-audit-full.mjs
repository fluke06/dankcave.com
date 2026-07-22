// Comprehensive SEO + a11y + core-web-vitals audit. Runs against every major
// template, collects violations, and prints a grouped report.
import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();

const templates = [
  ['home',     '/'],
  ['shop',     '/shop/'],
  ['pdp',      '/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/'],
  ['pdp-var',  '/shop/rollings/rolling-papers/zig-zag-pre-rolled-cones-1-1-4/'],
  ['blog',     '/blog/'],
  ['post',     '/?p=17726'],
  ['category', '/product-category/smoking-accessories/'],
  ['page',     '/privacy-policy-2/'],
  ['cart',     '/cart/'],
  ['checkout', '/checkout/'],
  ['404',      '/no-such-page'],
];

const findings = { critical: [], warning: [], info: [] };
function record(sev, msg) { findings[sev].push(msg); }

for (const [label, path] of templates) {
  const url = 'http://localhost:8090' + path;
  const res = await p.goto(url, { waitUntil: 'load', timeout: 20000 });
  await p.waitForTimeout(200);
  const status = res.status();

  const audit = await p.evaluate(() => {
    // Collect everything we care about
    const title = document.title;
    const desc = document.querySelector('meta[name="description"]')?.content;
    const canonical = document.querySelector('link[rel="canonical"]')?.href;
    const robots = document.querySelector('meta[name="robots"]')?.content;
    const ogTitle = document.querySelector('meta[property="og:title"]')?.content;
    const ogImage = document.querySelector('meta[property="og:image"]')?.content;
    const twCard  = document.querySelector('meta[name="twitter:card"]')?.content;
    const viewport = document.querySelector('meta[name="viewport"]')?.content;
    const lang = document.documentElement.lang;
    const hreflang = document.querySelectorAll('link[rel="alternate"][hreflang]').length;

    // Headings
    const h1s = Array.from(document.querySelectorAll('h1')).map(h => h.textContent.trim().slice(0, 40));
    const headings = ['h2', 'h3', 'h4', 'h5', 'h6'].map(t => document.querySelectorAll(t).length);

    // Images without alt
    const imgs = Array.from(document.querySelectorAll('img'));
    const noAlt = imgs.filter(i => !i.hasAttribute('alt') && !i.hasAttribute('aria-hidden')).length;
    const emptyAltDecorative = imgs.filter(i => i.alt === '').length;
    const missingDims = imgs.filter(i => !i.hasAttribute('width') && !i.hasAttribute('height') && !i.style.width && !i.style.height).length;

    // Semantic landmarks
    const hasMain  = !!document.querySelector('main');
    const hasHeader = !!document.querySelector('header[role="banner"], header.site-header');
    const hasFooter = !!document.querySelector('footer, .legal-bar');
    const hasNav = !!document.querySelector('nav');
    const skipLink = !!document.querySelector('a[href^="#"][class*="skip"], .skip-link');

    // Links + accessibility
    const linksNoText = Array.from(document.querySelectorAll('a')).filter(a => !a.textContent.trim() && !a.getAttribute('aria-label') && !a.querySelector('img[alt]')).length;
    const buttonsNoLabel = Array.from(document.querySelectorAll('button')).filter(b => !b.textContent.trim() && !b.getAttribute('aria-label')).length;

    // JSON-LD schema
    const schemas = [];
    document.querySelectorAll('script[type="application/ld+json"]').forEach(s => {
      try {
        const d = JSON.parse(s.textContent);
        if (d['@graph']) d['@graph'].forEach(g => schemas.push(g['@type']));
        else if (d['@type']) schemas.push(d['@type']);
      } catch {}
    });

    return {
      title, desc, canonical, robots, ogTitle, ogImage, twCard, viewport, lang, hreflang,
      h1s, headings,
      noAlt, emptyAltDecorative, missingDims, imgTotal: imgs.length,
      hasMain, hasHeader, hasFooter, hasNav, skipLink,
      linksNoText, buttonsNoLabel,
      schemas,
    };
  });

  // Grade this template
  if (status !== 200 && label !== '404') record('critical', `${label} HTTP ${status}`);
  if (!audit.title) record('critical', `${label}: missing <title>`);
  else if (audit.title.length > 65) record('warning', `${label}: title too long (${audit.title.length} chars)`);

  if (!audit.desc) record('critical', `${label}: missing meta description`);
  else if (audit.desc.length < 50) record('warning', `${label}: meta description too short (${audit.desc.length} chars)`);
  else if (audit.desc.length > 165) record('warning', `${label}: meta description too long (${audit.desc.length} chars)`);

  if (!audit.canonical) record('critical', `${label}: missing canonical URL`);
  if (!audit.ogTitle) record('warning', `${label}: missing og:title`);
  if (!audit.ogImage) record('warning', `${label}: missing og:image`);
  if (!audit.twCard) record('warning', `${label}: missing twitter:card`);
  if (!audit.viewport) record('critical', `${label}: missing viewport meta`);
  if (!audit.lang) record('warning', `${label}: missing <html lang>`);

  if (audit.h1s.length === 0) record('critical', `${label}: no H1`);
  else if (audit.h1s.length > 1) record('warning', `${label}: ${audit.h1s.length} H1s — ${audit.h1s.join(' | ')}`);

  if (audit.noAlt > 0) record('warning', `${label}: ${audit.noAlt}/${audit.imgTotal} images missing alt`);
  if (audit.missingDims > 3) record('info', `${label}: ${audit.missingDims} images without explicit dimensions (potential CLS)`);

  if (!audit.hasMain) record('warning', `${label}: no <main> landmark`);
  if (!audit.skipLink) record('info', `${label}: no skip-to-content link (a11y)`);

  if (audit.linksNoText > 0) record('warning', `${label}: ${audit.linksNoText} anchor(s) with no text/label`);
  if (audit.buttonsNoLabel > 0) record('warning', `${label}: ${audit.buttonsNoLabel} button(s) with no text/label`);

  const schemaTypes = new Set(audit.schemas);
  if (label === 'pdp' && !schemaTypes.has('Product')) record('warning', `pdp: missing Product schema`);
  if (label === 'post' && !schemaTypes.has('Article')) record('warning', `post: missing Article schema`);
  if (!schemaTypes.has('BreadcrumbList') && !['404', 'cart', 'checkout'].includes(label)) record('info', `${label}: missing BreadcrumbList schema`);

  console.log(`${label.padEnd(9)} | H1:${audit.h1s.length} | alt-miss:${audit.noAlt}/${audit.imgTotal} | main:${audit.hasMain?'✓':'✗'} | skip:${audit.skipLink?'✓':'✗'} | schemas:${[...new Set(audit.schemas)].join(',').slice(0,50)}`);
}

console.log('\n=== FINDINGS ===');
console.log('\nCRITICAL:');
findings.critical.forEach(f => console.log('  ✗', f));
console.log('\nWARNING:');
findings.warning.forEach(f => console.log('  ⚠', f));
console.log('\nINFO:');
findings.info.forEach(f => console.log('  ·', f));

console.log('\ntotals — critical:', findings.critical.length, ' warning:', findings.warning.length, ' info:', findings.info.length);
await b.close();
