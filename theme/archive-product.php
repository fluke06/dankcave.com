<?php
/**
 * Shop archive — /shop/, /product-category/<slug>/, /product-tag/<slug>/.
 * Two-column layout with sticky filter sidebar on the left and a 3-up
 * product grid on the right.
 *
 * @package Dankcave
 */

if ( ! function_exists( 'WC' ) ) {
	// If Woo isn't active fall back to a simple archive.
	get_header();
	echo '<main class="wrap"><p>' . esc_html__( 'Shop unavailable.', 'dankcave' ) . '</p></main>';
	get_footer();
	return;
}

get_header();

$queried  = is_shop() ? null : get_queried_object();
$is_cat   = $queried instanceof WP_Term && 'product_cat' === $queried->taxonomy;
$title    = $is_cat ? $queried->name : ( is_shop() ? get_the_title( wc_get_page_id( 'shop' ) ) : woocommerce_page_title( false ) );
$description = $is_cat && $queried->description
	? $queried->description
	: get_theme_mod( 'dankcave_shop_description', '' );
$total_products = $is_cat ? (int) $queried->count : (int) wp_count_posts( 'product' )->publish;
?>

<div class="wc-archive">

	<?php get_template_part( 'template-parts/shop/breadcrumb' ); ?>

	<header class="shop-header">
		<div class="shop-header__inner">
			<div class="shop-header__title-block">
				<h1 class="shop-header__title"><?php echo esc_html( $title ); ?></h1>
				<?php if ( $description ) : ?>
					<p class="shop-header__desc"><?php echo wp_kses_post( wpautop( $description ) ); ?></p>
				<?php elseif ( $total_products ) : ?>
					<p class="shop-header__desc">
						<?php
						/* translators: %d: number of products in the archive. */
						printf( esc_html( _n( '%d hand-picked piece.', '%d hand-picked pieces.', $total_products, 'dankcave' ) ), (int) $total_products );
						?>
					</p>
				<?php endif; ?>
			</div>
			<div class="shop-header__sort">
				<span class="shop-header__sort-label"><?php esc_html_e( 'Sort', 'dankcave' ); ?></span>
				<?php woocommerce_catalog_ordering(); ?>
			</div>
		</div>
	</header>

	<div class="wc-archive__layout">
		<?php get_template_part( 'template-parts/shop/filter-sidebar' ); ?>

		<div class="wc-archive__main">
			<?php if ( woocommerce_product_loop() ) : ?>
				<div class="product-grid product-grid--3">
					<?php while ( have_posts() ) : the_post();
						$product = wc_get_product( get_the_ID() );
						if ( ! $product ) { continue; }
						get_template_part( 'template-parts/product/card', null, array( 'product' => $product ) );
					endwhile; ?>
				</div>
				<?php get_template_part( 'template-parts/shop/pagination' ); ?>
			<?php else : ?>
				<div class="wc-archive__empty">
					<?php wc_no_products_found(); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

</div>

<?php get_footer(); ?>
