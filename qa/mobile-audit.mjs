// Mobile viewport audit — captures every interactive feature at iPhone 13
// (390x844) and iPhone SE (375x667) and logs any obvious sizing/overflow issues.
import { chromium, devices } from 'playwright';
import fs from 'fs';

const OUT = new URL('./screenshots/mobile-audit', import.meta.url).pathname;
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch();

const findings = [];
function log(msg) { findings.push(msg); console.log(msg); }

async function check(label, deviceKey, action) {
  const dev = deviceKey === 'se' ? { viewport: { width: 375, height: 667 } } : { ...devices['iPhone 13'], deviceScaleFactor: 2 };
  const ctx = await b.newContext(dev);
  const p = await ctx.newPage();
  const errors = [];
  p.on('pageerror', e => errors.push(e.message));
  try {
    await action(p, label + '-' + deviceKey);
  } catch (e) {
    log(`✗ ${label} @ ${deviceKey}: ${e.message.slice(0, 100)}`);
  }
  if (errors.length) log(`✗ ${label} @ ${deviceKey}: JS errors — ${errors.slice(0, 2).join(' | ')}`);
  await ctx.close();
}

async function shot(p, name) {
  await p.screenshot({ path: `${OUT}/${name}.png`, fullPage: false });
}
async function overflow(p) {
  return p.evaluate(() => Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) - window.innerWidth);
}

// Home
await check('home', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/', { waitUntil: 'load' });
  await p.waitForTimeout(600);
  await shot(p, name);
  const ov = await overflow(p);
  if (ov > 0) log(`✗ home overflow ${ov}px`); else log(`✓ home no overflow`);
});

// Header hamburger opens mobile menu
await check('header-menu', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/', { waitUntil: 'load' });
  await p.waitForTimeout(400);
  await p.click('.site-header__toggle');
  await p.waitForTimeout(400);
  await shot(p, name);
  const open = await p.evaluate(() => document.getElementById('primary-nav-mobile')?.hidden === false);
  log(open ? '✓ hamburger opens mobile nav' : '✗ hamburger did not open');
});

// Product card hover actions on TOUCH devices — hover doesn't fire, so quickview / wishlist / compare are unreachable
await check('shop-cards', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  await shot(p, name);
  const visible = await p.evaluate(() => {
    const actions = document.querySelector('.product-card__hover-actions');
    if (!actions) return false;
    const cs = getComputedStyle(actions);
    return cs.opacity !== '0' && cs.display !== 'none';
  });
  log(visible ? '✓ shop card hover actions visible on mobile' : '✗ shop card hover actions HIDDEN — quickview/wishlist/compare unreachable on touch');
});

// Filters sidebar on shop
await check('shop-filters', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  const sidebar = await p.$('.shop-filters');
  if (sidebar) {
    const box = await sidebar.boundingBox();
    log(`shop filters box: ${Math.round(box.width)}x${Math.round(box.height)} at (${Math.round(box.x)},${Math.round(box.y)})`);
    if (box.width < 100) log('✗ filters column too narrow on mobile');
    else log('✓ filters column has usable width');
  }
});

// PDP layout
await check('pdp', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/shop/rollings/rolling-papers/zig-zag-rolling-papers-king-size/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  await shot(p, name);
  const ov = await overflow(p);
  if (ov > 0) log(`✗ pdp overflow ${ov}px`); else log(`✓ pdp no overflow`);
});

// Cart drawer opens on mobile
await check('cart-drawer', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  await p.goto('http://localhost:8090/', { waitUntil: 'load' });
  await p.click('.cart-summary');
  await p.waitForTimeout(500);
  await shot(p, name);
  const info = await p.evaluate(() => {
    const d = document.getElementById('dc-cart-drawer');
    const panel = d?.querySelector('.dc-cart-drawer__panel');
    return { open: d?.getAttribute('data-open') === 'true', panelW: panel ? Math.round(panel.getBoundingClientRect().width) : 0, panelR: panel ? Math.round(panel.getBoundingClientRect().right) : 0 };
  });
  log(info.open ? `✓ cart drawer opens (${info.panelW}px wide)` : '✗ cart drawer did not open');
  if (info.panelR > 400) log(`✗ drawer overshoots viewport (right=${info.panelR})`); else log(`✓ drawer fits viewport`);
});

// Search modal
await check('search-modal', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/', { waitUntil: 'load' });
  await p.click('.header-search-pill');
  await p.waitForTimeout(400);
  await shot(p, name);
  const info = await p.evaluate(() => {
    const form = document.querySelector('.search-modal__form');
    const box = form?.getBoundingClientRect();
    return { open: document.getElementById('search-modal')?.getAttribute('data-open') === 'true', formW: box ? Math.round(box.width) : 0 };
  });
  log(info.open ? `✓ search modal opens (form ${info.formW}px)` : '✗ search modal did not open');
});

