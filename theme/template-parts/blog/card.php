<?php
/**
 * Reusable blog card partial. 16:9 image well, date + reading time, title.
 * Reused on home "From the blog", blog index, and single-post related posts.
 *
 * Args:
 *   post - WP_Post instance (defaults to global $post)
 *   demo - optional demo data (title, image, date_label)
 *
 * @package Dankcave
 */

$args = wp_parse_args( $args ?? array(), array( 'post' => null, 'demo' => null ) );
$post = $args['post'] ?: ( $GLOBALS['post'] ?? null );
$demo = $args['demo'];

if ( ! $post && ! $demo ) {
	return;
}

if ( $post ) {
	$permalink   = get_permalink( $post );
	$title       = get_the_title( $post );
	$thumb_id    = get_post_thumbnail_id( $post );
	$image_url   = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'dc-blog-card' ) : '';
	$date        = get_the_date( 'M j', $post );
	$reading     = function_exists( 'dankcave_reading_time' ) ? dankcave_reading_time( $post->ID ) : '';
	$date_label  = trim( $date . ( $reading ? ' · ' . $reading : '' ) );
} else {
	$permalink   = '#';
	$title       = $demo['title']      ?? '';
	$image_url   = $demo['image']      ?? '';
	$date_label  = $demo['date_label'] ?? '';
}

// Deterministic soft-warm image well fallback color per post
$wells = array( '#f3e3d0', '#e6ede2', '#efe7dd', '#f2ede8', '#f1e6d6' );
$well  = $wells[ abs( crc32( (string) ( $post ? $post->ID : $title ) ) ) % count( $wells ) ];
?>
<a class="blog-card" href="<?php echo esc_url( $permalink ); ?>">
	<div class="blog-card__well" style="background:<?php echo esc_attr( $well ); ?>">
		<?php if ( $image_url ) : ?>
			<img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy">
		<?php endif; ?>
	</div>
	<div class="blog-card__body">
		<?php if ( $date_label ) : ?>
			<div class="blog-card__date"><?php echo esc_html( $date_label ); ?></div>
		<?php endif; ?>
		<h3 class="blog-card__title"><?php echo esc_html( $title ); ?></h3>
	</div>
</a>
