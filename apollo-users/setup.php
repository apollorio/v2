<?php
/**
 * Apollo Users - Setup & Flush
 *
 * This script ensures the plugin is active and flushes rewrite rules
 * URL: http://localhost:10004/wp-content/plugins/apollo-users/setup.php
 */

// Load WordPress
$wp_load_path = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load_path ) ) {
	$wp_load_path = dirname( __DIR__, 3 ) . '/wp-load.php';
}

if ( ! file_exists( $wp_load_path ) ) {
	die( '<h1>Error: Could not find WordPress</h1><p>wp-load.php not found.</p>' );
}

require_once $wp_load_path;

// Check if we can access WordPress functions
if ( ! function_exists( 'activate_plugin' ) || ! function_exists( 'flush_rewrite_rules' ) ) {
	die( '<h1>Error: WordPress functions not available</h1>' );
}

$plugin_file = 'apollo-users/apollo-users.php';
$messages    = array();

// Try to activate the plugin
if ( ! is_plugin_active( $plugin_file ) ) {
	$result = activate_plugin( $plugin_file );
	if ( is_wp_error( $result ) ) {
		$messages[] = '❌ Failed to activate plugin: ' . $result->get_error_message();
	} else {
		$messages[] = '✅ Plugin activated successfully';
	}
} else {
	$messages[] = 'ℹ️ Plugin was already active';
	// Deactivate and reactivate to force rewrite flush
	deactivate_plugins( $plugin_file );
	$result = activate_plugin( $plugin_file );
	if ( is_wp_error( $result ) ) {
		$messages[] = '❌ Failed to reactivate plugin: ' . $result->get_error_message();
	} else {
		$messages[] = '✅ Plugin reactivated successfully';
	}
}

// Flush rewrite rules
flush_rewrite_rules();
$messages[] = '✅ Rewrite rules flushed';

echo '<!DOCTYPE html>
<html>
<head>
    <title>Apollo Users Setup - Complete</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .message { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .button { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; display: inline-block; margin: 10px 5px; }
        .button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>Apollo Users Setup Complete</h1>';

foreach ( $messages as $message ) {
	$class = 'info';
	if ( strpos( $message, '✅' ) === 0 ) {
		$class = 'success';
	}
	if ( strpos( $message, '❌' ) === 0 ) {
		$class = 'error';
	}
	echo "<div class='message $class'>$message</div>";
}

echo '
    <p>
        <a href="' . home_url( '/radar' ) . '" class="button">Test the Radar page →</a>
        <a href="' . home_url() . '" class="button">← Go back to site</a>
    </p>

    <hr>
    <small>This file can be deleted after setup: <code>' . __FILE__ . '</code></small>
</body>
</html>';
