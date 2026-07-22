<?php
/**
 * Pattern: Why-choose feature cards — 3 cards on white background, each with a wine-red accent bar.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-why","align":"full"} -->
<section class="wp-block-group alignfull pattern-why">
	<div class="pattern-why__inner">
		<div class="pattern-why__head">
			<!-- wp:paragraph {"className":"pattern-eyebrow pattern-eyebrow--gold"} -->
			<p class="pattern-eyebrow pattern-eyebrow--gold">WHY CHOOSE US</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"className":"pattern-h2"} -->
			<h2 class="wp-block-heading pattern-h2">Three reasons the cave wins</h2>
			<!-- /wp:heading -->
		</div>
		<div class="pattern-why__grid">
			<article class="pattern-why__card">
				<span class="pattern-why__bar"></span>
				<h3 class="pattern-why__title">Variety &amp; quality</h3>
				<p class="pattern-why__body">Classic glass bongs, discreet dab pens and bubblers, advanced rigs, or essential rolling accessories &mdash; we have it all.</p>
			</article>
			<article class="pattern-why__card">
				<span class="pattern-why__bar"></span>
				<h3 class="pattern-why__title">Curated collection</h3>
				<p class="pattern-why__body">From glass pipes and water pipes to hand pipes, we carefully curate our inventory so every product meets our standards.</p>
			</article>
			<article class="pattern-why__card">
				<span class="pattern-why__bar"></span>
				<h3 class="pattern-why__title">Affordable prices</h3>
				<p class="pattern-why__body">Top-quality smoking accessories should be accessible to everyone &mdash; competitive prices, zero compromise on quality.</p>
			</article>
		</div>
	</div>
</section>
<!-- /wp:group -->
BLOCKS;
