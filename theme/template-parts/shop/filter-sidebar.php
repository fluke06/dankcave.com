<?php
/**
 * Shop filter sidebar — categories, price ranges, availability.
 *
 * Filtering is URL-param driven (link-based) rather than JS. Users click a
 * category or price range and land on a filtered archive. For a fully
 * interactive multi-checkbox experience, a filter plugin (e.g. YITH WooCommerce
 * Ajax Product Filter) can be dropped in later.
 *
 * @package Dankcave
 */

$current_term_id = 0;
if ( is_product_category() ) {
	$current_term = get_queried_object();
	$current_term_id = $current_term ? (int) $current_term->term_id : 0;
}

$categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'exclude'    => array( get_option( 'default_product_cat' ) ),
	'orderby'    => 'count',
	'order'      => 'DESC',
	'number'     => 12,
) );

$price_ranges = array(
	array( 'label' => __( 'Under $50', 'dankcave' ),  'min' => '',    'max' => '50' ),
	array( 'label' => __( '$50–100', 'dankcave' ),    'min' => '50',  'max' => '100' ),
	array( 'label' => __( '$100–200', 'dankcave' ),   'min' => '100', 'max' => '200' ),
	array( 'label' => __( '$200+', 'dankcave' ),      'min' => '200', 'max' => '' ),
);

$current_min = isset( $_GET['min_price'] ) ? (int) $_GET['min_price'] : 0;
$current_max = isset( $_GET['max_price'] ) ? (int) $_GET['max_price'] : 0;

$in_stock_url = add_query_arg( 'in_stock', '1' );
$on_sale_url  = add_query_arg( 'on_sale',  '1' );
?>
<aside class="shop-filters" aria-label="<?php esc_attr_e( 'Filters', 'dankcave' ); ?>">
	<div class="shop-filters__head">
		<div class="shop-filters__title"><?php esc_html_e( 'Filters', 'dankcave' ); ?></div>
		<a class="shop-filters__clear" href="<?php echo esc_url( is_shop() ? wc_get_page_permalink( 'shop' ) : get_term_link( get_queried_object() ) ); ?>"><?php esc_html_e( 'Clear', 'dankcave' ); ?></a>
	</div>

	<div class="shop-filters__group">
		<div class="shop-filters__label"><?php esc_html_e( 'Price', 'dankcave' ); ?></div>
		<div class="shop-filters__pills">
			<?php foreach ( $price_ranges as $range ) :
				$url = remove_query_arg( array( 'min_price', 'max_price' ) );
				if ( $range['min'] !== '' ) $url = add_query_arg( 'min_price', $range['min'], $url );
				if ( $range['max'] !== '' ) $url = add_query_arg( 'max_price', $range['max'], $url );
				$is_active = ( (string) $current_min === (string) $range['min'] ) && ( (string) $current_max === (string) $range['max'] );
			?>
				<a class="shop-filters__pill<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $range['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
		<div class="shop-filters__group">
			<div class="shop-filters__label"><?php esc_html_e( 'Category', 'dankcave' ); ?></div>
			<ul class="shop-filters__list">
				<?php foreach ( $categories as $cat ) :
					$is_active = ( $cat->term_id === $current_term_id );
				?>
					<li>
						<a class="shop-filters__row<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
							<span class="shop-filters__check" aria-hidden="true"><?php echo $is_active ? '&#x2713;' : ''; ?></span>
							<span class="shop-filters__row-label"><?php echo esc_html( $cat->name ); ?></span>
							<span class="shop-filters__row-count"><?php echo (int) $cat->count; ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<div class="shop-filters__group">
		<div class="shop-filters__label"><?php esc_html_e( 'Availability', 'dankcave' ); ?></div>
		<ul class="shop-filters__list">
			<li>
				<a class="shop-filters__row<?php echo ! empty( $_GET['in_stock'] ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $in_stock_url ); ?>">
					<span class="shop-filters__check" aria-hidden="true"><?php echo ! empty( $_GET['in_stock'] ) ? '&#x2713;' : ''; ?></span>
					<span class="shop-filters__row-label"><?php esc_html_e( 'In stock', 'dankcave' ); ?></span>
				</a>
			</li>
			<li>
				<a class="shop-filters__row<?php echo ! empty( $_GET['on_sale'] ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $on_sale_url ); ?>">
					<span class="shop-filters__check" aria-hidden="true"><?php echo ! empty( $_GET['on_sale'] ) ? '&#x2713;' : ''; ?></span>
					<span class="shop-filters__row-label"><?php esc_html_e( 'On sale', 'dankcave' ); ?></span>
				</a>
			</li>
		</ul>
	</div>
</aside>
