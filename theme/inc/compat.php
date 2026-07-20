<?php
/**
 * Third-party plugin compatibility filters.
 *
 * These plugins run on production and the theme must not break their hooks
 * during template refactoring. See CLAUDE.md § Third-party integrations.
 *
 *   - Authorize.net (woo-authorize-net-gateway-aim) — payment gateway
 *   - Shippo — shipping calculation
 *   - Sezzle — BNPL, adds a widget on single-product summary hook
 *   - MailChimp for WordPress (mc4wp) — newsletter capture
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO: Add plugin-specific compat filters as issues surface during template building.
// Examples we may need:
//   - MC4WP shortcode wrapper for the newsletter band
//   - Sezzle widget positioning tweak on single-product template
//   - Custom checkout field ordering if Authorize.net's fields clash with theme layout
//   - Shippo rate-picker styling
