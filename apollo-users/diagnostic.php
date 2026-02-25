<?php
/**
 * Apollo Users - Diagnostic
 *
 * Check plugin status and rewrite rules
 * URL: http://localhost:10004/wp-content/plugins/apollo-users/diagnostic.php
 */

// Load WordPress
$wp_load_path = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load_path ) ) {
	$wp_load_path = dirname( __DIR__, 3 ) . '/wp-load.php';
}

if ( ! file_exists( $wp_load_path ) ) {
	die( '<h1>Error: Could not find WordPress</h1>' );
}

require_once $wp_load_path;

echo '<!DOCTYPE html>
<html>
<head>
    <title>Apollo Users Diagnostic</title>
    <style>
        body { font-family: monospace; margin: 20px; }
        .section { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>Apollo Users Diagnostic</h1>';

echo '<div class="section">
    <h2>Plugin Status</h2>
    <p>Plugin file: apollo-users/apollo-users.php</p>
    <p>Active: <span class="' . ( is_plugin_active( 'apollo-users/apollo-users.php' ) ? 'success' : 'error' ) . '">' .
		( is_plugin_active( 'apollo-users/apollo-users.php' ) ? 'YES' : 'NO' ) . '</span></p>
</div>';

echo '<div class="section">
    <h2>Rewrite Rules</h2>
    <p>Current rewrite rules:</p>
    <pre>';
global $wp_rewrite;
if ( $wp_rewrite && isset( $wp_rewrite->rules ) ) {
	$radar_rules = array_filter(
		$wp_rewrite->rules,
		function ( $rule ) {
			return strpos( $rule, 'apollo_user_page=radar' ) !== false;
		}
	);
	if ( ! empty( $radar_rules ) ) {
		echo "✓ Found radar rewrite rules:\n";
		foreach ( $radar_rules as $pattern => $rule ) {
			echo "  $pattern => $rule\n";
		}
	} else {
		echo "❌ No radar rewrite rules found\n";
	}
} else {
	echo "❌ Could not access rewrite rules\n";
}
echo '</pre>
</div>';

echo '<div class="section">
    <h2>Test URLs</h2>
    <p><a href="' . home_url( '/radar' ) . '">Test /radar page</a></p>
    <p><a href="' . home_url( '/apollo-flush-rewrites' ) . '">Flush rewrite rules</a></p>
    <p><a href="' . home_url() . '">Back to site</a></p>
</div>';

echo '</body></html>';
