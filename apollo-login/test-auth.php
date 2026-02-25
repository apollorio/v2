<?php
/**
 * Direct authentication test.
 * Access: http://localhost:10004/wp-content/plugins/apollo-login/test-auth.php
 */

// Load WordPress.
require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/wp-load.php';

header( 'Content-Type: text/html; charset=utf-8' );

$username = 'root';
$password = 'root';

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Apollo Login - Authentication Test</title>
	<style>
		body { font-family: system-ui, sans-serif; padding: 20px; background: #f5f5f5; }
		.box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
		.success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
		.error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
		.info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
		h1 { color: #333; }
		code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
		pre { background: #282c34; color: #abb2bf; padding: 15px; border-radius: 4px; overflow-x: auto; }
	</style>
</head>
<body>
	<h1>🔍 Apollo Login - Direct Authentication Test</h1>

	<div class="box info">
		<strong>Testing credentials:</strong><br>
		Username: <code><?php echo esc_html( $username ); ?></code><br>
		Password: <code><?php echo esc_html( $password ); ?></code>
	</div>

	<?php
	// Test 1: Get user by login
	echo '<div class="box">';
	echo '<h2>Test 1: Get User by Login</h2>';
	$user_obj = get_user_by( 'login', $username );
	if ( $user_obj ) {
		echo '<div class="success">✓ User found!</div>';
		echo '<pre>';
		echo 'ID: ' . $user_obj->ID . "\n";
		echo 'Login: ' . $user_obj->user_login . "\n";
		echo 'Email: ' . $user_obj->user_email . "\n";
		echo 'Display Name: ' . $user_obj->display_name . "\n";
		echo '</pre>';
	} else {
		echo '<div class="error">✗ User not found</div>';
	}
	echo '</div>';

	// Test 2: Check password directly
	if ( $user_obj ) {
		echo '<div class="box">';
		echo '<h2>Test 2: Check Password Hash</h2>';

		$stored_hash = $user_obj->user_pass;
		echo '<strong>Stored password hash:</strong><br>';
		echo '<code style="word-break: break-all;">' . esc_html( $stored_hash ) . '</code><br><br>';

		$check = wp_check_password( $password, $stored_hash, $user_obj->ID );
		if ( $check ) {
			echo '<div class="success">✓ Password matches!</div>';
		} else {
			echo '<div class="error">✗ Password does NOT match</div>';
			echo '<br><strong>Trying common passwords:</strong><br>';

			$common = array( 'root', 'admin', 'password', '123456', 'root123', 'admin123' );
			foreach ( $common as $pwd ) {
				$test = wp_check_password( $pwd, $stored_hash, $user_obj->ID );
				if ( $test ) {
					echo '<div class="success">✓ FOUND: Password is <code>' . esc_html( $pwd ) . '</code></div>';
					break;
				} else {
					echo '✗ Not: <code>' . esc_html( $pwd ) . '</code><br>';
				}
			}
		}
		echo '</div>';

		// Test 3: wp_authenticate
		echo '<div class="box">';
		echo '<h2>Test 3: wp_authenticate()</h2>';
		$result = wp_authenticate( $username, $password );

		if ( is_wp_error( $result ) ) {
			echo '<div class="error">';
			echo '<strong>✗ Authentication FAILED</strong><br><br>';
			echo '<strong>Error Code:</strong> ' . $result->get_error_code() . '<br>';
			echo '<strong>Error Message:</strong> ' . $result->get_error_message() . '<br>';
			echo '</div>';

			echo '<br><strong>All error data:</strong><br>';
			echo '<pre>' . print_r( $result->get_error_messages(), true ) . '</pre>';
		} else {
			echo '<div class="success">';
			echo '<strong>✓ Authentication SUCCESS!</strong><br><br>';
			echo 'User ID: ' . $result->ID . '<br>';
			echo 'Login: ' . $result->user_login . '<br>';
			echo '</div>';
		}
		echo '</div>';

		// Test 4: wp_signon
		echo '<div class="box">';
		echo '<h2>Test 4: wp_signon()</h2>';
		$creds  = array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => false,
		);
		$signon = wp_signon( $creds, false );

		if ( is_wp_error( $signon ) ) {
			echo '<div class="error">';
			echo '<strong>✗ wp_signon FAILED</strong><br><br>';
			echo '<strong>Error Code:</strong> ' . $signon->get_error_code() . '<br>';
			echo '<strong>Error Message:</strong> ' . $signon->get_error_message() . '<br>';
			echo '</div>';
		} else {
			echo '<div class="success">';
			echo '<strong>✓ wp_signon SUCCESS!</strong><br><br>';
			echo 'User ID: ' . $signon->ID . '<br>';
			echo 'Login: ' . $signon->user_login . '<br>';
			echo '</div>';
		}
		echo '</div>';
	}
	?>

	<div class="box info">
		<strong>Debug Information:</strong><br>
		WordPress Version: <?php echo get_bloginfo( 'version' ); ?><br>
		Site URL: <?php echo get_site_url(); ?><br>
		WP_DEBUG: <?php echo defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Enabled' : 'Disabled'; ?><br>
		WP_DEBUG_LOG: <?php echo defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ? 'Enabled' : 'Disabled'; ?><br>
	</div>
</body>
</html>
