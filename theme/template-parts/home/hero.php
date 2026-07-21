<?php
/**
 * Home hero — big display type + product image + callout + dual-tone CTA.
 * Editable via Appearance → Customize → Home Hero.
 *
 * @package Dankcave
 */

$left_1     = get_theme_mod( 'dankcave_hero_left_line1',   'Sess' );
$left_2     = get_theme_mod( 'dankcave_hero_left_line2',   'ions' );
$right      = get_theme_mod( 'dankcave_hero_right_word',   'higher.' );
$lede       = get_theme_mod( 'dankcave_hero_lede',         'Curated vapes and glass that blur the line between gear and ritual.' );
$cta_label  = get_theme_mod( 'dankcave_hero_cta_label',    'Discover collection' );
$cta_url    = get_theme_mod( 'dankcave_hero_cta_url',      home_url( '/shop/' ) );
$hero_img   = get_theme_mod( 'dankcave_hero_image',        DANKCAVE_URI . 'assets/images/hero-product-placeholder.png' );
$hero_alt   = get_theme_mod( 'dankcave_hero_image_alt',    'E-Vape One' );
$cal_title  = get_theme_mod( 'dankcave_hero_callout_title','E-Vape One' );
$cal_body   = get_theme_mod( 'dankcave_hero_callout_body', "Ceramic-coil vapor.\nOne-button sessions." );
?>
<section class="hero" aria-label="<?php esc_attr_e( 'Featured', 'dankcave' ); ?>">
	<div class="hero__stage">
		<div class="hero__glow" aria-hidden="true"></div>

		<h1 class="hero__headline hero__headline--left">
			<span><?php echo esc_html( $left_1 ); ?></span>
			<span><?php echo esc_html( $left_2 ); ?></span>
		</h1>

		<img class="hero__product" src="<?php echo esc_url( $hero_img ); ?>" alt="<?php echo esc_attr( $hero_alt ); ?>" loading="eager" fetchpriority="high">

		<div class="hero__callout" role="complementary">
			<div class="hero__callout-title"><?php echo esc_html( $cal_title ); ?></div>
			<div class="hero__callout-body"><?php echo nl2br( esc_html( $cal_body ) ); ?></div>
		</div>

		<div class="hero__headline hero__headline--right" aria-hidden="true"><?php echo esc_html( $right ); ?></div>

		<div class="hero__cta-block">
			<a class="hero__cta" href="<?php echo esc_url( $cta_url ); ?>">
				<span class="hero__cta-label"><?php echo esc_html( $cta_label ); ?></span>
				<span class="hero__cta-arrow" aria-hidden="true">&#x2197;</span>
			</a>
			<p class="hero__lede"><?php echo esc_html( $lede ); ?></p>
		</div>
	</div>
</section>
