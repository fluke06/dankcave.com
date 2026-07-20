# Dankcave — custom WooCommerce theme rebuild

## Project overview

Full custom WooCommerce theme built from scratch for Dankcave.com, a smoke shop e-commerce site owned by Javid Aimaque. This project follows the pivot from the "audit + tiered cleanup" quote (see `/Users/christiandizon/Sites/dankcave-audit/`) into a clean-slate rebuild.

**Client**: Javid Aimaque (`javid@dankcave.com`, personal `saimaque99@gmail.com`)
**Site**: https://dankcave.com
**Started**: 2026-07-21
**Timeline target**: 2-3 weeks
**Budget**: $850 flat
**Payment**: Half upfront ($425 received 2026-07-21), half on launch ($425)
**Preferred comms**: Telegram `@Souprah` for day-to-day. Email for scope decisions, milestone approvals, payment references (paper trail).

## Scope

### In scope

- Custom WooCommerce theme built from scratch, taking the same craft approach as the ToolPlaybook build (`/Users/christiandizon/Sites/toolplaybook/`)
- Templates to build:
  - Home
  - Shop / product listing (archive-product)
  - Single product
  - Cart
  - Checkout
  - My Account (dashboard, orders, downloads, addresses, payment methods, account details)
  - Order received (thank-you page)
  - Blog index
  - Single blog post
  - Static pages (about, contact, privacy policy, terms, shipping policy, refund policy)
  - 404
  - Search results
- Design system distilled from the approved home-page mockup (see `design/` folder — Javid greenlit the elevated/editorial direction)
- Block Patterns registered under a "Dankcave" category in the block inserter so Javid can build new pages using the same visual language without leaving Gutenberg. Patterns to build (all using the same design tokens as the theme):
  - Hero: big display type + product cutout
  - Product card grid (4-up, pastel-backed cards)
  - Category tile grid (lifestyle photography)
  - Editorial storytelling band (image + overlay text)
  - Product row with "See all →" header (Popular / Trending / New Products)
  - Blog card row (3-up)
  - Big black CTA band
  - Stats row ($50+, 100%, 30 day, 20+ yrs)
  - Newsletter subscribe band
  - Section header + eyebrow (used above every section)
  - Content page hero (About/Contact/legal)
- Mobile responsive across every template (verified at iPhone 13 width, 390px)
- Data migration: 74 real completed-purchase customers preserved from the current WooCommerce DB, plus Shopify customer data imported into the new install
- Site maintenance mode set up on live production during the rebuild
- Customer PII file removal + Google Search Console cleanup (immediate, one-time)

### Not in scope (client handles)

- Product data (photos, titles, descriptions, SEO, categories). Javid does this himself once the store is live in the new theme.
- Bot user data migration. Not migrating those.
- Marketing (Javid runs this).
- Ongoing support beyond a 14-day post-launch bug-fix window.

## Design direction

**Approved**: the elevated / editorial / DTC-brand-leaning direction, not the cluttered / promo-heavy vibe of the reference sites Javid originally cited (Dankgeek, Dankstop, Headshop).

**Full template set designed and dropped in `design/` folder as `.dc.html` files** (Claude Design format, viewable in any browser via `./support.js`). Nine templates in total, not just home. This means design decisions are LOCKED — no design review cycles needed. Build directly against these.

**Template inventory:**

| File | Represents | WordPress template mapping |
|---|---|---|
| `Dankcave - Broken Headline.dc.html` | Home page (hero with broken "Sess/ions" type treatment) | `front-page.php` |
| `Dankcave - Listing.dc.html` | Shop archive / product listing | `archive-product.php` |
| `Dankcave - Product.dc.html` | Single product page | `single-product.php` |
| `Dankcave - Cart.dc.html` | Cart | `cart/cart.php` (WooCommerce template override) |
| `Dankcave - Checkout.dc.html` | Checkout | `checkout/form-checkout.php` (Woo override) |
| `Dankcave - Account.dc.html` | My Account (dashboard, orders, addresses) | `myaccount/*.php` (Woo overrides) |
| `Dankcave - Blog.dc.html` | Blog index | `home.php` |
| `Dankcave - Blog Post.dc.html` | Single blog post | `single.php` |
| `Dankcave - Page.dc.html` | Generic content page (about, contact, legal, etc.) | `page.php` |

