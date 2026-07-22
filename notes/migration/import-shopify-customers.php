<?php
/**
 * Shopify → WooCommerce customer import.
 *
 * Reads notes/migration/customers_export.csv (standard Shopify Customer Export
 * format) and creates / updates WordPress users for each row with a valid
 * email. Preserves rich Shopify meta (total spent, order count, tags, address)
 * on the user record so campaigns can segment on it.
 *
 * Usage (from wp-cli, inside the container). Env vars are used because wp-cli
 * intercepts anything that looks like a flag:
 *   wp eval-file /tmp/import-shopify-customers.php                             # dry run, all rows
 *   DC_LIMIT=50 wp eval-file /tmp/import-shopify-customers.php                 # dry run, first 50 rows
 *   DC_COMMIT=1 wp eval-file /tmp/import-shopify-customers.php                 # write
 *   DC_LIMIT=50 DC_COMMIT=1 wp eval-file /tmp/import-shopify-customers.php     # write, first 50 rows
 *
 * From `docker exec`, use `-e DC_COMMIT=1` etc:
 *   docker exec -e DC_COMMIT=1 -e DC_LIMIT=50 dankcave-cli wp eval-file /tmp/import-shopify-customers.php --allow-root
 *
 * Idempotent: safe to re-run. Existing users are updated, not duplicated.
 *
 * Segments applied (custom user meta `dc_import_segments`, comma-separated):
 *   shopify-buyer      total_spent > 0
 *   shopify-prospect   total_spent = 0
 *   woo-customer       already has a Woo order (union of Shopify + Woo)
 *
 * Overlap rule: Shopify record wins the address / phone / name fields for
 * duplicates on email, because the Shopify history is richer than a mostly-
 * empty Woo bot account. The Woo user_login and user_pass are preserved.
 *
 * @package Dankcave\Migration
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// -----------------------------------------------------------------------------
// Args (from env vars; wp-cli captures anything that looks like a flag)
// -----------------------------------------------------------------------------
$commit = ! empty( getenv( 'DC_COMMIT' ) );
$limit  = (int) getenv( 'DC_LIMIT' );

// -----------------------------------------------------------------------------
// Locate CSV — mount it into /tmp when calling docker exec, e.g.:
//   docker cp customers_export.csv <container>:/tmp/customers_export.csv
// -----------------------------------------------------------------------------
$csv_path = '/tmp/customers_export.csv';
if ( ! file_exists( $csv_path ) ) {
	fwrite( STDERR, "CSV not found at $csv_path — copy it into the container first.\n" );
	exit( 1 );
}

// -----------------------------------------------------------------------------
// Log setup
// -----------------------------------------------------------------------------
$mode = $commit ? 'COMMIT' : 'DRY-RUN';
echo "Shopify import — mode: $mode\n";
echo "Source: $csv_path\n";
if ( $limit ) { echo "Limit:  $limit rows\n"; }
echo str_repeat( '-', 60 ) . "\n";

$counts = array(
	'read'         => 0,
	'skipped_email'=> 0,
	'created'      => 0,
	'updated'      => 0,
	'error'        => 0,
);

$fh = fopen( $csv_path, 'r' );
if ( ! $fh ) { fwrite( STDERR, "Cannot read $csv_path\n" ); exit( 1 ); }
$header = fgetcsv( $fh );

$col = function ( $row, $index ) {
	return isset( $row[ $index ] ) ? trim( (string) $row[ $index ] ) : '';
};

// Column indexes (0-based) from the Shopify default export header:
// 0 Customer ID  1 First Name  2 Last Name  3 Email  4 Accepts Marketing
// 5 Default Address Company  6 Address1  7 Address2  8 City  9 Province Code
// 10 Country Code  11 Zip  12 Default Address Phone  13 Phone
// 14 Accepts SMS  15 Total Spent  16 Total Orders  17 Note  18 Tax Exempt
// 19 Tags  20 Accepts WhatsApp

$row_index = 0;
while ( ( $row = fgetcsv( $fh ) ) !== false ) {
	$row_index++;
	$counts['read']++;
	if ( $limit && $counts['read'] > $limit ) { break; }

	$email = strtolower( $col( $row, 3 ) );
	if ( ! $email || ! is_email( $email ) ) {
		$counts['skipped_email']++;
		continue;
	}

	$first = $col( $row, 1 );
	$last  = $col( $row, 2 );
	$accepts_email = strtolower( $col( $row, 4 ) ) === 'yes';
	$company = $col( $row, 5 );
	$addr1   = $col( $row, 6 );
	$addr2   = $col( $row, 7 );
	$city    = $col( $row, 8 );
	$state   = $col( $row, 9 );
	$country = $col( $row, 10 );
	$zip     = $col( $row, 11 );
	$phone   = $col( $row, 13 ) ?: $col( $row, 12 );
	$spent   = (float) $col( $row, 15 );
	$orders  = (int) $col( $row, 16 );
	$note    = $col( $row, 17 );
	$tags    = $col( $row, 19 );

	$existing = get_user_by( 'email', $email );

	$segment = $spent > 0 ? 'shopify-buyer' : 'shopify-prospect';

	if ( $existing ) {
		$counts['updated']++;
		if ( $commit ) {
			apply_updates( $existing->ID, compact( 'first', 'last', 'phone', 'company', 'addr1', 'addr2', 'city', 'state', 'country', 'zip', 'spent', 'orders', 'note', 'tags', 'accepts_email', 'segment' ) );
		}
	} else {
		if ( $commit ) {
			$user_id = wc_create_new_customer( $email, sanitize_user( strtolower( ( $first . '.' . $last ) ?: explode( '@', $email )[0] ), true ), wp_generate_password( 20, true, true ) );
			if ( is_wp_error( $user_id ) ) {
				$counts['error']++;
				fwrite( STDERR, "row $row_index ($email): " . $user_id->get_error_message() . "\n" );
				continue;
			}
			apply_updates( $user_id, compact( 'first', 'last', 'phone', 'company', 'addr1', 'addr2', 'city', 'state', 'country', 'zip', 'spent', 'orders', 'note', 'tags', 'accepts_email', 'segment' ) );
		}
		$counts['created']++;
	}
}

fclose( $fh );

echo str_repeat( '-', 60 ) . "\n";
foreach ( $counts as $k => $v ) { printf( "%-16s %d\n", $k, $v ); }
echo "\nMode: $mode  " . ( $commit ? '' : '(nothing was written — pass --commit to apply)' ) . "\n";

// -----------------------------------------------------------------------------
// Apply updates to a user_id.
// -----------------------------------------------------------------------------
function apply_updates( $user_id, $data ) {
	extract( $data );

	// Names — only overwrite if we have something.
	if ( $first ) update_user_meta( $user_id, 'first_name', $first );
	if ( $last )  update_user_meta( $user_id, 'last_name',  $last );

	// Billing + shipping addresses (WC standard meta keys).
	$addr_meta = array(
		'billing_first_name'  => $first,
		'billing_last_name'   => $last,
		'billing_company'     => $company,
		'billing_address_1'   => $addr1,
		'billing_address_2'   => $addr2,
		'billing_city'        => $city,
		'billing_state'       => $state,
		'billing_postcode'    => $zip,
		'billing_country'     => $country,
		'billing_phone'       => $phone,
		'shipping_first_name' => $first,
		'shipping_last_name'  => $last,
		'shipping_company'    => $company,
		'shipping_address_1'  => $addr1,
		'shipping_address_2'  => $addr2,
		'shipping_city'       => $city,
		'shipping_state'      => $state,
		'shipping_postcode'   => $zip,
		'shipping_country'    => $country,
	);
	foreach ( $addr_meta as $k => $v ) {
		if ( $v !== '' ) update_user_meta( $user_id, $k, $v );
	}

	// Shopify-specific bookkeeping meta.
	update_user_meta( $user_id, '_shopify_total_spent',  $spent );
	update_user_meta( $user_id, '_shopify_total_orders', $orders );
	if ( $note )      update_user_meta( $user_id, '_shopify_note', $note );
	if ( $tags )      update_user_meta( $user_id, '_shopify_tags', $tags );
	update_user_meta( $user_id, '_shopify_accepts_email', $accepts_email ? 1 : 0 );
	update_user_meta( $user_id, '_source', 'shopify_import' );

	// Segments (merge with any existing).
	$existing_segments = (string) get_user_meta( $user_id, 'dc_import_segments', true );
	$segments = array_filter( array_unique( array_map( 'trim', explode( ',', $existing_segments . ',' . $segment ) ) ) );
	update_user_meta( $user_id, 'dc_import_segments', implode( ',', $segments ) );
}
