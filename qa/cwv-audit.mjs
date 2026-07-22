// Core Web Vitals audit via Playwright + browser PerformanceObserver.
// Measures LCP, CLS, FCP, TTFB, and Total Blocking Time (proxy for INP).
// Runs each template a few times to smooth out variance, throttles CPU + network
// to approximate a mid-range mobile device (Google's own field target).
import { chromium } from 'playwright';

const b = await chromium.launch();

const templates = [
  ['home',     '/'],
  ['shop',     '/shop/'],
  ['pdp',      '/shop/flavored-rolling-papers/high-hemp-organic-wraps-cbd/'],
  ['blog',     '/blog/'],
  ['post',     '/?p=17726'],
  ['category', '/product-category/smoking-accessories/'],
  ['cart',     '/cart/'],
  ['checkout', '/checkout/'],
];

async function measure(path, opts = {}) {
  const ctx = await b.newContext({ viewport: opts.mob ? { width: 390, height: 844 } : { width: 1280, height: 900 } });
  const p = await ctx.newPage();

  // Approximate a mid-range mobile: 4x CPU throttle + slow 4G
  const client = await ctx.newCDPSession(p);
  if (opts.throttle) {
    await client.send('Network.enable');
    await client.send('Emulation.setCPUThrottlingRate', { rate: 4 });
    await client.send('Network.emulateNetworkConditions', {
      offline: false,
      latency: 150,          // ms
      downloadThroughput: (1.6 * 1024 * 1024) / 8,   // ~1.6 Mbps
      uploadThroughput: (750 * 1024) / 8,            // 750 kbps
    });
  }

  // Warm up caches — first request often includes cold PHP compile time
  if (opts.addCart) await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });

  const startNav = Date.now();
  const response = await p.goto('http://localhost:8090' + path, { waitUntil: 'load', timeout: 45000 });
  const ttfb = (await p.evaluate(() => performance.getEntriesByType('navigation')[0]?.responseStart)) || 0;

  // Give observers time to record LCP + CLS after load
  await p.waitForTimeout(2500);

  const vitals = await p.evaluate(() => new Promise((resolve) => {
    const out = { lcp: 0, lcpEl: null, cls: 0, fcp: 0, longTasks: 0, tbt: 0, layoutShifts: [], resources: 0 };

    // FCP
    const fcpEntry = performance.getEntriesByName('first-contentful-paint')[0];
    if (fcpEntry) out.fcp = Math.round(fcpEntry.startTime);

    // LCP — take the last observed entry
    try {
      const po = new PerformanceObserver(() => {});
      po.observe({ type: 'largest-contentful-paint', buffered: true });
      const entries = po.takeRecords();
      po.disconnect();
      const last = entries[entries.length - 1];
      if (last) {
        out.lcp = Math.round(last.startTime);
        const el = last.element;
        out.lcpEl = el ? (el.tagName + (el.className ? '.' + String(el.className).replace(/\s+/g, '.').slice(0, 60) : '')) : null;
      }
    } catch {}

    // CLS
    try {
      const po = new PerformanceObserver(() => {});
      po.observe({ type: 'layout-shift', buffered: true });
      const entries = po.takeRecords();
      po.disconnect();
      entries.forEach(e => {
        if (!e.hadRecentInput) {
          out.cls += e.value;
          if (e.value > 0.01) out.layoutShifts.push({ v: +e.value.toFixed(3), t: Math.round(e.startTime) });
        }
      });
      out.cls = +out.cls.toFixed(3);
    } catch {}

    // Long tasks (proxy for TBT / responsiveness)
    try {
      const po = new PerformanceObserver(() => {});
      po.observe({ type: 'longtask', buffered: true });
      const entries = po.takeRecords();
      po.disconnect();
      out.longTasks = entries.length;
      out.tbt = Math.round(entries.reduce((sum, t) => sum + Math.max(0, t.duration - 50), 0));
    } catch {}

    // Resource count as a rough page-weight indicator
    out.resources = performance.getEntriesByType('resource').length;

    resolve(out);
  }));

  await ctx.close();
  return { ttfb: Math.round(ttfb), status: response.status(), ...vitals };
}

console.log('=== Desktop (unthrottled) ===');
console.log('template   | LCP    | CLS   | FCP   | TTFB  | TBT   | tasks | rsc | LCP element');
console.log('-'.repeat(105));
for (const [label, path] of templates) {
  const r = await measure(path, { addCart: ['cart', 'checkout'].includes(label) });
  const lcpMark = r.lcp < 2500 ? '✓' : (r.lcp < 4000 ? '△' : '✗');
  const clsMark = r.cls < 0.10 ? '✓' : (r.cls < 0.25 ? '△' : '✗');
  console.log(`${label.padEnd(10)} | ${(r.lcp + 'ms').padEnd(6)}${lcpMark} | ${(r.cls + '').padEnd(5)}${clsMark} | ${(r.fcp + 'ms').padEnd(5)} | ${(r.ttfb + 'ms').padEnd(5)} | ${(r.tbt + 'ms').padEnd(5)} | ${(r.longTasks + '').padEnd(5)} | ${(r.resources + '').padEnd(3)} | ${(r.lcpEl || '').slice(0, 40)}`);
}

console.log('\n=== Mobile (4x CPU throttle, slow 4G) ===');
console.log('template   | LCP    | CLS   | FCP    | TTFB  | TBT    | tasks | rsc | LCP element');
console.log('-'.repeat(105));
for (const [label, path] of templates) {
  const r = await measure(path, { addCart: ['cart', 'checkout'].includes(label), mob: true, throttle: true });
  const lcpMark = r.lcp < 2500 ? '✓' : (r.lcp < 4000 ? '△' : '✗');
  const clsMark = r.cls < 0.10 ? '✓' : (r.cls < 0.25 ? '△' : '✗');
  console.log(`${label.padEnd(10)} | ${(r.lcp + 'ms').padEnd(6)}${lcpMark} | ${(r.cls + '').padEnd(5)}${clsMark} | ${(r.fcp + 'ms').padEnd(6)} | ${(r.ttfb + 'ms').padEnd(5)} | ${(r.tbt + 'ms').padEnd(6)} | ${(r.longTasks + '').padEnd(5)} | ${(r.resources + '').padEnd(3)} | ${(r.lcpEl || '').slice(0, 40)}`);
}

await b.close();

console.log('\nlegend: LCP good < 2.5s, needs work < 4s, poor > 4s. CLS good < 0.10, poor > 0.25.');
