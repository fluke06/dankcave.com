<?php
/**
 * Pattern: Centred community CTA closer — eyebrow + headline + intro + two-button row.
 * The "Follow us" button URL comes from the Customizer's Instagram field so
 * Javid can point it at any social profile without editing the pattern.
 *
 * @package Dankcave
 */

return ( function () {
	$follow_url = trim( (string) get_theme_mod( 'dankcave_social_instagram', 'https://www.instagram.com/dankcaveshop/' ) );
	if ( ! $follow_url ) {
		$follow_url = trim( (string) get_theme_mod( 'dankcave_social_facebook', '' ) );
	}
	if ( ! $follow_url ) {
		$follow_url = home_url( '/' );
	}

	return '
<!-- wp:group {"className":"pattern-community","align":"full"} -->
<section class="wp-block-group alignfull pattern-community">
	<div class="pattern-community__inner">
		<p class="pattern-eyebrow pattern-eyebrow--gold">JOIN THE DANKCAVE COMMUNITY</p>
		<h2 class="pattern-community__title">Discover why DankCave is the best online smoke shop.</h2>
		<p class="pattern-community__body">Follow us for the latest updates, exclusive offers and smoking tips. Where quality meets affordability &mdash; happy smoking!</p>
		<div class="pattern-community__buttons">
			<a class="pattern-community__btn pattern-community__btn--dark" href="' . esc_url( home_url( '/shop/' ) ) . '">Start shopping</a>
			<a class="pattern-community__btn pattern-community__btn--light" href="' . esc_url( $follow_url ) . '" target="_blank" rel="noopener">Follow us</a>
		</div>
	</div>
</section>
<!-- /wp:group -->
';
} )();
