<?php
/**
 * Newsletter capture band — "Vices, handled with care." above the legal bar.
 * Renders the MC4WP shortcode if set in Customizer, else a styled placeholder
 * so the design is preserved even when no shortcode is wired yet.
 *
 * @package Dankcave
 */

$heading  = get_theme_mod( 'dankcave_newsletter_heading', __( 'Vices, handled with care.', 'dankcave' ) );
$subcopy  = get_theme_mod( 'dankcave_newsletter_subcopy', __( "Drops, deals, and the occasional bad influence — in your inbox, 21+ only.", 'dankcave' ) );
$sc       = trim( (string) get_theme_mod( 'dankcave_newsletter_shortcode', '' ) );
?>
<section class="newsletter-band" aria-labelledby="newsletter-band-heading">
	<div class="newsletter-band__copy">
		<h2 class="newsletter-band__heading" id="newsletter-band-heading"><?php echo esc_html( $heading ); ?></h2>
		<p class="newsletter-band__subcopy"><?php echo esc_html( $subcopy ); ?></p>
	</div>
	<div class="newsletter-band__form">
		<?php if ( $sc ) : ?>
			<?php echo do_shortcode( $sc ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<form class="newsletter-band__placeholder" onsubmit="event.preventDefault();" aria-label="<?php esc_attr_e( 'Newsletter signup (placeholder — connect MailChimp shortcode in Customizer)', 'dankcave' ); ?>">
				<input type="email" placeholder="<?php esc_attr_e( 'your@email.com', 'dankcave' ); ?>" aria-label="<?php esc_attr_e( 'Email address', 'dankcave' ); ?>">
				<button type="submit"><?php esc_html_e( 'Join the cave', 'dankcave' ); ?></button>
			</form>
		<?php endif; ?>
	</div>
</section>
