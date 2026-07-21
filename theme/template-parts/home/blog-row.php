<?php
/**
 * Home section — "From the blog". 3-up recent blog card grid.
 *
 * @package Dankcave
 */

$heading    = get_theme_mod( 'dankcave_blog_row_heading',    'From the blog' );
$link_label = get_theme_mod( 'dankcave_blog_row_link_label', 'All posts →' );
$link_url   = get_theme_mod( 'dankcave_blog_row_link_url',   home_url( '/blog/' ) );

$posts = get_posts( array(
	'post_type'      => 'post',
	'posts_per_page' => 3,
	'post_status'    => 'publish',
) );

$demo_posts = array(
	array( 'title' => 'Delta-8 vs. Delta-10 — the major difference', 'date_label' => 'Oct 03 · 6 min read', 'image' => '' ),
	array( 'title' => 'The best herbs to vaporize',                  'date_label' => 'Aug 03 · 6 min read', 'image' => '' ),
	array( 'title' => "Bongs 101 — a beginner's guide",              'date_label' => 'May 31 · 7 min read', 'image' => '' ),
);
?>
<section class="section section--cream home-blog-row">
	<div class="wrap">
		<div class="section-head">
			<h2 class="section-head__title"><?php echo esc_html( $heading ); ?></h2>
			<a class="section-head__link" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?></a>
		</div>
		<div class="blog-row">
			<?php if ( ! empty( $posts ) ) : ?>
				<?php foreach ( $posts as $post ) : ?>
					<?php get_template_part( 'template-parts/blog/card', null, array( 'post' => $post ) ); ?>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach ( $demo_posts as $demo ) : ?>
					<?php get_template_part( 'template-parts/blog/card', null, array( 'demo' => $demo ) ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
