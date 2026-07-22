<?php
/**
 * Pattern: About hero — dark radial band with gold eyebrow and big display headline.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-about-hero","align":"full"} -->
<section class="wp-block-group alignfull pattern-about-hero">
	<div class="pattern-about-hero__inner">
		<!-- wp:paragraph {"className":"pattern-about-hero__eyebrow"} -->
		<p class="pattern-about-hero__eyebrow">THE BEST ONLINE HEADSHOP &amp; SMOKE SHOP</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1,"className":"pattern-about-hero__title"} -->
		<h1 class="wp-block-heading pattern-about-hero__title">Your ultimate destination for the good stuff.</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"pattern-about-hero__intro"} -->
		<p class="pattern-about-hero__intro">Welcome to DankCave — the premier online headshop, dedicated to top-quality gear for all your smoking needs.</p>
		<!-- /wp:paragraph -->
	</div>
</section>
<!-- /wp:group -->
BLOCKS;
