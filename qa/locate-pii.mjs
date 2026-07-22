// Try to locate customers_export.csv on live production via existing session.
// Read-only navigation only. No delete clicks.

import { chromium } from 'playwright';
import fs from 'fs';

const STATE = '/tmp/dankcave-state.json';
if (!fs.existsSync(STATE)) {
  console.error('No saved session at /tmp/dankcave-state.json. Re-run login-helper.mjs.');
  process.exit(1);
}

const b = await chromium.launch({ headless: true });
const ctx = await b.newContext({ storageState: STATE, viewport: { width: 1400, height: 900 } });
const p = await ctx.newPage();
p.setDefaultTimeout(20000);

// Verify session still valid
await p.goto('https://dankcave.com/wp-admin/', { waitUntil: 'domcontentloaded' }).catch(() => {});
await p.waitForTimeout(2000);
const url = p.url();
if (!url.includes('/wp-admin') || url.includes('wp-login.php')) {
  console.error('Session invalid, redirected to', url);
  await b.close();
  process.exit(1);
}
console.log('Session valid, at', url);

// Try Media Library search
await p.goto('https://dankcave.com/wp-admin/upload.php?s=customers_export&mode=list', { waitUntil: 'domcontentloaded' });
await p.waitForTimeout(2000);
await p.screenshot({ path: '/tmp/dankcave-media-search.png', fullPage: true });
const mediaHits = await p.evaluate(() => {
  const rows = document.querySelectorAll('.wp-list-table tbody tr');
  return Array.from(rows).map(r => r.innerText.replace(/\s+/g, ' ').trim().slice(0, 200));
});
console.log(`\nMedia Library search for "customers_export": ${mediaHits.length} hits`);
mediaHits.forEach((h, i) => console.log(`  ${i+1}. ${h}`));

// Also check the WebToffee plugin's export history if that admin page exists
const webToffeeUrls = [
  '/wp-admin/admin.php?page=wt_import_export_for_woo_basic',
  '/wp-admin/admin.php?page=wt_users_customer_ie',
  '/wp-admin/admin.php?page=wt-iew-exporter',
];
for (const u of webToffeeUrls) {
  const res = await p.goto('https://dankcave.com' + u, { waitUntil: 'domcontentloaded' }).catch(() => null);
  if (res && res.status() === 200) {
    console.log(`\nFound plugin admin page at: ${u}`);
    await p.waitForTimeout(1000);
    await p.screenshot({ path: `/tmp/dankcave-plugin-${u.split('page=')[1]}.png`, fullPage: true });
    break;
  }
}

// Direct URL check on the file itself
const guessedPaths = [
  '/wp-content/uploads/customers_export.csv',
  '/wp-content/uploads/wt_iew_exports/customers_export.csv',
  '/wp-content/uploads/2024/customers_export.csv',
  '/wp-content/uploads/wpallimport/uploads/customers_export.csv',
];
console.log('\nDirect URL probes:');
for (const path of guessedPaths) {
  const res = await ctx.request.get('https://dankcave.com' + path).catch(() => null);
  console.log(`  ${res ? res.status() : 'ERR'}  ${path}`);
}

await b.close();
console.log('\nDone. Screenshots saved to /tmp/dankcave-*.png');
