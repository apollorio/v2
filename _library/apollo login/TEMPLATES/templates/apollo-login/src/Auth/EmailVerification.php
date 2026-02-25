<?php
/**
 * Email Verification Handler
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Auth;

use function Apollo\Login\apollo_login_generate_token;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EmailVerification {

	public function __construct() {
		add_action( 'wp_ajax_apollo_verify_email', [ $this, 'handle_verify' ] );
		add_action( 'wp_ajax_nopriv_apollo_verify_email', [ $this, 'handle_verify' ] );
		add_action( 'wp_ajax_apollo_resend_verification', [ $this, 'handle_resend' ] );
		add_action( 'wp_ajax_nopriv_apollo_resend_verification', [ $this, 'handle_resend' ] );
		add_action( 'template_redirect', [ $this, 'handle_verify_link' ] );
	}

	public function handle_verify_link(): void {
		if ( get_query_var( 'apollo_page' ) !== 'verificar-email' ) {
			return;
		}

		$token   = sanitize_text_field( $_GET['token'] ?? '' );
		$user_id = (int) ( $_GET['user_id'] ?? 0 );

		if ( $token && $user_id ) {
			$stored_token = get_user_meta( $user_id, APOLLO_META_VERIFICATION_TOKEN, true );

			if ( $stored_token && hash_equals( $stored_token, $token ) ) {
				update_user_meta( $user_id, APOLLO_META_EMAIL_VERIFIED, true );
				delete_user_meta( $user_id, APOLLO_META_VERIFICATION_TOKEN );

				do_action( 'apollo_login_email_verified', $user_id );
			}
		}
	}

	public function handle_verify(): void {
		$token   = sanitize_text_field( $_POST['token'] ?? '' );
		$user_id = (int) ( $_POST['user_id'] ?? 0 );

		if ( ! $token || ! $user_id ) {
			wp_send_json_error([ 'message' => __( 'Dados incompletos.', 'apollo-login' ) ], 400 );
		}

		$stored_token = get_user_meta( $user_id, APOLLO_META_VERIFICATION_TOKEN, true );

		if ( ! $stored_token || ! hash_equals( $stored_token, $token ) ) {
			wp_send_json_error([ 'message' => __( 'Token inválido.', 'apollo-login' ) ], 400 );
		}

		update_user_meta( $user_id, APOLLO_META_EMAIL_VERIFIED, true );
		delete_user_meta( $user_id, APOLLO_META_VERIFICATION_TOKEN );

		do_action( 'apollo_login_email_verified', $user_id );

		wp_send_json_success([
			'message' => __( 'E-mail verificado com sucesso!', 'apollo-login' ),
		]);
	}

	public function handle_resend(): void {
		if ( ! check_ajax_referer( 'apollo_auth_nonce', 'nonce', false ) ) {
			wp_send_json_error([ 'message' => __( 'Verificação falhou.', 'apollo-login' ) ], 403 );
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			$user_id = (int) ( $_POST['user_id'] ?? 0 );
		}

		if ( ! $user_id ) {
			wp_send_json_error([ 'message' => __( 'Usuário não encontrado.', 'apollo-login' ) ], 400 );
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			wp_send_json_error([ 'message' => __( 'Usuário não encontrado.', 'apollo-login' ) ], 400 );
		}

		$token = apollo_login_generate_token();
		update_user_meta( $user_id, APOLLO_META_VERIFICATION_TOKEN, $token );

		$verify_url = add_query_arg([
			'token'   => $token,
			'user_id' => $user_id,
		], home_url( '/' . APOLLO_LOGIN_PAGE_VERIFY . '/' ) );

		$social_name = get_user_meta( $user_id, APOLLO_META_SOCIAL_NAME, true ) ?: $user->display_name;

		wp_mail(
			$user->user_email,
			__( 'Apollo::Rio — Confirme seu e-mail', 'apollo-login' ),
			sprintf( __( "Olá %1\$s,\n\nConfirme seu e-mail:\n%2\$s\n\n— Apollo::Rio", 'apollo-login' ), $social_name, $verify_url ),
			[ 'Content-Type: text/plain; charset=UTF-8' ]
		);

		wp_send_json_success([
			'message' => __( 'E-mail de verificação reenviado.', 'apollo-login' ),
		]);
	}
}
