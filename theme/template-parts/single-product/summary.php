<?php
/**
 * Product summary column: eyebrow, title, rating, price, short description,
 * add-to-cart form, benefits, and description/specs/shipping accordions.
 *
 * @package Dankcave
 */

$args    = wp_parse_args( $args ?? array(), array( 'product' => null ) );
$product = $args['product'] ?: ( $GLOBALS['product'] ?? null );
if ( ! $product ) { return; }

$terms       = get_the_terms( $product->get_id(), 'product_cat' );
$eyebrow_cat = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

$avg   = (float) $product->get_average_rating();
$count = (int) $product->get_review_count();

$short = $product->get_short_description();
$long  = apply_filters( 'the_content', $product->get_description() );

// Visible attributes → Specs rows.
$specs = array();
foreach ( $product->get_attributes() as $attribute ) {
	if ( ! $attribute->get_visible() ) { continue; }
	$name  = wc_attribute_label( $attribute->get_name(), $product );
	if ( $attribute->is_taxonomy() ) {
		$values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
	} else {
		$values = $attribute->get_options();
	}
	$value_str = implode( ', ', array_filter( (array) $values ) );
	if ( $value_str ) {
		$specs[] = array( $name, $value_str );
	}
}

$benefits = array(
	__( 'Free discreet shipping', 'dankcave' ),
	__( '1-year warranty', 'dankcave' ),
	__( '30-day returns', 'dankcave' ),
);

$shipping_copy = get_theme_mod(
	'dankcave_pdp_shipping_returns',
	__( 'Ships free on orders over $50 in plain, unmarked packaging. 30-day returns; in-transit breakage covered. Same-day dispatch on orders before 2pm PT.', 'dankcave' )
);
?>
<div class="pdp-summary">
	<?php if ( $eyebrow_cat ) : ?>
		<div class="pdp-summary__eyebrow"><?php echo esc_html( strtoupper( $eyebrow_cat ) ); ?> · DANKCAVE</div>
	<?php endif; ?>

	<h1 class="pdp-summary__title"><?php the_title(); ?></h1>

	<?php if ( $count > 0 ) : ?>
		<div class="pdp-summary__rating" aria-label="<?php echo esc_attr( sprintf( __( '%1$s out of 5 stars, %2$d reviews', 'dankcave' ), $avg, $count ) ); ?>">
			<?php
			$full = (int) round( $avg );
			$full = max( 0, min( 5, $full ) );
			?>
			<span class="pdp-summary__stars" aria-hidden="true"><?php echo esc_html( str_repeat( '★', $full ) . str_repeat( '☆', 5 - $full ) ); ?></span>
			<span class="pdp-summary__reviews">
				<?php echo esc_html( number_format_i18n( $avg, 1 ) ); ?> ·
				<?php echo esc_html( sprintf( _n( '%d review', '%d reviews', $count, 'dankcave' ), $count ) ); ?>
			</span>
		</div>
	<?php endif; ?>

	<div class="pdp-summary__price">
		<?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<?php if ( $short ) : ?>
		<div class="pdp-summary__short">
			<?php echo apply_filters( 'woocommerce_short_description', $short ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>

	<div class="pdp-summary__cart">
		<?php woocommerce_template_single_add_to_cart(); ?>
	</div>

	<ul class="pdp-summary__benefits">
		<?php foreach ( $benefits as $item ) : ?>
			<li><?php echo esc_html( $item ); ?></li>
		<?php endforeach; ?>
	</ul>

	<div class="pdp-accordions">
		<?php if ( $long ) : ?>
			<details class="pdp-accordion" open>
				<summary class="pdp-accordion__head">
					<span><?php esc_html_e( 'Description', 'dankcave' ); ?></span>
					<span class="pdp-accordion__icon" aria-hidden="true"></span>
				</summary>
				<div class="pdp-accordion__body">
					<?php echo $long; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</details>
		<?php endif; ?>

		<?php if ( ! empty( $specs ) ) : ?>
			<details class="pdp-accordion">
				<summary class="pdp-accordion__head">
					<span><?php esc_html_e( 'Specs', 'dankcave' ); ?></span>
					<span class="pdp-accordion__icon" aria-hidden="true"></span>
				</summary>
				<div class="pdp-accordion__body">
					<table class="pdp-accordion__specs">
						<tbody>
							<?php foreach ( $specs as $row ) : ?>
								<tr>
									<th scope="row"><?php echo esc_html( $row[0] ); ?></th>
									<td><?php echo esc_html( $row[1] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</details>
		<?php endif; ?>

		<details class="pdp-accordion">
			<summary class="pdp-accordion__head">
				<span><?php esc_html_e( 'Shipping and returns', 'dankcave' ); ?></span>
				<span class="pdp-accordion__icon" aria-hidden="true"></span>
			</summary>
			<div class="pdp-accordion__body">
				<p><?php echo esc_html( $shipping_copy ); ?></p>
			</div>
		</details>
	</div>
</div>
