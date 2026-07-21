<?php
/**
 * "What the cave says" dark review band. Renders the top three approved reviews
 * of the product; skips the band entirely if there are no approved reviews yet.
 *
 * @package Dankcave
 */

$args    = wp_parse_args( $args ?? array(), array( 'product' => null ) );
$product = $args['product'] ?: ( $GLOBALS['product'] ?? null );
if ( ! $product ) { return; }

if ( ! comments_open() && 0 === (int) $product->get_review_count() ) {
	return;
}

$avg   = (float) $product->get_average_rating();
$count = (int) $product->get_review_count();

$reviews = get_comments( array(
	'post_id' => $product->get_id(),
	'status'  => 'approve',
	'type'    => 'review',
	'number'  => 3,
	'orderby' => 'comment_karma',
	'order'   => 'DESC',
) );

// Fallback to newest 3 if no karma is set.
if ( empty( $reviews ) ) {
	$reviews = get_comments( array(
		'post_id' => $product->get_id(),
		'status'  => 'approve',
		'type'    => 'review',
		'number'  => 3,
		'orderby' => 'comment_date_gmt',
		'order'   => 'DESC',
	) );
}

if ( empty( $reviews ) ) {
	return;
}
?>
<section class="pdp-reviews">
	<div class="pdp-reviews__head">
		<h2 class="pdp-reviews__title"><?php esc_html_e( 'What the cave says', 'dankcave' ); ?></h2>
		<?php if ( $count ) : ?>
			<span class="pdp-reviews__meta"><?php echo esc_html( number_format_i18n( $avg, 1 ) ); ?> ★ · <?php echo esc_html( sprintf( _n( '%d review', '%d reviews', $count, 'dankcave' ), $count ) ); ?></span>
		<?php endif; ?>
	</div>

	<div class="pdp-reviews__grid">
		<?php foreach ( $reviews as $review ) :
			$rating = (int) get_comment_meta( $review->comment_ID, 'rating', true );
			$rating = max( 0, min( 5, $rating ) );
			$stars  = str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating );
			$title  = get_comment_meta( $review->comment_ID, 'review_title', true );
			if ( ! $title ) {
				$title = wp_trim_words( $review->comment_content, 6, '' );
			}
			$verified = wc_review_is_from_verified_owner( $review->comment_ID );
		?>
			<article class="pdp-review">
				<?php if ( $rating ) : ?>
					<div class="pdp-review__stars" aria-label="<?php echo esc_attr( sprintf( __( '%d out of 5 stars', 'dankcave' ), $rating ) ); ?>"><?php echo esc_html( $stars ); ?></div>
				<?php endif; ?>
				<h3 class="pdp-review__title"><?php echo esc_html( $title ); ?></h3>
				<div class="pdp-review__body"><?php echo wp_kses_post( wpautop( $review->comment_content ) ); ?></div>
				<div class="pdp-review__attribution">
					<?php echo esc_html( $review->comment_author ); ?>
					<?php if ( $verified ) : ?>
						· <?php esc_html_e( 'Verified buyer', 'dankcave' ); ?>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
