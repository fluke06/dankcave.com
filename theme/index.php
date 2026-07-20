<?php
/**
 * Fallback template — WordPress uses this when a more specific template
 * (front-page.php, home.php, single.php, etc.) isn't available.
 *
 * @package Dankcave
 */

get_header(); ?>

<main class="wrap">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article class="post">
				<h1><?php the_title(); ?></h1>
				<div class="content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing here yet.', 'dankcave' ); ?></p>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
