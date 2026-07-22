<?php
/**
 * Pattern: Customer-satisfaction dark band — narrative + CTA on the left, 2×2 stat cards on the right.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-satisfaction","align":"full"} -->
<section class="wp-block-group alignfull pattern-satisfaction">
	<div class="pattern-satisfaction__inner">
		<div class="pattern-satisfaction__copy">
			<p class="pattern-eyebrow pattern-eyebrow--gold-bright">CUSTOMER SATISFACTION</p>
			<h2 class="pattern-satisfaction__title">Our top priority, every order.</h2>
			<p class="pattern-satisfaction__body">We&#8217;re committed to exceptional service, fast shipping and a seamless shopping experience. Questions? Our friendly, knowledgeable support team is always here to help.</p>
			<a class="pattern-satisfaction__cta" href="/contact/">Contact support</a>
		</div>
		<div class="pattern-satisfaction__stats">
			<div class="pattern-satisfaction__stat">
				<div class="pattern-satisfaction__stat-value">20+</div>
				<div class="pattern-satisfaction__stat-label">years in the game</div>
			</div>
			<div class="pattern-satisfaction__stat">
				<div class="pattern-satisfaction__stat-value">Free</div>
				<div class="pattern-satisfaction__stat-label">shipping over $50</div>
			</div>
			<div class="pattern-satisfaction__stat">
				<div class="pattern-satisfaction__stat-value">Discreet</div>
				<div class="pattern-satisfaction__stat-label">packaging, always</div>
			</div>
			<div class="pattern-satisfaction__stat">
				<div class="pattern-satisfaction__stat-value">21+</div>
				<div class="pattern-satisfaction__stat-label">adults only</div>
			</div>
		</div>
	</div>
</section>
<!-- /wp:group -->
BLOCKS;
