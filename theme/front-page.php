<?php
/**
 * Front page — home. Composes hero + shop-featured sections.
 *
 * @package Dankcave
 */

get_header(); ?>

<?php get_template_part( 'template-parts/home/hero' ); ?>

<?php
// TODO: subsequent home sections (Pick your poison, Shop by category,
// Editorial band, Popular & trending, New products, Blog row, CTA band, Stats)
// each become their own template-part and get built in follow-up commits.
?>

<?php get_footer(); ?>
