<?php

/**
 * Login Handler
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Auth;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login Handler class
 */
class LoginHandler {


	/**
	 * Constructor
	 */
	public function __construct() {
		// AJAX login handlers — JS sends action:'apollo_login' to admin-ajax.php
		add_action( 'wp_ajax_nopriv_apollo_login', array( $this, 'handle_ajax_login' ) );
		add_action( 'wp_ajax_apollo_login', array( $this, 'handle_ajax_login' ) );

		add_filter( 'authenticate', array( $this, 'check_lockout' ), 30, 3 );
		add_filter( 'login_errors', array( $this, 'custom_login_errors' ), 10, 1 );
		add_action( 'wp_login_failed', array( $this, 'on_login_failed' ) );
		add_action( 'wp_login', array( $this, 'on_login_success' ), 10, 2 );
	}

	/**
	 * Handle AJAX login request from /acesso form
	 *
	 * @return void
	 */
	public function handle_ajax_login(): void {
		// Verify nonce
		if ( ! check_ajax_referer( 'apollo_auth_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Verificação de segurança falhou. Recarregue a página.', 'apollo-login' ),
					'code'    => 'nonce_failed',
				),
				403
			);
		}

		$log      = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
		$pwd      = isset( $_POST['pwd'] ) ? $_POST['pwd'] : '';
		$remember = ! empty( $_POST['rememberme'] );

		if ( empty( $log ) || empty( $pwd ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Preencha todos os campos.', 'apollo-login' ),
					'code'    => 'missing_fields',
				),
				400
			);
		}

		// Find user by email, CPF, passport, Instagram (@handle), or username
		$user = $this->find_user( $log );

		if ( ! $user ) {
			wp_send_json_error(
				array(
					'message'  => __( 'Credenciais incorretas. Tente novamente.', 'apollo-login' ),
					'code'     => 'invalid_credentials',
					'attempts' => $this->get_ip_attempts(),
					'max'      => APOLLO_LOGIN_MAX_ATTEMPTS,
				),
				401
			);
		}

		// Check lockout before authenticate
		if ( \Apollo\Login\apollo_is_locked_out( $user->ID ) ) {
			$remaining = \Apollo\Login\apollo_lockout_remaining( $user->ID );
			wp_send_json_error(
				array(
					'message'  => __( 'Sistema bloqueado por segurança. Aguarde.', 'apollo-login' ),
					'code'     => 'rate_limited',
					'lockout'  => true,
					'duration' => $remaining,
				),
				429
			);
		}

		// Authenticate
		$authenticated = wp_authenticate( $user->user_login, $pwd );

		if ( is_wp_error( $authenticated ) ) {
			$attempts = $this->get_ip_attempts();

			wp_send_json_error(
				array(
					'message'  => __( 'Credenciais incorretas. Tente novamente.', 'apollo-login' ),
					'code'     => 'invalid_credentials',
					'attempts' => $attempts,
					'max'      => APOLLO_LOGIN_MAX_ATTEMPTS,
					'warning'  => $attempts >= ( APOLLO_LOGIN_MAX_ATTEMPTS - 1 ),
				),
				401
			);
		}

		// Success — set auth cookie
		wp_set_current_user( $authenticated->ID );
		wp_set_auth_cookie( $authenticated->ID, $remember );

		// Update last login
		update_user_meta( $authenticated->ID, '_apollo_last_login', current_time( 'mysql' ) );

		$redirect = apply_filters( 'apollo_login_redirect', home_url( '/explore' ), $authenticated );

		wp_send_json_success(
			array(
				'message'  => __( 'Acesso autorizado. Redirecionando...', 'apollo-login' ),
				'redirect' => $redirect,
				'user_id'  => $authenticated->ID,
			)
		);
	}

	/**
	 * Find user by email, CPF, passport, Instagram, or username
	 *
	 * @param string $identifier User identifier.
	 * @return \WP_User|null
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
			$users = get_users(
				array(
					'meta_key'   => '_apollo_cpf',
					'meta_value' => $cpf_clean,
					'number'     => 1,
				)
			);
			if ( ! empty( $users ) ) {
				return $users[0];
			}
		}

		// Try passport
		if ( strlen( $identifier ) >= 5 && strlen( $identifier ) <= 20 ) {
			$users = get_users(
				array(
					'meta_key'   => '_apollo_passport',
					'meta_value' => strtoupper( $identifier ),
					'number'     => 1,
				)
			);
			if ( ! empty( $users ) ) {
				return $users[0];
			}
		}

		return null;
	}

	/**
	 * Get failed login attempts count for current IP
	 *
	 * @return int
	 */
	private function get_ip_attempts(): int {
		$ip            = $this->get_client_ip();
		$transient_key = 'apollo_login_attempts_' . md5( $ip );
		return (int) get_transient( $transient_key );
	}

	/**
	 * Get client IP address
	 *
	 * @return string
	 */
	private function get_client_ip(): string {
		$headers = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'REMOTE_ADDR',
		);

		foreach ( $headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$ip = explode( ',', sanitize_text_field( $_SERVER[ $header ] ) );
				return trim( $ip[0] );
			}
		}

		return '0.0.0.0';
	}

	/**
	 * Custom login error messages
	 *
	 * @param string $error Error message.
	 * @return string
	 */
	public function custom_login_errors( string $error ): string {
		// Check for specific error codes
		if ( strpos( $error, 'locked_out' ) !== false ) {
			return __( 'Conta temporariamente bloqueada devido a múltiplas tentativas falhadas. Tente novamente em alguns minutos.', 'apollo-login' );
		}

		if ( strpos( $error, 'Credenciais incorretas' ) !== false ) {
			return __( 'Usuário ou senha incorretos. Verifique se digitou corretamente.', 'apollo-login' );
		}

		// Return original error if not customized
		return $error;
	}
	public function check_lockout( $user, string $username, string $password ) {
		// Skip if already an error or no username
		if ( is_wp_error( $user ) || empty( $username ) ) {
			return $user;
		}

		// Get user by username or email (case-insensitive for username)
		$user_obj = get_user_by( 'login', $username );
		if ( ! $user_obj ) {
			$user_obj = get_user_by( 'email', $username );
		}

		// If still not found, try case-insensitive username search
		if ( ! $user_obj ) {
			$user_obj = get_user_by( 'login', strtolower( $username ) );
		}
		if ( ! $user_obj ) {
			$user_obj = get_user_by( 'login', strtoupper( $username ) );
		}

		if ( ! $user_obj ) {
			return $user;
		}

		// Check lockout status.
		if ( \Apollo\Login\apollo_is_locked_out( $user_obj->ID ) ) {
			$remaining = \Apollo\Login\apollo_lockout_remaining( $user_obj->ID );

			return new \WP_Error(
				'apollo_locked_out',
				sprintf(
					/* translators: %d: seconds remaining. */
					__( 'Conta bloqueada por segurança. Tente novamente em %d segundos.', 'apollo-login' ),
					$remaining
				)
			);
		}

		return $user;
	}

	/**
	 * Handle failed login
	 *
	 * @param string $username Username or email.
	 * @return void
	 */
	public function on_login_failed( string $username ): void {
		// Log the attempt
		\Apollo\Login\apollo_log_login_attempt( $username, false );

		// Get failed attempts count
		$attempts = \Apollo\Login\apollo_get_failed_attempts( $username );

		// Check if should lock out
		if ( $attempts >= APOLLO_LOGIN_MAX_ATTEMPTS ) {
			// Get user
			$user = get_user_by( 'login', $username );
			if ( ! $user ) {
				$user = get_user_by( 'email', $username );
			}

			if ( $user ) {
				// Set lockout
				$lockout_until = time() + APOLLO_LOGIN_LOCKOUT_DURATION;
				update_user_meta( $user->ID, '_apollo_lockout_until', $lockout_until );

				// Reset attempts counter
				update_user_meta( $user->ID, '_apollo_login_attempts', 0 );
			}
		}
	}

	/**
	 * Handle successful login
	 *
	 * @param string   $username Username.
	 * @param \WP_User $user     User object.
	 * @return void
	 */
	public function on_login_success( string $username, \WP_User $user ): void {
		// Log successful login
		\Apollo\Login\apollo_log_login_attempt( $username, true );

		// Clear lockout
		delete_user_meta( $user->ID, '_apollo_lockout_until' );
		delete_user_meta( $user->ID, '_apollo_login_attempts' );

		// Update last login time
		update_user_meta( $user->ID, '_apollo_last_login', current_time( 'mysql' ) );
	}
}
