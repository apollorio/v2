<?php
/**
 * Apollo Users - Flush Rewrite Rules (Direct Access)
 *
 * Access this file directly in browser to flush WordPress rewrite rules
 * URL: http://localhost:10004/wp-content/plugins/apollo-users/flush.php
 */

// Load WordPress
$wp_load_path = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load_path ) ) {
	// Try alternative path
	$wp_load_path = dirname( __DIR__, 3 ) . '/wp-load.php';
}

if ( ! file_exists( $wp_load_path ) ) {
	die( '<h1>Error: Could not find WordPress</h1><p>wp-load.php not found at expected locations.</p>' );
}

require_once $wp_load_path;

// Check if we can access WordPress functions
if ( ! function_exists( 'flush_rewrite_rules' ) ) {
	die( '<h1>Error: WordPress functions not available</h1>' );
}

// Flush rewrite rules
flush_rewrite_rules();

echo '<!DOCTYPE html>
<html>
<head>
    <title>Rewrite Rules Flushed - Apollo Users</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 20px; border-radius: 5px; }
        .button { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; display: inline-block; margin: 10px 5px; }
        .button:hover { background: #005a87; }
    </style>
</head>
<body>
    <div class="success">
        <h1>✓ Rewrite Rules Flushed Successfully!</h1>
        <p>The /radar page should now work properly.</p>
    </div>

    <p>
        <a href="' . home_url( '/radar' ) . '" class="button">Test the Radar page →</a>
        <a href="' . home_url() . '" class="button">← Go back to site</a>
    </p>

    <hr>
    <small>This file can be deleted after testing: <code>' . __FILE__ . '</code></small>
</body>
</html>';
