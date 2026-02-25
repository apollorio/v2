<?php
/**
 * Login Handler
 *
 * Handles user authentication via AJAX with rate limiting,
 * security state management, and login attempt logging.
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Auth;

use Apollo\Login\Security\RateLimiter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Login {

	public function __construct() {
		add_action( 'wp_ajax_nopriv_apollo_login', [ $this, 'handle_login' ] );
		add_action( 'wp_ajax_apollo_login', [ $this, 'handle_login' ] );
	}

	public function handle_login(): void {
		// Verify nonce
		if ( ! check_ajax_referer( 'apollo_auth_nonce', 'nonce', false ) ) {
			wp_send_json_error([
				'message' => __( 'Verificação de segurança falhou.', 'apollo-login' ),
				'code'    => 'nonce_failed',
			], 403 );
		}

		$ip = $this->get_client_ip();

		// Check rate limit
		if ( RateLimiter::is_locked_out( $ip ) ) {
			$this->log_attempt( $ip, sanitize_text_field( $_POST['log'] ?? '' ), 'locked' );
			wp_send_json_error([
				'message'  => __( 'Sistema bloqueado por segurança. Aguarde.', 'apollo-login' ),
				'code'     => 'rate_limited',
				'lockout'  => true,
				'duration' => RateLimiter::get_remaining_lockout( $ip ),
			], 429 );
		}

		$log      = sanitize_text_field( $_POST['log'] ?? '' );
		$pwd      = $_POST['pwd'] ?? '';
		$remember = ! empty( $_POST['rememberme'] );

		if ( empty( $log ) || empty( $pwd ) ) {
			wp_send_json_error([
				'message' => __( 'Preencha todos os campos.', 'apollo-login' ),
				'code'    => 'missing_fields',
			], 400 );
		}

		// Determine login type (email, CPF, passport, or username)
		$user = $this->find_user( $log );

		if ( ! $user ) {
			$this->handle_failed_attempt( $ip, $log );
			wp_send_json_error([
				'message'  => __( 'Credenciais incorretas. Tente novamente.', 'apollo-login' ),
				'code'     => 'invalid_credentials',
				'attempts' => RateLimiter::get_attempt_count( $ip ),
				'max'      => (int) get_option( 'apollo_login_max_attempts', APOLLO_LOGIN_MAX_ATTEMPTS ),
			], 401 );
		}

		// Authenticate
		$authenticated = wp_authenticate( $user->user_login, $pwd );

		if ( is_wp_error( $authenticated ) ) {
			$this->handle_failed_attempt( $ip, $log );
			$attempts = RateLimiter::get_attempt_count( $ip );
			$max      = (int) get_option( 'apollo_login_max_attempts', APOLLO_LOGIN_MAX_ATTEMPTS );

			wp_send_json_error([
				'message'  => __( 'Credenciais incorretas. Tente novamente.', 'apollo-login' ),
				'code'     => 'invalid_credentials',
				'attempts' => $attempts,
				'max'      => $max,
				'warning'  => $attempts >= ( $max - 1 ),
			], 401 );
		}

		// Success - log in
		wp_set_current_user( $authenticated->ID );
		wp_set_auth_cookie( $authenticated->ID, $remember );

		// Clear rate limit
		RateLimiter::clear_attempts( $ip );

		// Update last login
		update_user_meta( $authenticated->ID, APOLLO_META_LAST_LOGIN, current_time( 'mysql' ) );

		// Log success
		$this->log_attempt( $ip, $log, 'success' );

		$redirect = get_option( 'apollo_login_redirect_after_login', '/mural/' );

		/**
		 * Filter the redirect URL after successful login
		 *
		 * @param string   $redirect Redirect URL
		 * @param \WP_User $user     Authenticated user
		 */
		$redirect = apply_filters( 'apollo_login_redirect', $redirect, $authenticated );

		wp_send_json_success([
			'message'  => __( 'Acesso autorizado. Redirecionando...', 'apollo-login' ),
			'redirect' => home_url( $redirect ),
			'user_id'  => $authenticated->ID,
		]);
	}

	/**
	 * Find user by email, CPF, passport, Instagram, or username
	 */
	private function find_user( string $identifier ): ?\WP_User {
		// Try email
		if ( is_email( $identifier ) ) {
			$user = get_user_by( 'email', $identifier );
			if ( $user ) {
				return $user;
			}
		}

		// Try username (Instagram = username in Apollo)
		$clean = ltrim( $identifier, '@' );
		$user  = get_user_by( 'login', $clean );
		if ( $user ) {
			return $user;
		}

		// Try CPF (search user meta)
		$cpf_clean = preg_replace( '/\D/', '', $identifier );
		if ( strlen( $cpf_clean ) === 11 ) {
			$users = get_users([
				'meta_key'   => '_apollo_cpf',
				'meta_value' => $cpf_clean,
				'number'     => 1,
			]);
			if ( ! empty( $users ) ) {
				return $users[0];
			}
		}

		// Try passport
		if ( strlen( $identifier ) >= 5 && strlen( $identifier ) <= 20 ) {
			$users = get_users([
				'meta_key'   => '_apollo_passport',
				'meta_value' => strtoupper( $identifier ),
				'number'     => 1,
			]);
			if ( ! empty( $users ) ) {
				return $users[0];
			}
		}

		return null;
	}

	private function handle_failed_attempt( string $ip, string $username ): void {
		RateLimiter::record_attempt( $ip );
		$this->log_attempt( $ip, $username, 'failed' );

		// Check if should lock out
		$attempts = RateLimiter::get_attempt_count( $ip );
		$max      = (int) get_option( 'apollo_login_max_attempts', APOLLO_LOGIN_MAX_ATTEMPTS );

		if ( $attempts >= $max ) {
			RateLimiter::lock_out( $ip );
		}
	}

	private function log_attempt( string $ip, string $username, string $status ): void {
		global $wpdb;

		$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_LOGIN_ATTEMPTS;

		$wpdb->insert( $table, [
			'ip_address' => $ip,
			'username'   => $username,
			'user_agent' => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ),
			'status'     => $status,
			'created_at' => current_time( 'mysql' ),
		], [ '%s', '%s', '%s', '%s', '%s' ] );
	}

	private function get_client_ip(): string {
		$headers = [
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'REMOTE_ADDR',
		];

		foreach ( $headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$ip = explode( ',', sanitize_text_field( $_SERVER[ $header ] ) );
				return trim( $ip[0] );
			}
		}

		return '0.0.0.0';
	}
}