**Style anchors visible across the mockups**:
- Big display type in the hero ("Sessions" / "higher.")
- Pastel product card backgrounds (green, blue, brown, pink)
- Rich lifestyle photography for category tiles
- Clean product cutout imagery for product cards
- Editorial storytelling section ("Twenty years at the torch" style)
- Black hero band near the bottom for newsletter capture ("Vibes, handled with care.")
- Social proof stats row ($50+, 100%, 30 day, 20+ yrs)

**All shop templates (listing, product, cart, checkout, account) inherit this home-page language.** Colors + type + spacing + card treatment stay consistent throughout.

## Design tokens (extracted from the mockup HTMLs)

Turn these into CSS custom properties on `:root` in the theme's main stylesheet. Every template uses the same set.

### Colors

**Dark neutrals** (backgrounds, dominant surface):
- `--ink`: `#171310` (near-black, dominant background)
- `--ink-2`: `#1c1714` (slightly lighter background variant)
- `--ink-3`: `#1a1c21` (cool dark variant)
- `--ink-4`: `#22242a` (neutral dark, dominant text on light)
- `--ink-5`: `#33373d`
- `--ink-6`: `#3f434a`
- `--ink-7`: `#4a4d54`

**Warm cream neutrals** (secondary surfaces, cards):
- `--cream-100`: `#f2f0ee`
- `--cream-200`: `#f2ede8`
- `--cream-300`: `#efe7dd`
- `--cream-400`: `#eee9e0`
- `--cream-500`: `#e7ddd0`
- `--cream-600`: `#e2ded9`
- `--cream-700`: `#dcd7cd`
- `--cream-800`: `#d8d2c8`
- `--cream-900`: `#c8c3ba`

**Muted grays** (secondary text, borders):
- `--muted-1`: `#b7ada3`
- `--muted-2`: `#a7a29a`
- `--muted-3`: `#8a8e96` (most-used muted)
- `--muted-4`: `#5c616a`

**Accents**:
- `--accent-wine`: `#993331` (dominant accent, buttons + CTAs)
- `--accent-wine-dark`: `#362e28`
- `--gold`: `#c9a227` (secondary accent)
- `--gold-dark`: `#b08a3c`
- `--gold-bright`: `#f8c913`
- `--warm-highlight`: `#f3e3d0`
- `--pink-soft`: `#f7e0ea`
- `--green`: `#2e6b3e`
- `--orange`: `#e8871e`

**Base**:
- `--white`: `#fff`

### Typography

**Fonts loaded from Google Fonts** (many are declared but only Gabarito is the default body — trim to used-only when building):

- **Gabarito** (400-800) — BASE default body font. Use for most UI + body copy.
- **Instrument Serif** (regular + italic) — editorial callouts, italic subheads
- **Archivo** (400-700) — UI microcopy alt
- **Space Grotesk** (400/500/700) — nav / mono-adjacent display
- **Unbounded** (400/600/800) — big display type variant
- **Bricolage Grotesque** (variable) — headings alt
- **Rubik Spray Paint** — accent-only, used sparingly
- **Permanent Marker** — accent-only, used sparingly

**Type scale (px):** 11, 12, 13, 13.5, 14, 14.5, 15, 15.5, 16, 17, 18, 19, 20, 22, 24, 26, 28, 30, 32, 34, 40, 44, 46, 52, 56, 60, and 150 (hero-only display).

Standardize to a smaller working scale when building:
- 12 (micro / mono eyebrow)
- 14 (small body)
- 16 (body default)
- 18 (large body)
- 22 (section subhead)
- 28 (section head)
- 40 (h2)
- 60 (h1)
- 150 (hero display, only home)

### Border radii

Standard set observed:
- 12px — small cards
- 16px — medium cards
- 18px — large cards
- 22px — hero cards
- 28px — big hero blocks
- 99px — pill / round buttons
- Split radii (`16px 0 0 16px`, `0 16px 16px 0`) — for edge-attached cards

