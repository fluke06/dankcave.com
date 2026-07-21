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

<?php
// TODO: subsequent home sections (Popular & trending, New products, Blog row,
// CTA band, Stats) get built in follow-up commits.
?>

<?php get_footer(); ?>
