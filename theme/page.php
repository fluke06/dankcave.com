<?php
/**
 * Default static page. Two-column layout when the page's parent (or the page
 * itself) has sibling pages under a "legal" grouping — we surface a policy
 * sidebar in that case. Otherwise renders as a single centered article card.
 *
 * @package Dankcave
 */

get_header();

while ( have_posts() ) : the_post();
	$page_id     = get_the_ID();
	$parent_id   = wp_get_post_parent_id( $page_id );
	$siblings    = array();

	// If page has a parent, use siblings; otherwise treat as top-level with no sidebar.
	if ( $parent_id ) {
		$siblings = get_pages( array(
			'parent'    => $parent_id,
			'sort_column' => 'menu_order,post_title',
		) );
	}

	// Also allow marking a page as a "legal" hub via slug/title convention.
	$is_policy_hub = ! empty( $siblings ) || in_array( get_post_field( 'post_name', $parent_id ), array( 'legal', 'policies', 'help' ), true );

	$updated = get_the_modified_date( 'F j, Y' );
	?>

	<article class="dc-page">
		<header class="dc-page__header">
			<nav class="dc-page__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'dankcave' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'dankcave' ); ?></a>
				<?php if ( $parent_id ) : ?>
					<span class="dc-page__crumbs-sep">/</span>
					<a href="<?php echo esc_url( get_permalink( $parent_id ) ); ?>"><?php echo esc_html( get_the_title( $parent_id ) ); ?></a>
				<?php endif; ?>
				<span class="dc-page__crumbs-sep">/</span>
				<span class="dc-page__crumbs-current"><?php the_title(); ?></span>
			</nav>
			<h1 class="dc-page__title"><?php the_title(); ?></h1>
			<?php if ( $updated ) : ?>
				<div class="dc-page__updated"><?php echo esc_html( sprintf( __( 'Last updated: %s', 'dankcave' ), $updated ) ); ?></div>
			<?php endif; ?>
		</header>

		<div class="dc-page__layout <?php echo $is_policy_hub ? 'dc-page__layout--with-nav' : 'dc-page__layout--single'; ?>">

			<?php if ( $is_policy_hub && ! empty( $siblings ) ) : ?>
				<aside class="dc-page-nav" aria-label="<?php esc_attr_e( 'Policy pages', 'dankcave' ); ?>">
					<div class="dc-page-nav__eyebrow"><?php echo esc_html( strtoupper( get_the_title( $parent_id ) ) ); ?></div>
					<div class="dc-page-nav__list">
						<?php foreach ( $siblings as $sib ) :
							$is_current = ( $sib->ID === $page_id );
						?>
							<a class="dc-page-nav__link<?php echo $is_current ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_permalink( $sib ) ); ?>"><?php echo esc_html( $sib->post_title ); ?></a>
						<?php endforeach; ?>
					</div>

					<div class="dc-page-nav__help">
						<div class="dc-page-nav__help-title"><?php esc_html_e( 'Questions?', 'dankcave' ); ?></div>
						<p class="dc-page-nav__help-body"><?php esc_html_e( 'Reach our team any time — real humans in Tracy, CA.', 'dankcave' ); ?></p>
						<a class="dc-page-nav__help-cta" href="mailto:<?php echo esc_attr( get_theme_mod( 'dankcave_support_email', 'support@dankcave.com' ) ); ?>"><?php echo esc_html( get_theme_mod( 'dankcave_support_email', 'support@dankcave.com' ) ); ?> →</a>
					</div>
				</aside>
			<?php endif; ?>

			<div class="dc-page__card">
				<div class="wp-content">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
	</article>

<?php endwhile;

get_footer();
