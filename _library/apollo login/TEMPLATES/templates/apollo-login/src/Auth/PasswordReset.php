<?php
/**
 * Password Reset Handler
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Auth;

use function Apollo\Login\apollo_login_generate_token;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PasswordReset {

	public function __construct() {
		add_action( 'wp_ajax_nopriv_apollo_reset_request', [ $this, 'handle_reset_request' ] );
		add_action( 'wp_ajax_nopriv_apollo_reset_confirm', [ $this, 'handle_reset_confirm' ] );
	}

	public function handle_reset_request(): void {
		if ( ! check_ajax_referer( 'apollo_auth_nonce', 'nonce', false ) ) {
			wp_send_json_error([ 'message' => __( 'Verificação de segurança falhou.', 'apollo-login' ) ], 403 );
		}

		$email = sanitize_email( $_POST['email'] ?? '' );

		if ( empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error([ 'message' => __( 'E-mail inválido.', 'apollo-login' ) ], 400 );
		}

		// Always return success to prevent user enumeration
		$user = get_user_by( 'email', $email );

		if ( $user ) {
			$token   = apollo_login_generate_token( 32 );
			$expires = time() + 3600; // 1 hour

			update_user_meta( $user->ID, APOLLO_META_PASSWORD_RESET_TOKEN, wp_hash( $token ) );
			update_user_meta( $user->ID, APOLLO_META_PASSWORD_RESET_EXPIRES, $expires );

			$this->send_reset_email( $user, $token );
		}

		wp_send_json_success([
			'message' => __( 'Se o e-mail existir, você receberá instruções de recuperação.', 'apollo-login' ),
		]);
	}

	public function handle_reset_confirm(): void {
		if ( ! check_ajax_referer( 'apollo_auth_nonce', 'nonce', false ) ) {
			wp_send_json_error([ 'message' => __( 'Verificação de segurança falhou.', 'apollo-login' ) ], 403 );
		}

		$token       = sanitize_text_field( $_POST['token'] ?? '' );
		$user_id     = (int) ( $_POST['user_id'] ?? 0 );
		$new_password = $_POST['new_password'] ?? '';

		if ( empty( $token ) || ! $user_id || empty( $new_password ) ) {
			wp_send_json_error([ 'message' => __( 'Dados incompletos.', 'apollo-login' ) ], 400 );
		}

		if ( strlen( $new_password ) < 8 ) {
			wp_send_json_error([ 'message' => __( 'Senha deve ter pelo menos 8 caracteres.', 'apollo-login' ) ], 400 );
		}

		$stored_hash = get_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_TOKEN, true );
		$expires     = (int) get_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_EXPIRES, true );

		if ( ! $stored_hash || ! wp_check_password( $token, $stored_hash ) ) {
			wp_send_json_error([ 'message' => __( 'Token inválido ou expirado.', 'apollo-login' ) ], 400 );
		}

		if ( time() > $expires ) {
			delete_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_TOKEN );
			delete_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_EXPIRES );
			wp_send_json_error([ 'message' => __( 'Token expirado. Solicite um novo.', 'apollo-login' ) ], 400 );
		}

		// Reset password
		wp_set_password( $new_password, $user_id );

		// Clean up tokens
		delete_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_TOKEN );
		delete_user_meta( $user_id, APOLLO_META_PASSWORD_RESET_EXPIRES );

		wp_send_json_success([
			'message' => __( 'Senha alterada com sucesso!', 'apollo-login' ),
		]);
	}

	private function send_reset_email( \WP_User $user, string $token ): void {
		$social_name = get_user_meta( $user->ID, APOLLO_META_SOCIAL_NAME, true ) ?: $user->display_name;

		$reset_url = add_query_arg([
			'token'   => $token,
			'user_id' => $user->ID,
		], home_url( '/' . APOLLO_LOGIN_PAGE_RESET . '/' ) );

		$subject = __( 'Apollo::Rio — Resetar Senha', 'apollo-login' );
		$message = sprintf(
			__( "Olá %1\$s,\n\nClique no link abaixo para resetar sua senha:\n\n%2\$s\n\nEste link expira em 1 hora.\n\nSe você não solicitou, ignore este e-mail.\n\n— Apollo::Rio", 'apollo-login' ),
			$social_name,
			$reset_url
		);

		wp_mail( $user->user_email, $subject, $message, [ 'Content-Type: text/plain; charset=UTF-8' ] );
	}
}
