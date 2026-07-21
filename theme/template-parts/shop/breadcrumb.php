<?php
/**
 * Breadcrumb trail. Uses WooCommerce's own breadcrumb function when available,
 * wrapped in our own markup so we can style it.
 *
 * @package Dankcave
 */
?>
<nav class="shop-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'dankcave' ); ?>">
	<?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
		<?php woocommerce_breadcrumb( array(
			'delimiter'   => '<span class="shop-breadcrumb__sep">/</span>',
			'wrap_before' => '<div class="shop-breadcrumb__list">',
			'wrap_after'  => '</div>',
			'before'      => '',
			'after'       => '',
			'home'        => __( 'Home', 'dankcave' ),
		) ); ?>
	<?php endif; ?>
</nav>
