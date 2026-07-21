<?php
/**
 * Right-side sliding cart drawer. Rendered once at the end of the body so it
 * overlays every page. Contents update via the woocommerce_add_to_cart_fragments
 * filter (see inc/woocommerce.php) whenever an AJAX add-to-cart fires.
 *
 * @package Dankcave
 */

if ( ! function_exists( 'WC' ) ) { return; }
?>
<aside class="dc-cart-drawer" id="dc-cart-drawer" hidden aria-hidden="true" aria-labelledby="dc-cart-drawer-title">
	<div class="dc-cart-drawer__backdrop" data-dc-drawer-close></div>

	<div class="dc-cart-drawer__panel" role="dialog" aria-modal="true">
		<header class="dc-cart-drawer__head">
			<h2 id="dc-cart-drawer-title" class="dc-cart-drawer__title">
				<?php esc_html_e( 'Your bag', 'dankcave' ); ?>
				<span class="dc-cart-drawer__count" data-dc-drawer-count><?php echo (int) WC()->cart->get_cart_contents_count(); ?></span>
			</h2>
			<button type="button" class="dc-cart-drawer__close" aria-label="<?php esc_attr_e( 'Close cart', 'dankcave' ); ?>" data-dc-drawer-close>
				<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true">
					<line x1="4" y1="4" x2="16" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					<line x1="16" y1="4" x2="4" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				</svg>
			</button>
		</header>

		<div class="dc-cart-drawer__body">
			<div class="dc-cart-drawer__contents" data-dc-drawer-contents>
				<?php dankcave_render_cart_drawer_contents(); ?>
			</div>
		</div>

		<footer class="dc-cart-drawer__foot" data-dc-drawer-foot>
			<?php dankcave_render_cart_drawer_footer(); ?>
		</footer>
	</div>
</aside>
