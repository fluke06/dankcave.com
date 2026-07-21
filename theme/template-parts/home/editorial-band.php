<?php
/**
 * Home section — "Twenty years at the torch." editorial band. Full-width dark
 * video-backed band with a wine pill badge, big two-line heading, subcopy, and
 * cream CTA on the left. Everything editable via Customizer.
 *
 * @package Dankcave
 */

$badge     = get_theme_mod( 'dankcave_editorial_badge',    'Since 2006' );
$heading_1 = get_theme_mod( 'dankcave_editorial_heading_1', 'Twenty years' );
$heading_2 = get_theme_mod( 'dankcave_editorial_heading_2', 'at the torch.' );
$body      = get_theme_mod( 'dankcave_editorial_body',      "We started as borosilicate glassblowers. Every piece we stock today is something we'd keep on our own shelf." );
$cta_label = get_theme_mod( 'dankcave_editorial_cta_label', 'Read our story' );
$cta_url   = get_theme_mod( 'dankcave_editorial_cta_url',   home_url( '/about/' ) );
$video_url = get_theme_mod( 'dankcave_editorial_video_url', DANKCAVE_URI . 'assets/videos/editorial-band-placeholder.mp4' );
$poster    = get_theme_mod( 'dankcave_editorial_poster',    '' );
?>
<section class="home-editorial">
	<div class="wrap">
		<div class="editorial-band">
			<video class="editorial-band__video"
				<?php if ( $poster ) : ?>poster="<?php echo esc_url( $poster ); ?>"<?php endif; ?>
				autoplay muted loop playsinline preload="metadata"
				aria-hidden="true">
				<?php if ( $video_url ) : ?>
					<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
				<?php endif; ?>
			</video>
			<div class="editorial-band__overlay" aria-hidden="true"></div>
			<div class="editorial-band__content">
				<?php if ( $badge ) : ?>
					<span class="editorial-band__badge"><?php echo esc_html( strtoupper( $badge ) ); ?></span>
				<?php endif; ?>
				<h2 class="editorial-band__heading">
					<?php echo esc_html( $heading_1 ); ?><br>
					<?php echo esc_html( $heading_2 ); ?>
				</h2>
				<p class="editorial-band__body"><?php echo esc_html( $body ); ?></p>
				<?php if ( $cta_label ) : ?>
					<a class="editorial-band__cta" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_label ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
