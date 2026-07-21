<?php
/**
 * Single blog post. Mirrors design/Dankcave - Blog Post.dc.html:
 * breadcrumb, meta/author header, hero image, article body,
 * tags row, and a dark "Keep reading" band.
 *
 * @package Dankcave
 */

get_header();

while ( have_posts() ) : the_post();
	$post_id     = get_the_ID();
	$cats        = get_the_category();
	$primary_cat = $cats ? $cats[0] : null;
	$reading     = function_exists( 'dankcave_reading_time' ) ? dankcave_reading_time( $post_id ) : '';
	$date        = get_the_date( 'M j, Y' );
	$eyebrow     = strtoupper( trim(
		( $primary_cat ? $primary_cat->name . ' · ' : '' ) .
		$date .
		( $reading ? ' · ' . $reading : '' )
	) );

	$author_id   = get_the_author_meta( 'ID' );
	$author_name = get_the_author();
	$author_bio  = get_the_author_meta( 'description' );
	$author_initials = strtoupper( substr( $author_name, 0, 2 ) );

	$thumb_id  = get_post_thumbnail_id( $post_id );
	$hero_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';

	$wells = array( '#f3e3d0', '#e6ede2', '#efe7dd', '#f2ede8', '#f1e6d6' );
	$well  = $wells[ abs( crc32( (string) $post_id ) ) % count( $wells ) ];

	$tags = get_the_tags();
	?>

	<article class="dc-post">
		<nav class="dc-post__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'dankcave' ); ?>">
			<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'Journal', 'dankcave' ); ?></a>
			<?php if ( $primary_cat ) : ?>
				<span class="dc-post__crumbs-sep">/</span>
				<span class="dc-post__crumbs-current"><?php echo esc_html( $primary_cat->name ); ?></span>
			<?php endif; ?>
		</nav>

		<header class="dc-post__header">
			<div class="dc-post__eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<h1 class="dc-post__title"><?php the_title(); ?></h1>
			<div class="dc-post__author">
				<div class="dc-post__author-avatar" aria-hidden="true"><?php echo esc_html( $author_initials ); ?></div>
				<div class="dc-post__author-info">
					<div class="dc-post__author-name"><?php echo esc_html( $author_name ); ?></div>
					<?php if ( $author_bio ) : ?>
						<div class="dc-post__author-bio"><?php echo esc_html( $author_bio ); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</header>

		<?php if ( $hero_url ) : ?>
			<div class="dc-post__hero">
				<div class="dc-post__hero-well" style="background: <?php echo esc_attr( $well ); ?>;">
					<img src="<?php echo esc_url( $hero_url ); ?>" alt="<?php the_title_attribute(); ?>">
				</div>
			</div>
		<?php endif; ?>

		<div class="dc-post__body wp-content">
			<?php the_content(); ?>

			<?php if ( $tags ) : ?>
				<div class="dc-post__tags">
					<?php foreach ( $tags as $tag ) : ?>
						<a class="dc-post__tag" href="<?php echo esc_url( get_tag_link( $tag ) ); ?>">#<?php echo esc_html( $tag->slug ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</article>

	<?php
	// Related posts (same category, exclude current, newest 3)
	$related_query_args = array(
		'post_type'      => 'post',
		'posts_per_page' => 3,
		'post__not_in'   => array( $post_id ),
		'orderby'        => 'date',
		'order'          => 'DESC',
	);
	if ( $primary_cat ) {
		$related_query_args['category__in'] = array( $primary_cat->term_id );
	}
	$related = new WP_Query( $related_query_args );
	if ( ! $related->have_posts() ) {
		wp_reset_postdata();
		$related = new WP_Query( array(
			'post_type'      => 'post',
			'posts_per_page' => 3,
			'post__not_in'   => array( $post_id ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );
	}
	if ( $related->have_posts() ) : ?>
		<section class="dc-post-related">
			<div class="dc-post-related__inner">
				<h2 class="dc-post-related__title"><?php esc_html_e( 'Keep reading', 'dankcave' ); ?></h2>
				<div class="dc-post-related__grid">
					<?php while ( $related->have_posts() ) : $related->the_post();
						get_template_part( 'template-parts/blog/card', null, array( 'post' => get_post() ) );
					endwhile; ?>
				</div>
			</div>
		</section>
	<?php endif; wp_reset_postdata(); ?>

<?php endwhile;

get_footer();
