<?php
/**
 * Pattern: product row — WooCommerce shortcode + section header.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-product-row","align":"wide","style":{"spacing":{"padding":{"top":"56px","bottom":"56px","left":"48px","right":"48px"}}}} -->
<section class="wp-block-group alignwide pattern-product-row" style="padding:56px 48px;">
	<!-- wp:group {"className":"section-head","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
	<div class="wp-block-group section-head">
		<!-- wp:heading {"level":2,"className":"section-head__title","style":{"typography":{"fontSize":"30px","fontWeight":"800","letterSpacing":"-0.02em"}}} -->
		<h2 class="wp-block-heading section-head__title" style="font-size:30px;font-weight:800;letter-spacing:-0.02em;margin:0;">Popular right now</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"section-head__link"} -->
		<p class="section-head__link"><a href="/shop/" style="color:#993331;font-weight:700;text-decoration:none;">Shop all →</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:shortcode -->
	[products limit="4" columns="4" orderby="popularity" class="dc-products-row"]
	<!-- /wp:shortcode -->
</section>
<!-- /wp:group -->
BLOCKS;
