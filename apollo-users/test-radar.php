<?php
/**
 * Test if radar page is working
 */

// Load WordPress
require_once dirname( __DIR__, 4 ) . '/wp-load.php';

echo '<h1>Radar Page Test</h1>';

// Check if query var is set
$page = get_query_var( 'apollo_user_page', 'NOT_SET' );
echo "<p>apollo_user_page query var: <strong>$page</strong></p>";

// Check current URL
$current_url = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN';
echo "<p>Current URL: <strong>$current_url</strong></p>";

// Check if user is logged in
$logged_in = is_user_logged_in() ? 'YES' : 'NO';
echo "<p>User logged in: <strong>$logged_in</strong></p>";

// Check template
$template = get_page_template();
echo "<p>Template: <strong>$template</strong></p>";
