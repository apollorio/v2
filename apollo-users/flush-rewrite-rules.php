<?php
/**
 * Flush Rewrite Rules - Manual Trigger
 *
 * Run this file ONCE to flush rewrite rules after plugin updates.
 * Access via: http://localhost:10004/wp-content/plugins/apollo-users/flush-rewrite-rules.php
 *
 * @package Apollo\Users
 */

// Load WordPress
require_once dirname( __DIR__, 4 ) . '/wp-load.php';

// Security check
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'You do not have permission to access this page.' );
}

// Add rewrite rules
do_action( 'init' );

// Flush
flush_rewrite_rules( true );

// Output
echo '<h1>✓ Rewrite Rules Flushed</h1>';
echo '<p>The following virtual pages should now work:</p>';
echo '<ul>';
echo '<li><a href="' . home_url( '/radar' ) . '">/radar</a> - User Directory</li>';
echo '<li><a href="' . home_url( '/id/admin' ) . '">/id/{username}</a> - User Profiles</li>';
echo '<li><a href="' . home_url( '/editar-perfil' ) . '">/editar-perfil</a> - Edit Profile</li>';
echo '</ul>';
echo '<p><strong>You can now close this page and test the routes.</strong></p>';
echo '<p><a href="' . home_url() . '">← Back to Home</a></p>';