### Design approach when building

The mockup HTMLs use INLINE STYLES on every element (Claude Design output format). The theme should NOT copy inline styles. Instead:

1. Extract the token values above into `:root` CSS variables in `assets/css/theme.css`
2. Rebuild each template with semantic HTML + external class-based CSS that references the tokens
3. Group repeated patterns (product card, category tile, blog card, etc.) into reusable CSS classes
4. Register those patterns as Gutenberg block patterns (see § Scope) so Javid can compose new pages with them
5. Reference the `.dc.html` files as visual specs, not as code to lift

The mockup HTMLs are the visual truth. The theme code should reproduce that visual output with clean, maintainable CSS.

## Tech stack

- WordPress 6.7+ (production is running current core)
- WooCommerce 10.9.4+ (as of audit; will update as part of rebuild)
- PHP 8.1+
- Custom theme structure similar to ToolPlaybook — direct child of a lean base (or fully custom, no parent theme dependency)
- Vanilla JavaScript (no jQuery in theme code)
- Custom CSS with design tokens (colors, spacing, radii, shadows, type)
- Self-hosted fonts (per performance guidelines)
- Custom SVG icon registry (`tpb_the_icon()` pattern from ToolPlaybook)
- No page-builder dependency (no Elementor, no Divi). Javid's existing Elementor Pro templates on the old site are NOT migrated.

## Third-party integrations to keep working

All of these are handled by their respective WooCommerce plugins — the theme just needs to not break the standard WooCommerce hooks they attach to. Documenting the specific touch-points so nothing gets silently broken during template refactoring.

### Authorize.net (primary payment gateway)

- **Plugin**: `woo-authorize-net-gateway-aim` (Pledged Plugins, currently v6.1.23 on prod)
- **Theme touch-points**:
  - `woocommerce_checkout_payment` action must render intact on the checkout template. This is where the payment methods radio picker + card form appears.
  - Don't override `woocommerce_review_order_before_payment` or similar without keeping the default action's output.
  - Card form CSS is styled by the plugin — theme should not force-override input styles inside `.wc_payment_methods` in a way that breaks card entry validation.
- **API keys**: Already configured in the plugin settings on prod. Not needed by Christian during build. Sandbox/test keys may be needed near end-of-build for QA on the local mirror — Javid to provide.
- **Testing checklist**:
  - Card entry field renders cleanly at desktop + mobile widths
  - Validation messages surface correctly (card number, expiry, CVV, ZIP)
  - Successful test transaction fires the standard WooCommerce order lifecycle
  - AVS/CVV mismatch error styling works

### Goshippo (Shippo — shipping calculation)

- **Plugin**: WooCommerce Shippo integration (currently active)
- **Theme touch-points**:
  - `woocommerce_cart_shipping_methods` needs to render the calculated rates on cart + checkout
  - Address form on checkout must not strip fields Shippo relies on (zip, country, state)
  - Cart page shipping calculator display: keep the ZIP/country entry inputs functional
- **API keys**: Configured in the Shippo plugin's settings on prod. Not needed by Christian during build.
- **Testing checklist**:
  - Cart with items → shipping options populate correctly for a valid US address
  - International addresses trigger the correct rate lookup
  - Free-shipping threshold banner (if configured) displays
  - Label creation flow works from the WP admin Orders screen

### Sezzle (buy-now-pay-later gateway)

- **Plugin**: `sezzle-woocommerce-payment` (currently v6.1.5, active)
- **Theme touch-points**:
  - `woocommerce_single_product_summary` — Sezzle shows a "Buy in 4 interest-free payments of $X" widget under the product price. If the theme's custom single-product template moves the price out of that hook or removes the summary hook chain, the Sezzle widget disappears. Preserve the hook.
  - Checkout payment methods loop must render Sezzle as one of the options alongside Authorize.net.
- **Widget snippet**: Sezzle exposes both a shortcode and an automatic hook. The auto-hook is the default. Verify the widget renders on a test product during QA.

