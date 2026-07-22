import { chromium } from 'playwright';
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p = await ctx.newPage();
const errors = [];
p.on('pageerror', e => errors.push(e.message));

const templates = [
  ['home', '/'],
  ['shop', '/shop/'],
  ['pdp', '/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/'],
  ['blog', '/blog/'],
  ['post', '/?p=17726'],
  ['category', '/product-category/smoking-accessories/'],
];

console.log('template   | h1 | title | description | canonical | og:title | og:image | jsonld | schemas');
console.log('-'.repeat(120));

for (const [label, path] of templates) {
  await p.goto('http://localhost:8090' + path, { waitUntil: 'load' });
  const info = await p.evaluate(() => {
    const title       = document.title;
    const desc        = document.querySelector('meta[name="description"]')?.content;
    const canonical   = document.querySelector('link[rel="canonical"]')?.href;
    const ogTitle     = document.querySelector('meta[property="og:title"]')?.content;
    const ogImage     = document.querySelector('meta[property="og:image"]')?.content;
    const twCard      = document.querySelector('meta[name="twitter:card"]')?.content;
    const jsonld      = document.querySelectorAll('script[type="application/ld+json"]').length;
    let schemas = [];
    document.querySelectorAll('script[type="application/ld+json"]').forEach(s => {
      try {
        const d = JSON.parse(s.textContent);
        if (d['@graph']) schemas = schemas.concat(d['@graph'].map(g => g['@type']));
      } catch {}
    });
    const h1Count = document.querySelectorAll('h1').length;
    return { title, desc, canonical, ogTitle, ogImage, twCard, jsonld, schemas: schemas.join(','), h1Count };
  });
  console.log(`${label.padEnd(10)} | ${info.h1Count} | ${(info.title||'').slice(0,25).padEnd(25)} | ${(info.desc||'').slice(0,25).padEnd(25)} | ${info.canonical?'✓':'✗'} | ${info.ogTitle?'✓':'✗'} | ${info.ogImage?'✓':'✗'} | ${info.jsonld} | ${info.schemas.slice(0,40)}`);
}

// Verify llms.txt endpoint
const r = await p.goto('http://localhost:8090/llms.txt', { waitUntil: 'load' });
const llmsContent = await p.content();
console.log('\n/llms.txt  |', r.status(), '| length:', llmsContent.length, 'bytes');

await b.close();
console.log('\nerrors:', errors);
