<?php
/**
 * Site footer — newsletter capture band + legal bottom bar.
 *
 * @package Dankcave
 */
?>
	</main><!-- #site-main -->

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

	<!-- Compare tray — appears at bottom when 2+ products are marked for compare -->
	<aside class="dc-compare-tray" id="dc-compare-tray" hidden aria-hidden="true">
		<div class="dc-compare-tray__inner">
			<div class="dc-compare-tray__label">
				<strong><?php esc_html_e( 'Compare', 'dankcave' ); ?></strong>
				<span data-dc-compare-count>0</span>
			</div>
			<div class="dc-compare-tray__thumbs" data-dc-compare-thumbs></div>
			<div class="dc-compare-tray__actions">
				<button type="button" class="dc-compare-tray__clear" data-dc-compare-clear><?php esc_html_e( 'Clear', 'dankcave' ); ?></button>
				<button type="button" class="dc-compare-tray__open" data-dc-compare-open><?php esc_html_e( 'Compare →', 'dankcave' ); ?></button>
			</div>
		</div>
	</aside>

	<!-- Compare modal — side-by-side attribute table for saved products -->
	<div class="dc-compare-modal" id="dc-compare-modal" hidden aria-hidden="true">
		<div class="dc-compare-modal__backdrop" data-dc-compare-close></div>
		<div class="dc-compare-modal__panel" role="dialog" aria-modal="true">
			<header class="dc-compare-modal__head">
				<h2 class="dc-compare-modal__title"><?php esc_html_e( 'Compare products', 'dankcave' ); ?></h2>
				<button type="button" class="dc-compare-modal__close" aria-label="<?php esc_attr_e( 'Close compare', 'dankcave' ); ?>" data-dc-compare-close>
					<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true">
						<line x1="4" y1="4" x2="16" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
						<line x1="16" y1="4" x2="4" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</button>
			</header>
			<div class="dc-compare-modal__body" data-dc-compare-body>
				<div class="dc-quickview__loading"><?php esc_html_e( 'Loading…', 'dankcave' ); ?></div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php wp_footer(); ?>
</body>
</html>
