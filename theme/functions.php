<?php
/**
 * Dankcave theme bootstrap.
 *
 * Sets up the theme's constants, includes every inc/ module in a stable order,
 * and lets each module attach its own hooks. Keep this file thin.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DANKCAVE_VERSION', '0.1.0' );
define( 'DANKCAVE_DIR', trailingslashit( get_stylesheet_directory() ) );
define( 'DANKCAVE_URI', trailingslashit( get_stylesheet_directory_uri() ) );

require_once DANKCAVE_DIR . 'inc/setup.php';
require_once DANKCAVE_DIR . 'inc/enqueue.php';
require_once DANKCAVE_DIR . 'inc/icons.php';
require_once DANKCAVE_DIR . 'inc/template-tags.php';
require_once DANKCAVE_DIR . 'inc/customizer.php';
require_once DANKCAVE_DIR . 'inc/block-patterns.php';
require_once DANKCAVE_DIR . 'inc/woocommerce.php';
require_once DANKCAVE_DIR . 'inc/compat.php';
require_once DANKCAVE_DIR . 'inc/maintenance.php';
