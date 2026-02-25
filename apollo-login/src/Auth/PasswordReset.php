<?php

/**
 * Password Reset Handler
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
 * Password Reset class
 */
class PasswordReset {



	/**
	 * Constructor
	 */
	public function __construct() {
		// AJAX handlers for password reset
		add_action( 'wp_ajax_nopriv_apollo_forgot_password', array( $this, 'handle_forgot_password' ) );
		add_action( 'wp_ajax_apollo_forgot_password', array( $this, 'handle_forgot_password' ) );
		add_action( 'wp_ajax_nopriv_apollo_reset_confirm', array( $this, 'handle_reset_confirm' ) );
	}

	/**
	 * Handle forgot password AJAX request
	 *
	 * @return void
	 */
	public function handle_forgot_password(): void {
		// Verify nonce
		if (
			! isset( $_POST['apollo_forgot_password_nonce'] ) ||
			! wp_verify_nonce( $_POST['apollo_forgot_password_nonce'], 'apollo_forgot_password_action' )
		) {
			wp_send_json_error( __( 'Verificação de segurança falhou.', 'apollo-login' ), 403 );
		}

		$email = isset( $_POST['forgot_email'] ) ? sanitize_email( $_POST['forgot_email'] ) : '';

		if ( empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error( __( 'E-mail inválido.', 'apollo-login' ), 400 );
		}

		// Always return success to prevent user enumeration
		$user = get_user_by( 'email', $email );

		if ( $user ) {
			$token   = \Apollo\Login\apollo_login_generate_token( 32 );
			$expires = time() + 3600; // 1 hour

			// Store hashed token
			update_user_meta( $user->ID, APOLLO_META_PASSWORD_RESET_TOKEN, wp_hash( $token ) );
			update_user_meta( $user->ID, APOLLO_META_PASSWORD_RESET_EXPIRES, $expires );

			$reset_url = add_query_arg(
				array(
					'token'   => $token,
					'user_id' => $user->ID,
				),
				home_url( '/' . APOLLO_LOGIN_PAGE_RESET . '/' )
			);

			/**
			 * Fires when a password reset is requested — triggers apollo-email.
			 *
			 * @since 1.0.0
			 * @param int    $user_id   User ID.
			 * @param string $reset_url Password reset URL.
			 */
			do_action( 'apollo/login/password_reset_requested', $user->ID, $reset_url );

			// Fallback: send plain text if apollo-email not active
			if ( ! did_action( 'apollo/email/init' ) ) {
				$this->send_reset_email( $user, $token );
			}
		}

		wp_send_json_success(
			array(
				'message' => __( 'Se o e-mail existir em nossa base, você receberá instruções de recuperação em instantes.', 'apollo-login' ),
			)
		);
	}

	/**
	 * Handle password reset confirmation
	 *
	 * @return void
	 */
	public function handle_reset_confirm(): void {
		// Verify nonce
		if (
			! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( $_POST['nonce'], 'apollo_reset_confirm_action' )
		) {
			wp_send_json_error( __( 'Verificação de segurança falhou.', 'apollo-login' ), 403 );
		}

		$token        = isset( $_POST['token'] ) ? sanitize_text_field( $_POST['token'] ) : '';
		$user_id      = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : 0;
		$new_password = isset( $_POST['new_password'] ) ? $_POST['new_password'] : '';

		if ( empty( $token ) || ! $user_id || empty( $new_password ) ) {
			wp_send_json_error( __( 'Dados incompletos.', 'apollo-login' ), 400 );
		}

		if ( strlen( $new_password ) < 8 ) {
			wp_send_json_error( __( 'A senha deve ter pelo menos 8 caracteres.', 'apollo-login' ), 400 );
		}

		$stored_hash = get_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_TOKEN, true );
		$expires     = (int) get_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_EXPIRES, true );

		if ( ! $stored_hash || wp_hash( $token ) !== $stored_hash ) {
			wp_send_json_error( __( 'Token inválido ou expirado.', 'apollo-login' ), 400 );
		}

		if ( time() > $expires ) {
			delete_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_TOKEN );
			delete_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_EXPIRES );
			wp_send_json_error( __( 'Token expirado. Solicite um novo link de recuperação.', 'apollo-login' ), 400 );
		}

		// Reset password
		wp_set_password( $new_password, $user_id );

		// Clean up tokens
		delete_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_TOKEN );
		delete_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_EXPIRES );

		wp_send_json_success(
			array(
				'message' => __( 'Senha alterada com sucesso! Você já pode fazer login.', 'apollo-login' ),
			)
		);
	}

	/**
	 * Send password reset email
	 *
	 * @param \WP_User $user  User object.
	 * @param string   $token Reset token.
	 * @return void
	 */
	private function send_reset_email( \WP_User $user, string $token ): void {
		$social_name = get_user_meta( $user->ID, APOLLO_META_SOCIAL_NAME, true ) ?: $user->display_name;

		$reset_url = add_query_arg(
			array(
				'token'   => $token,
				'user_id' => $user->ID,
			),
			home_url( '/' . APOLLO_LOGIN_PAGE_RESET . '/' )
		);

		$subject = __( 'Apollo::Rio — Resetar Senha', 'apollo-login' );
		$message = sprintf(
			/* translators: 1: User name, 2: Reset URL */
			__( "Olá %1\$s,\n\nVocê solicitou a recuperação de senha da sua conta Apollo::Rio.\n\nClique no link abaixo para criar uma nova senha:\n\n%2\$s\n\nEste link expira em 1 hora.\n\nSe você não solicitou esta recuperação, ignore este e-mail. Sua conta permanecerá segura.\n\n— Equipe Apollo::Rio", 'apollo-login' ),
			$social_name,
			$reset_url
		);

		wp_mail(
			$user->user_email,
			$subject,
			$message,
			array( 'Content-Type: text/plain; charset=UTF-8' )
		);
	}
}
