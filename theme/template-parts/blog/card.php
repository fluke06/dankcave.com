<?php
/**
 * Reusable blog card partial. 16:10 image well, category+date+reading eyebrow,
 * title, optional excerpt, "Read article →" CTA. Reused across the home page
 * "From the blog" row, the blog index, category archives, and single-post
 * related posts.
 *
 * Args:
 *   post          - WP_Post instance (defaults to global $post)
 *   demo          - optional demo data (title, image, date_label, excerpt, eyebrow)
 *   show_excerpt  - whether to render the excerpt line (default false)
 *
 * @package Dankcave
 */

$args = wp_parse_args( $args ?? array(), array( 'post' => null, 'demo' => null, 'show_excerpt' => false ) );
$post = $args['post'] ?: ( $GLOBALS['post'] ?? null );
$demo = $args['demo'];

if ( ! $post && ! $demo ) {
	return;
}

if ( $post ) {
	$permalink  = get_permalink( $post );
	$title      = get_the_title( $post );
	$thumb_id   = get_post_thumbnail_id( $post );
	$image_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'dc-blog-card' ) : '';
	$date       = get_the_date( 'M j', $post );
	$reading    = function_exists( 'dankcave_reading_time' ) ? dankcave_reading_time( $post->ID ) : '';
	$cats       = get_the_category( $post->ID );
	$eyebrow    = strtoupper( trim( ( $cats && ! is_wp_error( $cats ) ? $cats[0]->name . ' · ' : '' ) . $date . ( $reading ? ' · ' . $reading : '' ) ) );
	$excerpt    = get_the_excerpt( $post );
} else {
	$permalink = '#';
	$title     = $demo['title']      ?? '';
	$image_url = $demo['image']      ?? '';
	$eyebrow   = $demo['eyebrow']    ?? ( $demo['date_label'] ?? '' );
	$excerpt   = $demo['excerpt']    ?? '';
}

?>
<a class="blog-card" href="<?php echo esc_url( $permalink ); ?>">
	<div class="blog-card__well">
		<?php if ( $image_url ) : ?>
			<img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy" width="720" height="450">
		<?php endif; ?>
	</div>
	<div class="blog-card__body">
		<?php if ( $eyebrow ) : ?>
			<div class="blog-card__date"><?php echo esc_html( $eyebrow ); ?></div>
		<?php endif; ?>
		<h3 class="blog-card__title"><?php echo esc_html( $title ); ?></h3>
		<?php if ( $args['show_excerpt'] && $excerpt ) : ?>
			<div class="blog-card__excerpt"><?php echo esc_html( wp_trim_words( $excerpt, 22 ) ); ?></div>
		<?php endif; ?>
		<span class="blog-card__cta"><?php esc_html_e( 'Read article →', 'dankcave' ); ?></span>
	</div>
</a>
