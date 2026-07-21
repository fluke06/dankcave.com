<?php
/**
 * Blog index — /blog/ or whichever page is set as posts page.
 * Layout mirrors design/Dankcave - Blog.dc.html.
 *
 * @package Dankcave
 */

get_header();

$heading = get_theme_mod( 'dankcave_blog_heading', __( 'The Journal', 'dankcave' ) );
$intro   = get_theme_mod( 'dankcave_blog_intro',   __( 'Guides, gear talk and cannabis culture — written by the crew, no gatekeeping.', 'dankcave' ) );

$categories = get_terms( array(
	'taxonomy'   => 'category',
	'hide_empty' => true,
	'orderby'    => 'count',
	'order'      => 'DESC',
	'number'     => 8,
) );

$queried_cat_id = is_category() ? get_queried_object_id() : 0;

$paged     = max( 1, get_query_var( 'paged' ) ?: get_query_var( 'page' ) );
$is_page_1 = ( 1 === $paged );

$featured  = null;
if ( $is_page_1 && ! is_category() && ! is_tag() && have_posts() ) {
	the_post();
	$featured = get_post();
}
?>

<div class="dc-blog">

	<header class="dc-blog__header">
		<h1 class="dc-blog__title"><?php echo esc_html( $heading ); ?></h1>
		<?php if ( $intro ) : ?>
			<p class="dc-blog__intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
		<nav class="dc-blog__chips" aria-label="<?php esc_attr_e( 'Blog categories', 'dankcave' ); ?>">
			<a class="dc-blog__chip <?php echo ( ! is_category() ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'All', 'dankcave' ); ?></a>
			<?php foreach ( $categories as $cat ) : ?>
				<a class="dc-blog__chip <?php echo ( $cat->term_id === $queried_cat_id ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

	<?php if ( $featured ) : ?>
		<section class="dc-blog__featured-wrap">
			<?php
			$permalink = get_permalink( $featured );
			$thumb_id  = get_post_thumbnail_id( $featured );
			$image     = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
			$cats      = get_the_category( $featured->ID );
			$cat_name  = $cats ? strtoupper( $cats[0]->name ) : '';
			$date      = get_the_date( 'M j', $featured );
			$reading   = function_exists( 'dankcave_reading_time' ) ? dankcave_reading_time( $featured->ID ) : '';
			$excerpt   = get_the_excerpt( $featured );
			$wells     = array( '#f3e3d0', '#e6ede2', '#efe7dd', '#f2ede8', '#f1e6d6' );
			$well      = $wells[ abs( crc32( (string) $featured->ID ) ) % count( $wells ) ];
			?>
			<a class="dc-blog-featured" href="<?php echo esc_url( $permalink ); ?>">
				<div class="dc-blog-featured__media" style="background: <?php echo esc_attr( $well ); ?>;">
					<?php if ( $image ) : ?>
						<img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy">
					<?php endif; ?>
				</div>
				<div class="dc-blog-featured__body">
					<span class="dc-blog-featured__flag"><?php esc_html_e( 'FEATURED', 'dankcave' ); ?></span>
					<div class="dc-blog-featured__eyebrow">
						<?php echo esc_html( trim( ( $cat_name ? $cat_name . ' · ' : '' ) . $date . ( $reading ? ' · ' . $reading : '' ) ) ); ?>
					</div>
					<h2 class="dc-blog-featured__title"><?php echo esc_html( get_the_title( $featured ) ); ?></h2>
					<?php if ( $excerpt ) : ?>
						<div class="dc-blog-featured__excerpt"><?php echo esc_html( wp_trim_words( $excerpt, 30 ) ); ?></div>
					<?php endif; ?>
					<span class="dc-blog-featured__cta"><?php esc_html_e( 'Read article →', 'dankcave' ); ?></span>
				</div>
			</a>
		</section>
	<?php endif; ?>

	<section class="dc-blog__grid-wrap">
		<?php if ( have_posts() ) : ?>
			<div class="dc-blog__grid">
				<?php while ( have_posts() ) : the_post();
					get_template_part( 'template-parts/blog/card', null, array(
						'post'       => get_post(),
						'show_excerpt' => true,
					) );
				endwhile; ?>
			</div>

			<?php
			$pagination = paginate_links( array(
				'total'     => $GLOBALS['wp_query']->max_num_pages,
				'current'   => max( 1, get_query_var( 'paged' ) ),
				'type'      => 'array',
				'prev_text' => __( '← Prev', 'dankcave' ),
				'next_text' => __( 'Next →', 'dankcave' ),
			) );
			?>
			<?php if ( $pagination ) : ?>
				<div class="dc-blog__pagination">
					<?php foreach ( $pagination as $link ) : ?>
						<span class="dc-blog__pagination-item"><?php echo wp_kses_post( $link ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<div class="dc-blog__empty">
				<?php esc_html_e( 'No articles here yet. Come back soon.', 'dankcave' ); ?>
			</div>
		<?php endif; ?>
	</section>
</div>

<?php get_footer();
