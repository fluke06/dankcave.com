<?php
/**
 * Pattern: section header (eyebrow + headline + optional link).
 * Returns Gutenberg block markup as a string.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"tagName":"header","className":"section-head","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
<header class="wp-block-group section-head">
	<!-- wp:group {"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"className":"section-head__eyebrow"} -->
		<p class="section-head__eyebrow">EDITORIAL</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":2,"className":"section-head__title"} -->
		<h2 class="wp-block-heading section-head__title">Section headline goes here.</h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->
	<!-- wp:paragraph {"className":"section-head__link"} -->
	<p class="section-head__link"><a href="#">See all →</a></p>
	<!-- /wp:paragraph -->
</header>
<!-- /wp:group -->
BLOCKS;
