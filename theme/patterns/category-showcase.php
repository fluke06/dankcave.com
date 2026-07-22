<?php
/**
 * Pattern: Category showcase — 3-col × 2-row category grid + bulleted product callouts.
 * Tile images are pulled from the top-rated products in each mapped category.
 *
 * Wrapped in an IIFE so its loop variables don't leak into the calling scope
 * (register_block_pattern uses `require`, which runs in the caller's scope).
 *
 * @package Dankcave
 */

return ( function () {
	$tiles = array(
		array( 'label' => 'Bongs',       'cat' => 'bong',                    'tone' => 'sand'  ),
		array( 'label' => 'Dab Rigs',    'cat' => 'best-dab-rigs',           'tone' => 'wheat' ),
		array( 'label' => 'Vaporizers',  'cat' => 'vaporizers',              'tone' => 'peach' ),
		array( 'label' => 'Rolling',     'cat' => 'accessories',             'tone' => 'pearl' ),
		array( 'label' => 'Pipes',       'cat' => 'glass-pipes-hand-pipes',  'tone' => 'wheat' ),
		array( 'label' => 'Skull Glass', 'cat' => 'bong',                    'tone' => 'sand'  ),
	);

	// Pre-fetch top-rated products per unique category so duplicate tiles
	// (e.g. Bongs / Skull Glass both use "bong") show different images.
	$cat_images = array();
	foreach ( array_unique( wp_list_pluck( $tiles, 'cat' ) ) as $cat_slug ) {
		$term = get_term_by( 'slug', $cat_slug, 'product_cat' );
		$cat_images[ $cat_slug ] = array();
		if ( ! $term ) { continue; }
		$q = get_posts( array(
			'post_type'      => 'product',
			'posts_per_page' => 3,
			'post_status'    => 'publish',
			'orderby'        => 'meta_value_num',
			'meta_key'       => '_wc_average_rating',
			'order'          => 'DESC',
			'tax_query'      => array( array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			) ),
		) );
		foreach ( $q as $product_post ) {
			$thumb = get_the_post_thumbnail_url( $product_post->ID, 'medium' );
			if ( $thumb ) { $cat_images[ $cat_slug ][] = $thumb; }
		}
	}

	$cat_seen  = array();
	$tile_html = '';
	foreach ( $tiles as $tile ) {
		$term     = get_term_by( 'slug', $tile['cat'], 'product_cat' );
		$link_url = $term ? get_term_link( $term ) : wc_get_page_permalink( 'shop' );
		$pool     = $cat_images[ $tile['cat'] ] ?? array();
		$idx      = $cat_seen[ $tile['cat'] ] ?? 0;
		$cat_seen[ $tile['cat'] ] = $idx + 1;
		$img_url  = ! empty( $pool ) ? $pool[ $idx % count( $pool ) ] : wc_placeholder_img_src( 'medium' );
		$tile_html .= sprintf(
			'<a class="pattern-tile pattern-tile--%s pattern-showcase__cell" href="%s"><img src="%s" alt="" loading="lazy"><span class="pattern-tile__label">%s</span></a>',
			esc_attr( $tile['tone'] ),
			esc_url( $link_url ),
			esc_url( $img_url ),
			esc_html( $tile['label'] )
		);
	}

	return '
<!-- wp:group {"className":"pattern-showcase","align":"full"} -->
<section class="wp-block-group alignfull pattern-showcase">
	<div class="pattern-showcase__inner">
		<header class="pattern-showcase__head">
			<div>
				<p class="pattern-eyebrow pattern-eyebrow--gold">OUR PRODUCTS</p>
				<h2 class="pattern-h2">Everything for your setup</h2>
			</div>
			<a class="pattern-showcase__more" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Shop all &rarr;</a>
		</header>
		<div class="pattern-showcase__grid">' . $tile_html . '</div>
		<div class="pattern-showcase__callouts">
			<p class="pattern-showcase__lead">Explore our vast collection and find the perfect addition to your setup. Whether you&#8217;re a seasoned smoker or new to the scene, our inventory includes:</p>
			<div class="pattern-showcase__list">
				<div class="pattern-showcase__item"><span class="pattern-showcase__dot"></span><div><strong>Bongs</strong> &mdash; classic glass bongs, silicone bongs, and more.</div></div>
				<div class="pattern-showcase__item"><span class="pattern-showcase__dot"></span><div><strong>Dab Rigs</strong> &mdash; from beginner-friendly rigs to advanced setups.</div></div>
				<div class="pattern-showcase__item"><span class="pattern-showcase__dot"></span><div><strong>Vaporizers</strong> &mdash; portable and desktop vaporizers for all your needs.</div></div>
				<div class="pattern-showcase__item"><span class="pattern-showcase__dot"></span><div><strong>Rolling Accessories</strong> &mdash; papers, trays, and other essentials.</div></div>
				<div class="pattern-showcase__item pattern-showcase__item--full"><span class="pattern-showcase__dot"></span><div><strong>Smoking Pipes</strong> &mdash; glass pipes, water pipes, hand pipes, and more.</div></div>
			</div>
		</div>
	</div>
</section>
<!-- /wp:group -->
';
} )();