// Quickview modal
await check('quickview', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/', { waitUntil: 'load' });
  await p.evaluate(async () => {
    const r = await fetch('/wp-admin/admin-ajax.php?action=dankcave_quickview&product_id=18135');
    const j = await r.json();
    const qv = document.getElementById('dc-quickview');
    qv.querySelector('[data-dc-quickview-body]').innerHTML = j.data.html;
    qv.hidden = false;
    qv.setAttribute('data-open', 'true');
    if (window.jQuery) window.jQuery(qv.querySelector('form.variations_form')).wc_variation_form();
  });
  await p.waitForSelector('.dc-quickview__title', { timeout: 5000 });
  await p.waitForTimeout(500);
  await shot(p, name);
  const info = await p.evaluate(() => {
    const panel = document.querySelector('.dc-quickview__panel');
    const grid = document.querySelector('.dc-quickview__grid');
    return {
      panelW: panel ? Math.round(panel.getBoundingClientRect().width) : 0,
      cols: grid ? getComputedStyle(grid).gridTemplateColumns : '',
    };
  });
  log(`quickview panel: ${info.panelW}px cols: ${info.cols}`);
});

// Compare modal
await check('compare-modal', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
  await p.evaluate(() => {
    // Store compare items in localStorage
    localStorage.setItem('dc-compare', JSON.stringify(['17009', '16986', '15668']));
  });
  await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(800);
  await p.evaluate(() => document.querySelector('[data-dc-compare-open]')?.click());
  await p.waitForTimeout(1500);
  await shot(p, name);
  const info = await p.evaluate(() => {
    const modal = document.getElementById('dc-compare-modal');
    const panel = document.querySelector('.dc-compare-modal__panel');
    const table = document.querySelector('.dc-compare-table');
    return {
      open: modal?.getAttribute('data-open') === 'true',
      panelW: panel ? Math.round(panel.getBoundingClientRect().width) : 0,
      tableW: table ? Math.round(table.getBoundingClientRect().width) : 0,
      cols: table ? table.querySelectorAll('.dc-compare-table__head').length : 0,
    };
  });
  log(info.open ? `✓ compare modal open (panel ${info.panelW}px, table ${info.tableW}px, ${info.cols} cols)` : '✗ compare modal did not open');
});

// Compare tray at bottom on mobile
await check('compare-tray-mobile', 'ip13', async (p, name) => {
  await p.evaluate(() => localStorage.setItem('dc-compare', JSON.stringify(['17009', '16986'])));
  await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(800);
  await shot(p, name);
  const info = await p.evaluate(() => {
    const tray = document.getElementById('dc-compare-tray');
    const box = tray?.getBoundingClientRect();
    return { visible: tray?.getAttribute('data-visible') === 'true', width: box ? Math.round(box.width) : 0, right: box ? Math.round(box.right) : 0 };
  });
  log(info.visible ? `✓ compare tray visible (${info.width}px, right=${info.right})` : '✗ compare tray hidden');
});

// Checkout form + shipping method card
await check('checkout', 'ip13', async (p, name) => {
  await p.goto('http://localhost:8090/?add-to-cart=17009', { waitUntil: 'load' });
  await p.waitForTimeout(400);
  await p.goto('http://localhost:8090/checkout/', { waitUntil: 'load' });
  await p.waitForTimeout(600);
  await shot(p, name);
  const info = await p.evaluate(() => ({
    billingCardW: Math.round(document.querySelector('.dc-checkout-card--billing')?.getBoundingClientRect().width || 0),
    asideW: Math.round(document.querySelector('.dc-checkout__aside')?.getBoundingClientRect().width || 0),
    shippingCard: !!document.querySelector('.dc-checkout-card--shipping-method'),
  }));
  log(`checkout: billing ${info.billingCardW}px, aside ${info.asideW}px, shipping card: ${info.shippingCard}`);
});

// iPhone SE (375px) — tighter constraints
await check('shop', 'se', async (p, name) => {
  await p.goto('http://localhost:8090/shop/', { waitUntil: 'load' });
  await p.waitForTimeout(500);
  await shot(p, name);
  const ov = await overflow(p);
  if (ov > 0) log(`✗ shop @375 overflow ${ov}px`); else log(`✓ shop @375 no overflow`);
});

await b.close();

console.log('\n=== summary (' + findings.length + ' notes) ===');