### Newsletter capture (design's black band)

- **Plugin**: `mailchimp-for-wp` (MC4WP, currently v4.12.0, active on prod). Keep it.
- Design's "Vibes, handled with care." black band at the bottom of the home page uses the MC4WP shortcode `[mc4wp_form]` to render the signup form.
- Theme touch-point: the newsletter block just renders that shortcode inside the styled container. No extra plugin needed, no service switch. Whatever list Javid has already set up in MC4WP continues to receive signups.
- Same "wire a shortcode into a Customizer field" pattern from ToolPlaybook can apply here if we want the site owner to be able to swap forms without editing template code, but not required for launch.

### General principle for the build

Do NOT reimplement any of the above functionality in the theme. Let each plugin handle its own logic via its documented WooCommerce hooks. The theme's job is to make sure the templates it renders don't accidentally strip or override those hooks. If a template file is fully custom (not extending a WooCommerce template), always call the required action/filter hooks so the plugins can inject their UI.

## Existing project context

Relevant folders on the local machine:

- `/Users/christiandizon/Sites/dankcave-audit/` — the audit report (`AUDIT-REPORT.md`), evidence backing every claim (`EVIDENCE.md`), extracted UpdraftPlus backup contents, and Playwright scripts
- `/Users/christiandizon/Sites/dankcave-local/` — Docker Compose environment (WordPress + MariaDB + WP-CLI) restored from Javid's UpdraftPlus backup, running locally at http://localhost:8090. Used for reference and safe testing.
- `/tmp/dankcave-live-audit/` — 28 admin-side screenshots from the local mirror audit
- `/tmp/dankcave-live-full/` — 69 admin-side screenshots from the live production audit (via Christian's authenticated session)
- `/tmp/dankcave-state.json` — Christian's Playwright storageState (live production admin session cookies). Expires after WP's session TTL, usually 48h.

The audit's punch list, per-tier pricing, and all findings are now superseded by this rebuild — no need to work from the audit report going forward except as reference material.

## Immediate items (do first before deep work)

Christian promised these would happen "today" in the accepted quote:

1. **Delete `customers_export.csv`** from `/wp-content/uploads/` on live production. Not in Media Library — likely in a plugin export folder like `/wp-content/uploads/wt_iew_exports/` (WebToffee Import/Export). Use File Manager plugin (install → find → delete → uninstall).
2. **Google Search Console cleanup** — URL Inspection on the CSV URL, then Removals tool if it was indexed.
3. **Maintenance mode** — install LightStart or SeedProd Coming Soon, configure "Dankcave is getting an upgrade — back online soon" page, preserve wp-admin access for both admins.

## Data migration plan

**Two sources of customer data to import into the new store:**

### Source 1: 74 real Woo customers from current DB

Query from the extracted DB dump at `/Users/christiandizon/Sites/dankcave-audit/db/dankcave.sql`:

```sql
-- Unique customers who have completed a purchase
SELECT DISTINCT customer_id, billing_email
FROM EswFc_wc_orders
WHERE status IN ('wc-completed', 'wc-processing', 'wc-on-hold', 'wc-refunded');
```

Extract customer records + addresses + purchase history, transform into WooCommerce Customer Import Suite CSV format, import into new install.

### Source 2: Shopify customer data (from pre-WordPress era)

**Received 2026-07-21.** File at `/tmp/dankcave-shopify/customers_export.csv` (also archived at `/Users/christiandizon/Sites/toolplaybook/customers_export.zip`).

Confirmed as **standard Shopify customer CSV export**. Migration is straightforward and stays within scope.

**Data profile:**
- 2,580 total customer records
- 2,217 with email (importable) / 363 without email (skip — WooCommerce requires unique email per customer)
- 1,131 records with at least one completed order (real buyers)
- 1,449 records with zero orders but valid email (marketing prospects who opted in)
- $111,660 total historical Shopify revenue across the 1,131 buyers
- Zero duplicate emails in file
- ~99.9% US-based (2,331 US, 1 AU, 1 CA)

**Import plan:**

1. Skip 363 rows with no email
2. Import 2,217 email-bearing records as WooCommerce customers with:
   - Standard billing fields: name, email, phone, address, country
   - Custom meta: `_shopify_total_spent`, `_shopify_total_orders`, `_source: shopify_import`
   - Marketing opt-in status preserved as MailChimp subscription state
3. Dedupe against the current 74 real Woo customers on email — Shopify record wins for overlaps (richer historical data)
4. Tag customers by segment: `shopify-buyer` (1,131), `shopify-prospect` (~1,086 after email dedupe), `woo-customer` (74)

**Awaiting Javid confirmation (via Telegram)**: whether to import all 2,217 email records or restrict to only the 1,131 confirmed buyers. Default is all 2,217 unless he says otherwise.

## Client preferences (learned from ToolPlaybook)

**Communication:**
- No em dashes in client-facing messages. Use commas, periods, or parenthetical asides instead.
- Full sentences on their own lines. Avoid breaking sentences awkwardly across lines.
- Warm but professional tone. Not overly casual, not corporate.
- Sign off simply ("Best, Christian") — no co-author signatures, no elaborate valedictions.

**Pricing:**
- Christian's anchor rate is low compared to market (ToolPlaybook was $450 for weeks of work). He tends to underprice himself and second-guess quotes downward.
- Hold the line on quoted numbers. If Javid negotiates, offer to trim scope not price.
- Payment: usually half up front, half on delivery.

**Deliverables:**
- Everything documented in a docs site (VitePress on Cloudflare Pages, following ToolPlaybook's pattern)
- Bundled markdown docs inside the theme zip for offline reading
- Handoff includes a written go-live checklist
- 14-day post-launch bug-fix window on Christian's dime

## Git / commit conventions

- **NEVER** add `Co-Authored-By: Claude` or any co-author line to commits or PR descriptions on any project. This is a permanent preference (per user memory).
- Small, focused commits with meaningful messages.
- Version-tag every release in `style.css` header AND a `THEME_VERSION` constant in `functions.php`. Bump both together.
- Follow the same pattern ToolPlaybook uses.

## Working conventions for this project

**Environment setup:**
- Local development happens against the Docker mirror at `/Users/christiandizon/Sites/dankcave-local/` (WordPress + MariaDB + WP-CLI). See `README.md` there for start/stop commands.
- Theme source of truth lives in `theme/` in this project folder.
- Symlink or rsync `theme/` into the Docker container's `/var/www/html/wp-content/themes/dankcave/` for testing.

**Testing:**

Full Playwright-based QA suite. Same craft rigor as ToolPlaybook. Tests live under `qa/` in this project.

Reference: ToolPlaybook's mobile QA scripts are at `/tmp/tpb-mobile-qa/` — reuse the patterns (Playwright + chromium headless, iPhone 13 viewport for mobile, screenshot-based visual checks, `page.evaluate` for DOM assertions).

**Baseline checks that run against every template (desktop 1280×900 + mobile iPhone 13 390×844):**

- HTTP 200 on the URL
- Zero JS `console.error` events during load
- Zero unhandled `pageerror` events
- Zero horizontal overflow: `Math.max(document.documentElement.scrollWidth, document.body.scrollWidth) === window.innerWidth` at 390px mobile
- Viewport meta does NOT contain `user-scalable=no` or `maximum-scale=1` (WCAG accessibility)
- `<html lang>` attribute present
- Every primary nav link resolves 200
- Sample 5 nav/CTA elements have tap targets ≥ 44×44 CSS px
- Page has exactly one `<h1>`
- Heading hierarchy is contiguous (no h1 → h4 jumps)
- `<title>`, `<meta name=description>`, and `<link rel=canonical>` present
- Full-page screenshots captured for visual regression comparison

**Per-template scenario tests:**

| Template | Beyond the baseline |
|---|---|
| Home (front-page.php) | Hero renders. Category grid, product carousels, editorial band, newsletter capture visible. Newsletter form is rendered (MC4WP shortcode present). |
| Shop archive | Product grid populated. Category filters work (or gracefully fail). Pagination renders when >1 page. Sort dropdown functional. |
| Single product | Product image gallery renders. Add-to-cart button visible + tappable. Sezzle "buy in 4 payments of $X" widget visible under price. Product data (SKU, price, description) all populated. Related products section renders. |
| Cart | Cart contents render. Update quantity works. Remove item works. Cart totals recalculate. Coupon code field renders. Proceed-to-checkout CTA visible. Empty-cart state renders when cart is empty. |
| Checkout | Billing form renders all required fields. Shipping calculation triggers on address change. Payment methods (Authorize.net + Sezzle) render as radio options. Card entry form is stylable and validates. Order review updates on shipping/payment change. |
| Order received | Order summary renders. Order number visible. Payment status shown. Customer email confirmation triggered (check email log). |
| My Account | Dashboard, Orders, Addresses, Payment methods, Account details, Logout all reachable. Each sub-page renders without errors. |
| Blog index | Featured post + card grid render. Category chips work. Pagination when >1 page. |
| Single blog post | Post content renders. Reading-time visible. Related posts row. Sidebar with newsletter + featured reviews (if we mirror ToolPlaybook's blog sidebar). |
| Page (about/contact/legal) | Content renders. Contact form (if present) renders with captcha + honeypot fields. |
| 404 | Custom 404 renders with search + back-to-home link. |
| Search results | Search box works. Results render. Empty-results state renders when no matches. |

**End-to-end (E2E) flow test (one full run before launch):**

1. Visit homepage
2. Click into a product from home
3. Add to cart (verify mini-cart updates)
4. Go to cart, adjust quantity, verify totals recalc
5. Proceed to checkout
6. Fill checkout form with test data
7. Select payment method
8. Submit order (use Authorize.net SANDBOX API keys — request from Javid near launch)
9. Land on order-received page
10. Verify order shows in wp-admin → Orders

Run this end-to-end sequence on the Docker local mirror before every production deploy.

**Visual regression against the .dc.html mockups:**

For each template, screenshot the built theme page at desktop + mobile, then compare visually against the corresponding `design/*.dc.html` mockup rendered in the same viewport. Not a pixel-perfect check (the mockups use inline styles that don't correspond to the theme's semantic CSS), but a same-shape check. Deltas are flagged for review, not auto-failed.

**Block patterns test:**

Run once in wp-admin: log in, create a new WordPress page, open the block inserter, verify all 11 registered patterns appear in the "Dankcave" category, insert each one, save the page, view the front-end, confirm each pattern renders correctly with its default styling.

**When to run:**

- Baseline suite on every meaningful commit (or at least end-of-day). Cheap to run, catches regressions fast.
- E2E flow test at each milestone (home page done, shop done, checkout done).
- Full suite + visual regression pass before launch.

**Test script organization:**

Put scripts under `qa/`:
```
qa/
├── package.json          # dependencies (playwright)
├── playwright.config.js  # base URL, viewports, timeout defaults
├── baseline.spec.js      # baseline checks that run against every URL
├── e2e-purchase.spec.js  # end-to-end add-to-cart to order-received
├── block-patterns.spec.js # verifies patterns in the block editor
└── screenshots/          # visual regression baselines (gitignored)
```

Playwright runs against `http://localhost:8090` (the Docker mirror) during development. Point at staging or production URL near launch via `PW_BASE_URL` env var.

**Deployment:**
- Build a WordPress theme zip via a `build-zip.sh` script (mirror the ToolPlaybook approach).
- Zip is delivered to Javid via the docs site's public folder for consistency with how ToolPlaybook is delivered.
- Never push code directly to production. Test in Docker, then Javid uploads to production himself (or Christian uploads via WP admin with Javid's admin credentials).

## Docs site

**In scope.** Mirrors the ToolPlaybook docs pattern (see `/Users/christiandizon/Sites/toolplaybook/docs/`). VitePress-based static site, deployed on Cloudflare Pages under `dankcave-docs.pages.dev`. Same visual language + navigation structure as ToolPlaybook's docs so Javid gets a familiar reading experience across projects.

**Sections to build:**

| Doc page | Purpose |
|---|---|
| `index.md` — Overview | Landing page: what the theme is, who it is for, quick links |
| `install.md` — Install | Fresh WordPress install, upload the theme zip, activate, initial permalinks + reading settings |
| `products.md` — Add a product | HIGH-VALUE for Javid. Field-by-field walkthrough of adding a WooCommerce product — title, description, images, gallery, categories, tags, price, stock, variations, dimensions, weight, shipping class. Includes SEO tips (Yoast) per product. Uses screenshots of the actual WP admin UI. This is the doc Javid will read most since he is filling in every product himself. |
| `blog.md` — Add a blog post | Add a WordPress post. Category, featured image, excerpts, tags, publishing. |
| `pages.md` — Static pages + block patterns | How to build a page using the Dankcave block patterns (Hero, Product Grid, Category Grid, Editorial Band, etc.). Includes a screenshot walkthrough of picking each pattern from the block inserter. |
| `customizer.md` — Customizer reference | Every Customizer field documented with a screenshot showing where it appears on the front-end. |
| `newsletter.md` — Newsletter setup | MailChimp for WordPress setup for the black newsletter band. Same detail level as ToolPlaybook's newsletter guide. |
| `integrations.md` — Payment + shipping | Confirming Authorize.net gateway settings and Shippo shipping calculation. Not day-to-day docs, more of a reference so Javid can check settings. |
| `maintenance.md` — Maintenance mode | How to turn the site's maintenance mode on and off from Customizer. |
| `golive.md` — Go-live checklist | Pre-launch checklist: content review, product spot-check, cache purge, DNS, SSL, Google Search Console re-verify, Yoast sitemap re-submit, remove any dev flags, monitor first-24h traffic. |

**Docs delivery pattern (same as ToolPlaybook):**

- Docs live in `docs/` inside this project.
- Deploy target: Cloudflare Pages (free tier, `npm run deploy` via wrangler).
- Downloadable theme zip served from the docs site's `public/` folder at `dankcave-docs.pages.dev/dankcave.zip` for easy re-install.
- Bundled offline: markdown copies of every doc page ship inside the theme zip too so the site owner can read locally if the docs URL ever goes down.

**Docs build sequence:** Docs are the LAST major deliverable, populated as templates get finalized (so screenshots capture the actual live UI, not mid-build placeholders). Start writing them after home + shop + product templates are built and locked, roughly week 2.

## Current status

**As of 2026-07-21:**

- ✅ Quote accepted at $850 flat
- ✅ Design direction approved (elevated / editorial, fully locked — Javid explicitly said the reference sites were just starting points and he trusts the elevated read)
- ✅ Deposit ($425) received
- ✅ Shopify customer export received + verified as standard CSV format. Data profile in § Data migration plan.
- ✅ Full design set (9 templates) dropped in `design/` folder. Design tokens extracted into § Design tokens.
- ⬜ Immediate items (PII deletion, GSC cleanup, maintenance mode) not yet started
- ⬜ Theme skeleton not yet scaffolded

**Next steps in order:**

1. Send confirmation reply thanking Javid + confirming deposit invoice + asking for Shopify data sample
2. Handle immediate items on live production (PII deletion + GSC + maintenance mode)
3. Christian drops home-page design HTML into `design/`
4. Scaffold the empty theme directory structure in `theme/`
5. Build design tokens (colors, type, spacing) from the mockup
6. Build header + footer + primary nav first (used by every other template)
7. Build home page next
8. Build shop archive → single product → cart → checkout in that order
9. Build blog + static pages
10. QA pass
11. Data migration (Woo customers + Shopify import) after theme is stable
12. Launch checklist + go-live

## Reference material

- ToolPlaybook (prior project, same aesthetic craft, similar structure): `/Users/christiandizon/Sites/toolplaybook/`
- ToolPlaybook docs: https://toolplaybook-docs.pages.dev
- Live production Dankcave: https://dankcave.com (will be in maintenance mode during rebuild)
- Client's original reference sites (aesthetic BASELINE, NOT the target): dankgeek.com, dankstop.com, headshop.com
