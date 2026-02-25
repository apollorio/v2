<?php
/**
 * Apollo Users - Flush Rewrite Rules
 *
 * Run this file to flush WordPress rewrite rules for Apollo Users plugin
 */

// Load WordPress
$wp_load_path = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load_path ) ) {
	// Try alternative path
	$wp_load_path = dirname( __DIR__, 3 ) . '/wp-load.php';
}
require_once $wp_load_path;

// Check if user is admin
if ( ! current_user_can( 'manage_options' ) ) {
	die( 'Access denied. Admin privileges required.' );
}

// Flush rewrite rules
global $wp_rewrite;
$wp_rewrite->flush_rules();

echo '<h1>Rewrite Rules Flushed Successfully!</h1>';
echo '<p>The /radar page should now work.</p>';
echo '<p><a href="' . home_url( '/radar' ) . '">Test the Radar page</a></p>';
echo '<p><a href="' . home_url() . '">Go back to site</a></p>';
