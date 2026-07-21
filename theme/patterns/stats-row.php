<?php
/**
 * Pattern: 4-cell stats row.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-stats","align":"wide","style":{"spacing":{"padding":{"top":"48px","bottom":"48px","left":"48px","right":"48px"}}}} -->
<section class="wp-block-group alignwide pattern-stats" style="padding:48px;">
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"40px","fontWeight":"800","letterSpacing":"-0.02em"}}} -->
			<h3 class="wp-block-heading" style="font-size:40px;font-weight:800;letter-spacing:-0.02em;margin:0;">$50+</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"13px"}}} -->
			<p style="font-size:13px;color:#8a8e96;margin:4px 0 0;">Free discreet shipping</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"40px","fontWeight":"800","letterSpacing":"-0.02em"}}} -->
			<h3 class="wp-block-heading" style="font-size:40px;font-weight:800;letter-spacing:-0.02em;margin:0;">100%</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"13px"}}} -->
			<p style="font-size:13px;color:#8a8e96;margin:4px 0 0;">Adults 21+ verified</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"40px","fontWeight":"800","letterSpacing":"-0.02em"}}} -->
			<h3 class="wp-block-heading" style="font-size:40px;font-weight:800;letter-spacing:-0.02em;margin:0;">30-day</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"13px"}}} -->
			<p style="font-size:13px;color:#8a8e96;margin:4px 0 0;">Hassle-free returns</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"40px","fontWeight":"800","letterSpacing":"-0.02em"}}} -->
			<h3 class="wp-block-heading" style="font-size:40px;font-weight:800;letter-spacing:-0.02em;margin:0;">20+ yrs</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"13px"}}} -->
			<p style="font-size:13px;color:#8a8e96;margin:4px 0 0;">In the game</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
BLOCKS;
