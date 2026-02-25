<?php
/**
 * Helper Functions
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get plugin instance
 *
 * @return Core\Plugin
 */
function apollo_login(): Core\Plugin {
	return Core\Plugin::get_instance();
}

/**
 * Check if user has completed quiz
 *
 * @param int $user_id User ID.
 * @return bool
 */
function apollo_quiz_completed( int $user_id ): bool {
	$score = get_user_meta( $user_id, '_apollo_quiz_score', true );
	return ! empty( $score ) && $score > 0;
}

/**
 * Check if user is locked out
 *
 * @param int $user_id User ID.
 * @return bool
 */
function apollo_is_locked_out( int $user_id ): bool {
	$lockout_until = get_user_meta( $user_id, '_apollo_lockout_until', true );

	if ( empty( $lockout_until ) ) {
		return false;
	}

	return time() < (int) $lockout_until;
}

/**
 * Get lockout remaining time in seconds
 *
 * @param int $user_id User ID.
 * @return int
 */
function apollo_lockout_remaining( int $user_id ): int {
	$lockout_until = get_user_meta( $user_id, '_apollo_lockout_until', true );

	if ( empty( $lockout_until ) ) {
		return 0;
	}

	$remaining = (int) $lockout_until - time();
	return max( 0, $remaining );
}

/**
 * Log login attempt
 *
 * @param string $username Username or email.
 * @param bool   $success  Success status.
 * @return void
 */
function apollo_log_login_attempt( string $username, bool $success ): void {
	global $wpdb;

	$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_LOGIN_ATTEMPTS;

	$wpdb->insert(
		$table,
		array(
			'username'     => sanitize_text_field( $username ),
			'ip_address'   => $_SERVER['REMOTE_ADDR'] ?? '',
			'success'      => $success ? 1 : 0,
			'user_agent'   => $_SERVER['HTTP_USER_AGENT'] ?? '',
			'attempted_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%d', '%s', '%s' )
	);
}

/**
 * Get failed login attempts count
 *
 * @param string $identifier Username, email, or IP.
 * @return int
 */
function apollo_get_failed_attempts( string $identifier ): int {
	global $wpdb;

	$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_LOGIN_ATTEMPTS;

	$count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$table}
			WHERE (username = %s OR ip_address = %s)
			AND success = 0
			AND attempted_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
			$identifier,
			$identifier
		)
	);

	return (int) $count;
}

/**
 * Validate CPF (Brazilian document)
 *
 * @param string $cpf CPF number.
 * @return bool
 */
function apollo_validate_cpf( string $cpf ): bool {
	// Remove non-numeric characters
	$cpf = preg_replace( '/[^0-9]/', '', $cpf );

	// Check if has 11 digits
	if ( strlen( $cpf ) !== 11 ) {
		return false;
	}

	// Check for known invalid CPFs
	$invalid = array(
		'00000000000',
		'11111111111',
		'22222222222',
		'33333333333',
		'44444444444',
		'55555555555',
		'66666666666',
		'77777777777',
		'88888888888',
		'99999999999',
	);

	if ( in_array( $cpf, $invalid, true ) ) {
		return false;
	}

	// Validate check digits
	for ( $t = 9; $t < 11; $t++ ) {
		$d = 0;
		for ( $c = 0; $c < $t; $c++ ) {
			$d += $cpf[$c] * ( ( $t + 1 ) - $c );
		}
		$d = ( ( 10 * $d ) % 11 ) % 10;
		if ( $cpf[$c] != $d ) {
			return false;
		}
	}

	return true;
}

/**
 * Generate email verification token
 *
 * @param int $user_id User ID.
 * @return string
 */
function apollo_generate_verification_token( int $user_id ): string {
	$token = wp_generate_password( 32, false );
	update_user_meta( $user_id, '_apollo_verification_token', $token );
	return $token;
}

/**
 * Verify email verification token
 *
 * @param int    $user_id User ID.
 * @param string $token   Token.
 * @return bool
 */
function apollo_verify_email_token( int $user_id, string $token ): bool {
	$saved_token = get_user_meta( $user_id, '_apollo_verification_token', true );

	if ( empty( $saved_token ) || $saved_token !== $token ) {
		return false;
	}

	// Mark as verified
	update_user_meta( $user_id, '_apollo_email_verified', true );
	delete_user_meta( $user_id, '_apollo_verification_token' );

	return true;
}

/**
 * AJAX handler for login
 *
 * @return void
 */
function apollo_ajax_login_handler(): void {
	// Verify nonce - check both 'nonce' and 'apollo_login_nonce' field names
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : ( isset( $_POST['apollo_login_nonce'] ) ? $_POST['apollo_login_nonce'] : '' );
	if ( ! wp_verify_nonce( $nonce, 'apollo_login_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Sessão expirada. Recarregue a página.', 'apollo-login' ) ) );
	}

	$username = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
	$password = isset( $_POST['pwd'] ) ? wp_unslash( $_POST['pwd'] ) : '';
	$remember = isset( $_POST['rememberme'] ) && '1' === $_POST['rememberme'];

	// Trim username
	$username = trim( $username );

	if ( empty( $username ) || empty( $password ) ) {
		wp_send_json_error( array( 'message' => __( 'Preencha todos os campos.', 'apollo-login' ) ) );
	}

	// Try to find user by username or email
	$user_obj = get_user_by( 'login', $username );
	if ( ! $user_obj ) {
		$user_obj = get_user_by( 'email', $username );
	}

	// If not found, try case-insensitive search
	if ( ! $user_obj ) {
		global $wpdb;
		$username_lower = strtolower( $username );
		$user_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->users} WHERE LOWER(user_login) = %s OR LOWER(user_email) = %s LIMIT 1",
				$username_lower,
				$username_lower
			)
		);
		if ( $user_id ) {
			$user_obj = get_user_by( 'ID', $user_id );
		}
	}

	// Use the actual username from database for authentication
	$actual_username = $user_obj ? $user_obj->user_login : $username;

	// Try to authenticate
	$credentials = array(
		'user_login'    => $actual_username,
		'user_password' => $password,
		'remember'      => $remember,
	);

	$user = wp_signon( $credentials, is_ssl() );

	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array(
			'message' => $user->get_error_message()
		) );
	}

	// Success
	wp_send_json_success( array(
		'message'  => __( 'Login realizado com sucesso!', 'apollo-login' ),
		'redirect' => apply_filters( 'apollo_login_redirect', home_url( '/mural/' ), $user ),
	) );
}
add_action( 'wp_ajax_nopriv_apollo_login_ajax', __NAMESPACE__ . '\\apollo_ajax_login_handler' );
add_action( 'wp_ajax_apollo_login_ajax', __NAMESPACE__ . '\\apollo_ajax_login_handler' );
