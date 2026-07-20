<?php
/**
 * Site footer.
 * TODO: Build the actual footer markup per design/*.dc.html (multi-column
 * link menus + tagline + social + newsletter capture in a black band above).
 *
 * @package Dankcave
 */
?>
	</div><!-- .site-content -->

	<footer class="site-footer">
		<div class="wrap">
			<p class="site-copyright">
				&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			</p>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
