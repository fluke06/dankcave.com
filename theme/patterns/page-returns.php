<?php
/**
 * Pattern: Returns & Refunds page — pure Gutenberg blocks. Numbers auto-
 * increment via CSS counters on .dc-legal__section.
 *
 * @package Dankcave
 */

return '
<!-- wp:group {"className":"dc-legal","align":"full","tagName":"section"} -->
<section class="wp-block-group alignfull dc-legal">
<!-- wp:group {"className":"dc-legal__hero"} -->
<div class="wp-block-group dc-legal__hero">
<!-- wp:heading {"level":1,"className":"dc-legal__title"} -->
<h1 class="wp-block-heading dc-legal__title">14 days.<br>No <span class="dc-legal__title-accent">drama</span>.</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__lede"} -->
<p class="dc-legal__lede">14-day returns, fast damage claims, and store credit &mdash; here is how it all works.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__body"} -->
<div class="wp-block-group dc-legal__body">

<!-- wp:paragraph {"className":"dc-legal__intro"} -->
<p class="dc-legal__intro">Not happy with something? Here is exactly how returns, damage claims, exchanges, and refunds work at DankCave.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">How to return an item</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">If you are unsatisfied for any reason, you may request a return within <strong>14 days of delivery</strong> by emailing <a href="mailto:returns@dankcave.com">returns@dankcave.com</a> with your order number and reason for return.</p>
<!-- /wp:paragraph -->
<!-- wp:list {"className":"dc-legal__list"} -->
<ul class="dc-legal__list"><!-- wp:list-item --><li>DankCave only accepts returns of unused product within 14 days of delivery.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Used product will not be returned under any circumstance.</li><!-- /wp:list-item --><!-- wp:list-item --><li>All returns must have an RMA number prior to shipping.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Returns incur a 15% restocking fee.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Returns in exchange for store credit do not incur the 15% restocking fee.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Shipping charges and rush processing fees are non-refundable.</li><!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">Once a return has been filed online and approved, a prepaid shipping label will be emailed to you.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Broken, incorrect, or mislabeled items</h2>
<!-- /wp:heading -->
<!-- wp:group {"className":"dc-legal__card"} -->
<div class="wp-block-group dc-legal__card">
<!-- wp:paragraph -->
<p>Received something broken, incorrect, or mislabeled? File a claim by emailing <span class="dc-legal__card-accent">returns@dankcave.com</span> within 72 hours of delivery.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">Include a few photos of the packaging and item, and be sure to add your order number to the email.</p>
<!-- /wp:paragraph -->
<!-- wp:list {"className":"dc-legal__list"} -->
<ul class="dc-legal__list"><!-- wp:list-item --><li>We will replace the broken item(s) with a functional unit at no additional cost.</li><!-- /wp:list-item --><!-- wp:list-item --><li>We may request that you send back the original broken item &mdash; at no cost to you.</li><!-- /wp:list-item --></ul>
<!-- /wp:list -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Returned to sender</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">If your package has been returned to sender due to an invalid address, you can choose to have it reshipped to an alternate or corrected address. Postage cost will not be covered.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">Please contact us at <a href="mailto:support@dankcave.com">support@dankcave.com</a> if your package has been returned.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Filing windows</h2>
<!-- /wp:heading -->
<!-- wp:list {"className":"dc-legal__list"} -->
<ul class="dc-legal__list"><!-- wp:list-item --><li><strong>Returns:</strong> within 14 days of receiving your item &mdash; email returns@dankcave.com with your order number and reason.</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Damage claims:</strong> within 72 hours of receiving the item &mdash; email returns@dankcave.com with your order number and photos of the item and packaging.</li><!-- /wp:list-item --></ul>
<!-- /wp:list -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Can I exchange for store credit?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">Yes &mdash; you have 14 days. Email us at <a href="mailto:support@dankcave.com">support@dankcave.com</a> with your order number. Once the item has been returned and approved, a store credit will be applied to your DankCave account.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Do you accept returns on used items?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">No, we do not accept returns on used items.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">When will I get my refund?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">Once a refund has been approved and issued, please allow 3&ndash;5 business days for your bank to clear the funds.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->

</section>
<!-- /wp:group -->
';
