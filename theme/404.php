<?php
/**
 * 404 not-found page.
 *
 * @package Dankcave
 */

get_header();

$popular = new WP_Query( array(
	'post_type'      => 'product',
	'posts_per_page' => 3,
	'status'         => 'publish',
	'orderby'        => 'meta_value_num',
	'meta_key'       => 'total_sales',
	'order'          => 'DESC',
) );
?>

<section class="dc-404">
	<div class="dc-404__inner">
		<div class="dc-404__badge">404</div>
		<h1 class="dc-404__title"><?php esc_html_e( 'Lost in the cave.', 'dankcave' ); ?></h1>
		<p class="dc-404__lede"><?php esc_html_e( 'The page you followed does not exist. Might have moved, or maybe we never made it. Either way, here are some good ways back.', 'dankcave' ); ?></p>

		<div class="dc-404__ctas">
			<a class="dc-404__cta dc-404__cta--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'dankcave' ); ?></a>
			<a class="dc-404__cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop everything', 'dankcave' ); ?></a>
		</div>

		<form class="dc-404__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="dc-404-search"><?php esc_html_e( 'Search the cave', 'dankcave' ); ?></label>
			<input type="search" id="dc-404-search" name="s" placeholder="<?php esc_attr_e( 'Search for anything…', 'dankcave' ); ?>">
			<button type="submit"><?php esc_html_e( 'Search', 'dankcave' ); ?></button>
		</form>
	</div>

	<?php if ( $popular->have_posts() ) : ?>
		<div class="dc-404__popular">
			<h2 class="dc-404__popular-title"><?php esc_html_e( 'Popular right now', 'dankcave' ); ?></h2>
			<div class="product-grid product-grid--3">
				<?php while ( $popular->have_posts() ) : $popular->the_post();
					$product = wc_get_product( get_the_ID() );
					if ( $product ) {
						get_template_part( 'template-parts/product/card', null, array( 'product' => $product ) );
					}
				endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	<?php endif; ?>
</section>

<?php get_footer(); ?>
