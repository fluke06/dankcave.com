// Verify mobile fixes: touch-visible card actions + new compare modal at 390px.
import { chromium, devices } from 'playwright';
import fs from 'fs';
const OUT = new URL('./screenshots/mobile-verify', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });

const b = await chromium.launch();
const ctx = await b.newContext({ ...devices['iPhone 13'], deviceScaleFactor: 2 });
const p = await ctx.newPage();
p.on('pageerror', e => console.log('JS ERR', e.message));

// 1) shop cards on touch: hover actions must be visible without hover
await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p.waitForTimeout(600);
const actionsVisible = await p.evaluate(() => {
  const a = document.querySelector('.product-card__hover-actions');
  if (!a) return { present: false };
  const cs = getComputedStyle(a);
  return { present: true, opacity: cs.opacity, pointerEvents: cs.pointerEvents, transform: cs.transform };
});
console.log('shop-card actions:', JSON.stringify(actionsVisible));
await p.screenshot({ path: `${OUT}/shop-cards.png`, fullPage: false });

// 2) tap wishlist + compare on two cards to build compare list
await p.evaluate(() => {
  document.querySelectorAll('.product-card [data-dc-compare]').forEach((btn, i) => { if (i < 3) btn.click(); });
});
await p.waitForTimeout(600);
await p.screenshot({ path: `${OUT}/compare-tray.png`, fullPage: false });

// 3) open compare modal
await p.click('[data-dc-compare-open]');
await p.waitForTimeout(1200);
await p.screenshot({ path: `${OUT}/compare-modal.png`, fullPage: false });

const modalInfo = await p.evaluate(() => {
  const modal = document.getElementById('dc-compare-modal');
  const panel = document.querySelector('.dc-compare-modal__panel');
  const rm = document.querySelectorAll('[data-dc-compare-remove]');
  const bestBadge = document.querySelector('.dc-compare-table__badge--best');
  const stockChip = document.querySelector('.dc-compare-table__chip');
  const table = document.querySelector('.dc-compare-table');
  const wrap = document.querySelector('.dc-compare-table-wrap');
  return {
    open: modal?.getAttribute('data-open') === 'true',
    panelW: panel ? Math.round(panel.getBoundingClientRect().width) : 0,
    panelH: panel ? Math.round(panel.getBoundingClientRect().height) : 0,
    removeButtons: rm.length,
    bestBadge: !!bestBadge,
    stockChip: stockChip?.textContent?.trim(),
    tableW: table ? Math.round(table.getBoundingClientRect().width) : 0,
    wrapW: wrap ? Math.round(wrap.getBoundingClientRect().width) : 0,
    wrapOverflow: wrap ? wrap.scrollWidth - wrap.clientWidth : 0,
  };
});
console.log('compare modal:', JSON.stringify(modalInfo, null, 2));

// 4) click per-column remove and verify re-render
await p.evaluate(() => document.querySelector('[data-dc-compare-remove]')?.click());
await p.waitForTimeout(1000);
await p.screenshot({ path: `${OUT}/compare-after-remove.png`, fullPage: false });
const after = await p.evaluate(() => {
  const heads = document.querySelectorAll('.dc-compare-table__head');
  return { cols: heads.length };
});
console.log('after per-column remove:', JSON.stringify(after));

// 5) close via close button (explicit selector — backdrop also has data-dc-compare-close)
await p.click('button.dc-compare-modal__close');
await p.waitForTimeout(500);
const closed = await p.evaluate(() => document.getElementById('dc-compare-modal')?.hidden === true);
console.log('close button works:', closed);

// 6) empty state — clear compare, open modal
await p.evaluate(() => {
  localStorage.setItem('dc-compare', JSON.stringify([]));
});
await p.evaluate(async () => {
  const modal = document.getElementById('dc-compare-modal');
  const body  = modal.querySelector('[data-dc-compare-body]');
  modal.hidden = false;
  modal.setAttribute('data-open', 'true');
  const r = await fetch('/wp-admin/admin-ajax.php?action=dankcave_compare_table&ids=');
  const j = await r.json();
  body.innerHTML = j.data.html;
});
await p.waitForTimeout(600);
await p.screenshot({ path: `${OUT}/compare-empty.png`, fullPage: false });
const emptyState = await p.evaluate(() => ({
  hasIcon: !!document.querySelector('.dc-compare-empty__icon'),
  hasCta: !!document.querySelector('.dc-compare-empty__cta'),
  title: document.querySelector('.dc-compare-empty__title')?.textContent,
}));
console.log('empty state:', JSON.stringify(emptyState));

// 7) desktop check — compare modal at 1280
const ctx2 = await b.newContext({ viewport: { width: 1280, height: 900 } });
const p2 = await ctx2.newPage();
await p2.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
await p2.waitForTimeout(500);
await p2.evaluate(() => {
  document.querySelectorAll('.product-card [data-dc-compare]').forEach((btn, i) => { if (i < 3) btn.click(); });
});
await p2.waitForTimeout(400);
await p2.click('[data-dc-compare-open]');
await p2.waitForTimeout(1200);
await p2.screenshot({ path: `${OUT}/compare-modal-desktop.png`, fullPage: false });

await b.close();
console.log('\nScreenshots:', OUT);
