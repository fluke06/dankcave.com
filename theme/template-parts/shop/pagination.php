<?php
/**
 * Shop pagination — numbered pills with a Next link on the right.
 *
 * @package Dankcave
 */

$total = isset( $GLOBALS['wp_query']->max_num_pages ) ? (int) $GLOBALS['wp_query']->max_num_pages : 1;
if ( $total < 2 ) { return; }

$current = max( 1, get_query_var( 'paged' ) );

$links = paginate_links( array(
	'total'     => $total,
	'current'   => $current,
	'mid_size'  => 1,
	'end_size'  => 1,
	'prev_next' => true,
	'prev_text' => '&larr; ' . __( 'Prev', 'dankcave' ),
	'next_text' => __( 'Next', 'dankcave' ) . ' &rarr;',
	'type'      => 'array',
) );

if ( empty( $links ) ) { return; }
?>
<nav class="shop-pagination" aria-label="<?php esc_attr_e( 'Pagination', 'dankcave' ); ?>">
	<?php foreach ( $links as $link ) : ?>
		<span class="shop-pagination__item"><?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	<?php endforeach; ?>
</nav>
