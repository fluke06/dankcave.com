<?php
/**
 * Pattern: Centred community CTA closer — eyebrow + headline + intro + two-button row.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-community","align":"full"} -->
<section class="wp-block-group alignfull pattern-community">
	<div class="pattern-community__inner">
		<p class="pattern-eyebrow pattern-eyebrow--gold">JOIN THE DANKCAVE COMMUNITY</p>
		<h2 class="pattern-community__title">Discover why DankCave is the best online smoke shop.</h2>
		<p class="pattern-community__body">Follow us for the latest updates, exclusive offers and smoking tips. Where quality meets affordability &mdash; happy smoking!</p>
		<div class="pattern-community__buttons">
			<a class="pattern-community__btn pattern-community__btn--dark" href="/shop/">Start shopping</a>
			<a class="pattern-community__btn pattern-community__btn--light" href="https://instagram.com/">Follow us</a>
		</div>
	</div>
</section>
<!-- /wp:group -->
BLOCKS;
