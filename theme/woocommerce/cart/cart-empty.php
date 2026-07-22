<?php
/**
 * Empty cart page.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_cart_is_empty' );
?>
<div class="dc-cart dc-cart--empty">
	<div class="dc-cart-empty">
		<h1 class="dc-cart-empty__title"><?php esc_html_e( 'Your bag is empty', 'dankcave' ); ?></h1>
		<p class="dc-cart-empty__blurb"><?php esc_html_e( 'You have not added anything yet. Have a look around — there is good gear inside.', 'dankcave' ); ?></p>
		<a class="dc-cart-empty__cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Return to shop', 'dankcave' ); ?></a>
	</div>
</div>
