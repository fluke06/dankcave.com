<?php
/**
 * Site-wide maintenance mode. When active, non-logged-in visitors see a
 * "we are updating, back soon" page. Logged-in admins pass through normally.
 *
 * Toggle via Appearance → Customize → Site Status → Maintenance Mode.
 * Or programmatically via the tpb_maintenance_active filter.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO: Register a Customizer toggle + custom maintenance page template.
// Placeholder guard so we can enable this quickly if needed:
//
// function dankcave_maintenance_gate() {
//     if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) return;
//     if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) return;
//     $active = (bool) get_theme_mod( 'dankcave_maintenance_active', false );
//     if ( ! apply_filters( 'dankcave_maintenance_active', $active ) ) return;
//     status_header( 503 );
//     header( 'Retry-After: 3600' );
//     include DANKCAVE_DIR . 'maintenance.php';
//     exit;
// }
// add_action( 'template_redirect', 'dankcave_maintenance_gate', 0 );
