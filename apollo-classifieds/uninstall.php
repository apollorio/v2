<?php

/**
 * Uninstall Script
 *
 * Removes plugin data when uninstalled (not just deactivated).
 *
 * @package Apollo\Classifieds
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all classifieds
$classifieds = get_posts(
	array(
		'post_type'   => 'classified',
		'numberposts' => -1,
		'post_status' => 'any',
	)
);

foreach ( $classifieds as $classified ) {
	wp_delete_post( $classified->ID, true );
}

// Delete options
delete_option( 'apollo_classifieds_version' );

// Clear transients
delete_transient( 'apollo_classifieds_cache' );
