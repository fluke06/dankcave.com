<?php
/**
 * Product gallery: vertical thumbnails + hero pastel well.
 *
 * @package Dankcave
 */

$args    = wp_parse_args( $args ?? array(), array( 'product' => null ) );
$product = $args['product'] ?: ( $GLOBALS['product'] ?? null );
if ( ! $product ) { return; }

$main_id     = (int) $product->get_image_id();
$gallery_ids = array_map( 'intval', $product->get_gallery_image_ids() );
if ( $main_id && ! in_array( $main_id, $gallery_ids, true ) ) {
	array_unshift( $gallery_ids, $main_id );
}

$has_images = ! empty( $gallery_ids );

if ( $product->is_featured() ) {
	$badge = __( 'BESTSELLER', 'dankcave' );
} elseif ( $product->is_on_sale() ) {
	$badge = __( 'SALE', 'dankcave' );
} else {
	$badge = '';
}

// Deterministic pastel well matching product-card palette.
$palettes = array(
	array( '#fbeef4', '#efe7dd' ),
	array( '#e6efe1', '#efe7dd' ),
	array( '#f3e3d0', '#efe7dd' ),
	array( '#e2e6f1', '#efe7dd' ),
	array( '#efe7dd', '#e2ded3' ),
	array( '#dfead4', '#e9e2d3' ),
	array( '#eee9e0', '#e0d9cc' ),
	array( '#f0e6d9', '#e5dfd0' ),
);
$palette = $palettes[ abs( crc32( (string) $product->get_id() ) ) % count( $palettes ) ];
$hero_bg = sprintf( 'radial-gradient(420px 380px at 55%% 45%%,%s,%s)', $palette[0], $palette[1] );

$hero_url = $main_id
	? wp_get_attachment_image_url( $main_id, 'large' )
	: wc_placeholder_img_src( 'large' );
?>
<div class="pdp-gallery">
	<?php if ( count( $gallery_ids ) > 1 ) : ?>
		<div class="pdp-gallery__thumbs" role="tablist" aria-label="<?php esc_attr_e( 'Product thumbnails', 'dankcave' ); ?>">
			<?php foreach ( $gallery_ids as $i => $img_id ) :
				$thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
				$full_url  = wp_get_attachment_image_url( $img_id, 'large' );
				if ( ! $thumb_url ) { continue; }
			?>
				<button type="button"
					class="pdp-gallery__thumb<?php echo 0 === $i ? ' is-active' : ''; ?>"
					data-full="<?php echo esc_url( $full_url ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'dankcave' ), $i + 1 ) ); ?>">
					<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy">
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="pdp-gallery__hero" style="background: <?php echo esc_attr( $hero_bg ); ?>;">
		<?php if ( $badge ) : ?>
			<span class="pdp-gallery__badge"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<img class="pdp-gallery__image" data-pdp-hero src="<?php echo esc_url( $hero_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
	</div>
</div>
