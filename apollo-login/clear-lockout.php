<?php
/**
 * Clear lockout script.
 * Access: http://localhost:10004/wp-content/plugins/apollo-login/clear-lockout.php
 */

// Load WordPress.
require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/wp-load.php';

// Get user
$user = get_user_by( 'login', 'root' );

if ( ! $user ) {
	die( 'User not found!' );
}

// Get current lockout data
$lockout_until  = get_user_meta( $user->ID, '_apollo_lockout_until', true );
$login_attempts = get_user_meta( $user->ID, '_apollo_login_attempts', true );

echo '<h1>🔓 Apollo Login - Clear Lockout</h1>';
echo '<div style="font-family: monospace; background: #f5f5f5; padding: 20px; margin: 20px 0;">';
echo '<h2>Current Status:</h2>';
echo '<strong>User:</strong> ' . $user->user_login . ' (ID: ' . $user->ID . ')<br>';
echo '<strong>Lockout Until:</strong> ' . ( $lockout_until ? $lockout_until : 'Not locked' ) . '<br>';
echo '<strong>Failed Attempts:</strong> ' . ( $login_attempts ? $login_attempts : '0' ) . '<br>';
echo '</div>';

// Clear lockout
delete_user_meta( $user->ID, '_apollo_lockout_until' );
delete_user_meta( $user->ID, '_apollo_login_attempts' );

echo '<div style="background: #d4edda; color: #155724; padding: 20px; margin: 20px 0; border-left: 4px solid #28a745;">';
echo '<strong>✓ Lockout cleared successfully!</strong><br>';
echo 'Server-side lockout removed. Cleaning browser localStorage...';
echo '</div>';

echo '<script>
// Clear client-side lockout
localStorage.removeItem("apollo_lockout_end");
console.log("✓ Cleared apollo_lockout_end from localStorage");

// Redirect to login page after 2 seconds
setTimeout(function() {
	window.location.href = "/acesso/";
}, 2000);
</script>';

echo '<div style="background: #fff3cd; color: #856404; padding: 20px; margin: 20px 0; border-left: 4px solid #ffc107;">';
echo '<strong>⏳ Redirecting to login page in 2 seconds...</strong>';
echo '</div>';

echo '<a href="/acesso/" style="display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Go to Login Page Now</a>';
