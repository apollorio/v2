<?php
/**
 * Apollo Users - Flush Rewrite Rules (CLI)
 *
 * Run this script via command line to flush WordPress rewrite rules
 */

// Define WordPress paths
$wp_paths = array(
	__DIR__ . '/../../../wp-load.php', // From plugins/apollo-users/
	__DIR__ . '/../../wp-load.php',   // From wp-content/plugins/
	dirname( __DIR__, 4 ) . '/wp-load.php', // 4 levels up
);

$wp_load_found = false;
foreach ( $wp_paths as $path ) {
	if ( file_exists( $path ) ) {
		require_once $path;
		$wp_load_found = true;
		break;
	}
}

if ( ! $wp_load_found ) {
	die( "Error: Could not find wp-load.php\n" );
}

// Check if we can access WordPress functions
if ( ! function_exists( 'flush_rewrite_rules' ) ) {
	die( "Error: WordPress functions not available\n" );
}

// Flush rewrite rules
flush_rewrite_rules();

echo "✓ Rewrite rules flushed successfully!\n";
echo "✓ The /radar page should now work.\n";
echo "✓ Test it at: http://localhost:10004/radar\n";
