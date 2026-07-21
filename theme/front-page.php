<?php
/**
 * Front page — home. Composes hero + shop-featured sections.
 *
 * @package Dankcave
 */

get_header(); ?>

<?php get_template_part( 'template-parts/home/hero' ); ?>
<?php get_template_part( 'template-parts/home/pick-your-poison' ); ?>
<?php get_template_part( 'template-parts/home/shop-by-category' ); ?>
<?php get_template_part( 'template-parts/home/editorial-band' ); ?>
<?php get_template_part( 'template-parts/home/popular-trending' ); ?>
<?php get_template_part( 'template-parts/home/new-products' ); ?>

<?php
// TODO: subsequent home sections (Blog row, CTA band, Stats) get built in
// follow-up commits.
?>

<?php get_footer(); ?>
