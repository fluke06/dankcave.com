<?php
/**
 * Search results template. Renders posts, pages, and products in a unified
 * card grid with a filter row at the top showing counts per type.
 *
 * @package Dankcave
 */

get_header();

$query          = get_search_query();
$total_results  = (int) $GLOBALS['wp_query']->found_posts;
$type_filter    = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : '';

$type_labels = array(
	''         => __( 'All', 'dankcave' ),
	'product'  => __( 'Products', 'dankcave' ),
	'post'     => __( 'Articles', 'dankcave' ),
	'page'     => __( 'Pages', 'dankcave' ),
);
?>

<section class="dc-search">
	<header class="dc-search__header">
		<div class="dc-search__eyebrow">
			<?php
			if ( $query ) {
				echo esc_html( sprintf( _n( '%d result for', '%d results for', $total_results, 'dankcave' ), $total_results ) );
			} else {
				esc_html_e( 'Search the cave', 'dankcave' );
			}
			?>
		</div>
		<h1 class="dc-search__title">
			<?php if ( $query ) : ?>
				&ldquo;<?php echo esc_html( $query ); ?>&rdquo;
			<?php else : ?>
				<?php esc_html_e( 'What are you looking for?', 'dankcave' ); ?>
			<?php endif; ?>
		</h1>

		<form class="dc-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="dc-search-input"><?php esc_html_e( 'Search', 'dankcave' ); ?></label>
			<input type="search" id="dc-search-input" name="s" value="<?php echo esc_attr( $query ); ?>" placeholder="<?php esc_attr_e( 'Search products, guides, categories…', 'dankcave' ); ?>">
			<button type="submit"><?php esc_html_e( 'Search', 'dankcave' ); ?></button>
		</form>

		<nav class="dc-search__filters" aria-label="<?php esc_attr_e( 'Filter results', 'dankcave' ); ?>">
			<?php foreach ( $type_labels as $type_key => $label ) :
				$url = add_query_arg( array( 's' => $query, 'type' => $type_key ) );
				$is_active = ( $type_filter === $type_key );
			?>
				<a class="dc-search__filter<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="dc-search__results">
			<?php
			$products = array();
			$posts    = array();
			$pages    = array();
			while ( have_posts() ) : the_post();
				$type = get_post_type();
				if ( $type_filter && $type_filter !== $type ) { continue; }
				if ( 'product' === $type )      $products[] = get_the_ID();
				elseif ( 'post' === $type )     $posts[]    = get_post();
				elseif ( 'page' === $type )     $pages[]    = get_post();
			endwhile;
			?>

			<?php if ( ! empty( $products ) && ( ! $type_filter || 'product' === $type_filter ) ) : ?>
				<section class="dc-search__group">
					<h2 class="dc-search__group-title"><?php esc_html_e( 'Products', 'dankcave' ); ?></h2>
					<div class="product-grid product-grid--3">
						<?php foreach ( $products as $pid ) :
							$product = wc_get_product( $pid );
							if ( $product ) {
								get_template_part( 'template-parts/product/card', null, array( 'product' => $product ) );
							}
						endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( ! empty( $posts ) && ( ! $type_filter || 'post' === $type_filter ) ) : ?>
				<section class="dc-search__group">
					<h2 class="dc-search__group-title"><?php esc_html_e( 'Articles', 'dankcave' ); ?></h2>
					<div class="dc-blog__grid">
						<?php foreach ( $posts as $post ) :
							get_template_part( 'template-parts/blog/card', null, array( 'post' => $post ) );
						endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( ! empty( $pages ) && ( ! $type_filter || 'page' === $type_filter ) ) : ?>
				<section class="dc-search__group">
					<h2 class="dc-search__group-title"><?php esc_html_e( 'Pages', 'dankcave' ); ?></h2>
					<ul class="dc-search__pages">
						<?php foreach ( $pages as $page ) : ?>
							<li>
								<a class="dc-search__page-link" href="<?php echo esc_url( get_permalink( $page ) ); ?>">
									<span class="dc-search__page-title"><?php echo esc_html( $page->post_title ); ?></span>
									<span class="dc-search__page-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $page ), 20 ) ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>
		</div>

		<?php the_posts_pagination( array(
			'prev_text' => __( '← Prev', 'dankcave' ),
			'next_text' => __( 'Next →', 'dankcave' ),
			'mid_size'  => 2,
			'class'     => 'dc-search__pagination',
		) ); ?>

	<?php else : ?>
		<div class="dc-search__empty">
			<p><?php esc_html_e( 'No results yet. Try broader terms — “bong”, “rolling papers”, “vape”.', 'dankcave' ); ?></p>
			<a class="dc-404__cta dc-404__cta--primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse everything', 'dankcave' ); ?></a>
		</div>
	<?php endif; ?>
</section>

<?php get_footer(); ?>
