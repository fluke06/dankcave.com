<?php
/**
 * Template Name: Landing (full-bleed)
 *
 * Renders page content edge-to-edge without the article-card wrapper page.php
 * uses for legal pages. Pick this for pages composed from Dankcave block
 * patterns (About, Contact, marketing landing pages).
 *
 * Adds a breadcrumb strip above the content so navigation stays discoverable.
 *
 * @package Dankcave
 */

get_header();

while ( have_posts() ) : the_post();
	$page_id   = get_the_ID();
	$parent_id = wp_get_post_parent_id( $page_id );

	// Long-form legal pages get a scroll-progress bar. Detect them by looking
	// at the post content for any known legal pattern reference or the shared
	// `.dc-legal` wrapper class (in case the pattern's been detached).
	$content_raw   = get_post_field( 'post_content', $page_id );
	$is_legal_page = (
		false !== strpos( $content_raw, 'dankcave/page-privacy' ) ||
		false !== strpos( $content_raw, 'dankcave/page-terms' ) ||
		false !== strpos( $content_raw, 'dankcave/page-shipping' ) ||
		false !== strpos( $content_raw, 'dankcave/page-returns' ) ||
		false !== strpos( $content_raw, 'dc-legal' )
	);
	?>
	<nav class="dc-landing-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'dankcave' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'dankcave' ); ?></a>
		<?php if ( $parent_id ) : ?>
			<span class="dc-landing-crumbs__sep" aria-hidden="true">/</span>
			<a href="<?php echo esc_url( get_permalink( $parent_id ) ); ?>"><?php echo esc_html( get_the_title( $parent_id ) ); ?></a>
		<?php endif; ?>
		<span class="dc-landing-crumbs__sep" aria-hidden="true">/</span>
		<span class="dc-landing-crumbs__current" aria-current="page"><?php the_title(); ?></span>
	</nav>
	<?php if ( $is_legal_page ) : ?>
		<div class="dc-landing-progress" aria-hidden="true"><div class="dc-landing-progress__bar"></div></div>
	<?php endif; ?>
	<?php
	the_content();
endwhile;

get_footer();
