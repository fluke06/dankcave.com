<?php
/**
 * Pattern: Shipping Policy page — pure Gutenberg blocks so Javid can edit
 * every heading, paragraph, and list from the block editor. Numbering is
 * driven by CSS counters on `.dc-legal__section` so reordering / adding /
 * removing sections auto-updates the numbers.
 *
 * @package Dankcave
 */

return '
<!-- wp:group {"className":"dc-legal","align":"full","tagName":"section"} -->
<section class="wp-block-group alignfull dc-legal">
<!-- wp:html -->
<div class="dc-legal__progress"><div class="dc-legal__progress-bar"></div></div>
<!-- /wp:html -->

<!-- wp:group {"className":"dc-legal__hero"} -->
<div class="wp-block-group dc-legal__hero">
<!-- wp:heading {"level":1,"className":"dc-legal__title"} -->
<h1 class="wp-block-heading dc-legal__title">Fast, discreet,<br><span class="dc-legal__title-accent">free</span> over $25.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"dc-legal__lede"} -->
<p class="dc-legal__lede">Free over $25, shipped from California in 2&ndash;5 business days. Here is the full rundown.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__body"} -->
<div class="wp-block-group dc-legal__body">

<!-- wp:paragraph {"className":"dc-legal__intro"} -->
<p class="dc-legal__intro">Everything you need to know about getting your order &mdash; costs, timing, tracking, and what happens if something goes wrong in transit.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">How much does shipping cost?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">We offer <strong>free shipping on all USA orders over $25</strong>.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">How long does shipping take?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">We ship via USPS or FedEx, with estimated delivery of 2&ndash;5 business days. Orders are fulfilled from our California warehouse.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">How long until my order ships?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">All orders placed before 2pm PST ship out the same day, Monday through Friday. Orders placed after 2pm PST ship out the following business day.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">Orders can be tracked by logging into <a href="/my-account/">your account</a> page.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Do you offer international shipping?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">We do not offer international shipping.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">What if my item is damaged during shipping?</h2>
<!-- /wp:heading -->
<!-- wp:group {"className":"dc-legal__card"} -->
<div class="wp-block-group dc-legal__card">
<!-- wp:paragraph -->
<p>If your item is damaged during shipping or defective, DankCave will replace it at <span class="dc-legal__card-accent">no cost</span> &mdash; return postage paid.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">Simply email <a href="mailto:returns@dankcave.com">returns@dankcave.com</a> within 72 hours of delivery with your order number and a few photos of the packaging and damaged item(s). Please keep all original packaging.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Do you ship to a PO Box?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">We do not ship to PO Box addresses.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->

</section>
<!-- /wp:group -->
';
