<?php
/**
 * Pattern: Commitment split — narrative text on the left with a 2×2 category tile grid on the right.
 * Tile images are pulled from the top-rated product in each mapped category.
 *
 * Wrapped in an IIFE so its loop variables don't leak into the calling scope
 * (register_block_pattern uses `require`, which runs in the caller's scope).
 *
 * @package Dankcave
 */

return ( function () {
	$tiles = array(
		array( 'label' => 'Bongs',    'cat' => 'bong',                    'tone' => 'sand'  ),
		array( 'label' => 'Vapes',    'cat' => 'vaporizers',              'tone' => 'peach' ),
		array( 'label' => 'Dab Rigs', 'cat' => 'best-dab-rigs',           'tone' => 'pearl' ),
		array( 'label' => 'Pipes',    'cat' => 'glass-pipes-hand-pipes',  'tone' => 'wheat' ),
	);

	$tile_html = '';
	foreach ( $tiles as $tile ) {
		$term     = get_term_by( 'slug', $tile['cat'], 'product_cat' );
		$img_url  = wc_placeholder_img_src( 'medium' );
		$link_url = wc_get_page_permalink( 'shop' );
		if ( $term ) {
			$link_url = get_term_link( $term );
			$q = get_posts( array(
				'post_type'      => 'product',
				'posts_per_page' => 1,
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
			if ( ! empty( $q ) ) {
				$thumb = get_the_post_thumbnail_url( $q[0]->ID, 'medium' );
				if ( $thumb ) { $img_url = $thumb; }
			}
		}
		$tile_html .= sprintf(
			'<a class="pattern-tile pattern-tile--%s" href="%s"><img src="%s" alt="" loading="lazy"><span class="pattern-tile__label">%s</span></a>',
			esc_attr( $tile['tone'] ),
			esc_url( $link_url ),
			esc_url( $img_url ),
			esc_html( $tile['label'] )
		);
	}

	return '
<!-- wp:html -->
<section class="pattern-commitment alignfull">
	<div class="pattern-commitment__inner">
		<div class="pattern-commitment__copy">
			<p class="pattern-eyebrow pattern-eyebrow--gold">OUR COMMITMENT</p>
			<h2 class="pattern-h2">Gear we&#8217;d keep on our own shelf.</h2>
			<p class="pattern-body">Our mission is to supply the best bongs, dab rigs, vaporizers, rolling papers, rolling trays and a comprehensive range of smoking accessories.</p>
			<p class="pattern-body">We understand the unique preferences of smokers, ensuring every product in our collection meets the highest standards of quality and functionality.</p>
		</div>
		<div class="pattern-commitment__tiles">' . $tile_html . '</div>
	</div>
</section>
<!-- /wp:html -->
';
} )();
