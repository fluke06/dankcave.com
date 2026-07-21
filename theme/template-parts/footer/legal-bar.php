<?php
/**
 * Legal bar — bottom-most row with copyright + shipping/returns/privacy/terms.
 *
 * @package Dankcave
 */

$copy = get_theme_mod(
	'dankcave_footer_copyright',
	sprintf(
		/* translators: %s: Site name */
		__( '© %1$s %2$s · Adults 21+ · Tracy, CA', 'dankcave' ),
		date( 'Y' ),
		get_bloginfo( 'name' )
	)
);
?>
<footer class="legal-bar" role="contentinfo">
	<div class="legal-bar__inner">
		<span class="legal-bar__copy"><?php echo esc_html( $copy ); ?></span>
		<nav class="legal-bar__nav" aria-label="<?php esc_attr_e( 'Legal', 'dankcave' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer-legal',
				'container'      => false,
				'menu_class'     => 'legal-bar__list',
				'fallback_cb'    => function () {
					echo '<ul class="legal-bar__list">';
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/shipping/' ) ),       esc_html__( 'Shipping', 'dankcave' ) );
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/returns/' ) ),        esc_html__( 'Returns', 'dankcave' ) );
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/privacy-policy/' ) ), esc_html__( 'Privacy', 'dankcave' ) );
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/terms/' ) ),          esc_html__( 'Terms', 'dankcave' ) );
					echo '</ul>';
				},
				'depth'          => 1,
			) );
			?>
		</nav>
	</div>
</footer>
