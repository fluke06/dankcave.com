<?php
/**
 * Home section — "No fuss. Just good gear." dark trust band with 4-column
 * stats grid underneath. Everything editable via Customizer.
 *
 * @package Dankcave
 */

$heading_1 = get_theme_mod( 'dankcave_trust_heading_1', 'No fuss.' );
$heading_2 = get_theme_mod( 'dankcave_trust_heading_2', 'Just good' );
$heading_3 = get_theme_mod( 'dankcave_trust_heading_accent', 'gear' );
$body      = get_theme_mod( 'dankcave_trust_body',      'Twenty years curating glass, vapes and rolling gear — priced fair, shipped discreet, backed by humans who actually pick up the phone.' );

$stats = array(
	array(
		'value' => get_theme_mod( 'dankcave_trust_stat_1_value', '$50+' ),
		'body'  => get_theme_mod( 'dankcave_trust_stat_1_body',  'Free, discreet shipping. Plain box, every time.' ),
	),
	array(
		'value' => get_theme_mod( 'dankcave_trust_stat_2_value', '100%' ),
		'body'  => get_theme_mod( 'dankcave_trust_stat_2_body',  'Lab-tested vapor path on every device we stock.' ),
	),
	array(
		'value' => get_theme_mod( 'dankcave_trust_stat_3_value', '30 day' ),
		'body'  => get_theme_mod( 'dankcave_trust_stat_3_body',  'No-drama returns. Breakage in transit is on us.' ),
	),
	array(
		'value' => get_theme_mod( 'dankcave_trust_stat_4_value', '20+ yrs' ),
		'body'  => get_theme_mod( 'dankcave_trust_stat_4_body',  'Glassblowers first, headshop second. We know the gear.' ),
	),
);
?>
<section class="home-trust">
	<div class="home-trust__top">
		<h2 class="home-trust__heading">
			<?php echo esc_html( $heading_1 ); ?><br>
			<?php echo esc_html( $heading_2 ); ?> <span class="home-trust__accent"><?php echo esc_html( $heading_3 ); ?></span>.
		</h2>
		<p class="home-trust__body"><?php echo esc_html( $body ); ?></p>
	</div>
	<div class="home-trust__stats">
		<?php foreach ( $stats as $stat ) : ?>
			<div class="home-trust__stat">
				<div class="home-trust__stat-value"><?php echo esc_html( $stat['value'] ); ?></div>
				<div class="home-trust__stat-body"><?php echo esc_html( $stat['body'] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
