<?php
/**
 * Home section — "Shop by category". Asymmetric 6-tile lifestyle grid.
 * Layout: 4×3 grid, top-left feature tile is 2×2, then two 2×1 rows filling the rest.
 *
 * Data source: top 6 product_cat terms (excluding "Uncategorized"), ordered by
 * product count DESC. Falls back to 6 demo tiles if the shop has no categories.
 *
 * @package Dankcave
 */

$heading    = get_theme_mod( 'dankcave_sbc_heading',    'Shop by category' );
$link_label = get_theme_mod( 'dankcave_sbc_link_label', 'Shop all →' );
$link_url   = get_theme_mod( 'dankcave_sbc_link_url',   home_url( '/shop/' ) );

// Grid position for each of the 6 tiles (matches the design mockup exactly).
$spans = array(
	array( 'col' => '1 / 3', 'row' => '1 / 3' ), // 1st: 2x2 feature
	array( 'col' => '3 / 5', 'row' => '1 / 2' ), // 2nd: 2x1 top-right
	array( 'col' => '3 / 4', 'row' => '2 / 3' ), // 3rd: 1x1 middle-left
	array( 'col' => '4 / 5', 'row' => '2 / 3' ), // 4th: 1x1 middle-right
	array( 'col' => '1 / 3', 'row' => '3 / 4' ), // 5th: 2x1 bottom-left
	array( 'col' => '3 / 5', 'row' => '3 / 4' ), // 6th: 2x1 bottom-right
);

$tiles = array();

if ( taxonomy_exists( 'product_cat' ) ) {
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'exclude'    => array( get_option( 'default_product_cat' ) ),
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 6,
	) );
	if ( ! is_wp_error( $terms ) && $terms ) {
		foreach ( $terms as $term ) {
			$thumb_id  = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
			$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'dc-category-tile' ) : '';
			$tiles[] = array(
				'name'     => $term->name,
				'count'    => (int) $term->count,
				'url'      => get_term_link( $term ),
				'image'    => $thumb_url,
			);
		}
	}
}

// Fallback demo tiles if the shop is empty.
if ( empty( $tiles ) ) {
	$tiles = array(
		array( 'name' => 'Bongs',       'count' => 124, 'url' => '#', 'image' => '' ),
		array( 'name' => 'Vaporizers',  'count' => 48,  'url' => '#', 'image' => '' ),
		array( 'name' => 'Dab Rigs',    'count' => 86,  'url' => '#', 'image' => '' ),
		array( 'name' => 'Pens',        'count' => 37,  'url' => '#', 'image' => '' ),
		array( 'name' => 'Rolling',     'count' => 92,  'url' => '#', 'image' => '' ),
		array( 'name' => 'Accessories', 'count' => 210, 'url' => '#', 'image' => '' ),
	);
}

// Pastel fallbacks for tiles missing images (still lets the grid look complete).
$tile_fallbacks = array( 'var(--cream-500)', 'var(--cream-600)', 'var(--cream-700)', 'var(--cream-400)', 'var(--warm-highlight)', 'var(--cream-800)' );
?>
<section class="home-sbc">
	<div class="wrap">
		<div class="section-head section-head--padded">
			<h2 class="section-head__title"><?php echo esc_html( $heading ); ?></h2>
			<a class="section-head__link" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?></a>
		</div>
	</div>
	<div class="category-grid">
		<?php foreach ( $tiles as $i => $tile ) :
			if ( ! isset( $spans[ $i ] ) ) { break; }
			$style = sprintf( 'grid-column: %s; grid-row: %s;', $spans[ $i ]['col'], $spans[ $i ]['row'] );
			if ( empty( $tile['image'] ) ) {
				$style .= sprintf( ' background:%s;', $tile_fallbacks[ $i % count( $tile_fallbacks ) ] );
			}
		?>
			<a class="category-tile" href="<?php echo esc_url( $tile['url'] ); ?>" style="<?php echo esc_attr( $style ); ?>">
				<?php if ( ! empty( $tile['image'] ) ) : ?>
					<img class="category-tile__img" src="<?php echo esc_url( $tile['image'] ); ?>" alt="" loading="lazy">
				<?php endif; ?>
				<div class="category-tile__overlay" aria-hidden="true"></div>
				<div class="category-tile__meta">
					<div class="category-tile__name"><?php echo esc_html( $tile['name'] ); ?></div>
					<div class="category-tile__count">
						<?php
						/* translators: %d: number of products in the category */
						printf( esc_html( _n( '%d product →', '%d products →', $tile['count'], 'dankcave' ) ), (int) $tile['count'] );
						?>
					</div>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>
