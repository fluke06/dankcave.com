<?php
/**
 * Site footer — newsletter capture band + legal bottom bar.
 *
 * @package Dankcave
 */
?>
	</div><!-- .site-content -->

	<?php get_template_part( 'template-parts/footer/newsletter-band' ); ?>
	<?php get_template_part( 'template-parts/footer/legal-bar' ); ?>

	<?php if ( function_exists( 'WC' ) ) { get_template_part( 'template-parts/cart/drawer' ); } ?>

	<?php if ( function_exists( 'WC' ) ) : ?>
	<div class="dc-quickview" id="dc-quickview" hidden aria-hidden="true">
		<div class="dc-quickview__backdrop" data-dc-quickview-close></div>
		<div class="dc-quickview__panel" role="dialog" aria-modal="true" aria-labelledby="dc-quickview-title">
			<button type="button" class="dc-quickview__close" aria-label="<?php esc_attr_e( 'Close quick view', 'dankcave' ); ?>" data-dc-quickview-close>
				<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true">
					<line x1="4" y1="4" x2="16" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					<line x1="16" y1="4" x2="4" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				</svg>
			</button>
			<div class="dc-quickview__body" data-dc-quickview-body>
				<div class="dc-quickview__loading"><?php esc_html_e( 'Loading…', 'dankcave' ); ?></div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php wp_footer(); ?>
</body>
</html>
