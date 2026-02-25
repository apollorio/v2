<?php
/**
 * Account Handler Component
 *
 * Handles /minha-conta page: account settings, password change,
 * privacy settings, account deletion.
 * Ported from UsersWP account patterns.
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AccountHandler {

	public function __construct() {
		// AJAX handlers
		add_action( 'wp_ajax_apollo_update_account', [ $this, 'ajax_update_account' ] );
		add_action( 'wp_ajax_apollo_change_password', [ $this, 'ajax_change_password' ] );
		add_action( 'wp_ajax_apollo_update_privacy', [ $this, 'ajax_update_privacy' ] );
		add_action( 'wp_ajax_apollo_delete_account', [ $this, 'ajax_delete_account' ] );
	}

	/**
	 * AJAX: Update account info
	 */
	public function ajax_update_account(): void {
		check_ajax_referer( 'apollo_account_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Você precisa estar logado.' ] );
		}

		$user_id = get_current_user_id();
		$user    = get_userdata( $user_id );

		// Update email if changed
		$new_email = sanitize_email( $_POST['email'] ?? '' );
		if ( $new_email && $new_email !== $user->user_email ) {
			if ( ! is_email( $new_email ) ) {
				wp_send_json_error( [ 'message' => 'E-mail inválido.' ] );
			}
			if ( email_exists( $new_email ) && email_exists( $new_email ) !== $user_id ) {
				wp_send_json_error( [ 'message' => 'Este e-mail já está em uso.' ] );
			}
			wp_update_user( [
				'ID'         => $user_id,
				'user_email' => $new_email,
			] );
		}

		// Update meta fields
		$fields = [
			'_apollo_social_name' => sanitize_text_field( $_POST['social_name'] ?? '' ),
			'_apollo_bio'         => sanitize_textarea_field( $_POST['bio'] ?? '' ),
			'_apollo_website'     => esc_url_raw( $_POST['website'] ?? '' ),
			'_apollo_phone'       => sanitize_text_field( $_POST['phone'] ?? '' ),
			'user_location'       => sanitize_text_field( $_POST['location'] ?? '' ),
		];

		foreach ( $fields as $key => $value ) {
			update_user_meta( $user_id, $key, $value );
		}

		// Update display_name
		if ( ! empty( $fields['_apollo_social_name'] ) ) {
			wp_update_user( [
				'ID'           => $user_id,
				'display_name' => $fields['_apollo_social_name'],
			] );
		}

		wp_send_json_success( [ 'message' => 'Conta atualizada com sucesso!' ] );
	}

	/**
	 * AJAX: Change password (UsersWP pattern)
	 */
	public function ajax_change_password(): void {
		check_ajax_referer( 'apollo_account_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Você precisa estar logado.' ] );
		}

		$user_id         = get_current_user_id();
		$user            = get_userdata( $user_id );
		$current_pass    = $_POST['current_password'] ?? '';
		$new_pass        = $_POST['new_password'] ?? '';
		$confirm_pass    = $_POST['confirm_password'] ?? '';

		// Verify current password
		if ( ! wp_check_password( $current_pass, $user->user_pass, $user_id ) ) {
			wp_send_json_error( [ 'message' => 'Senha atual incorreta.' ] );
		}

		// Validate new password
		if ( strlen( $new_pass ) < 8 ) {
			wp_send_json_error( [ 'message' => 'A nova senha deve ter pelo menos 8 caracteres.' ] );
		}

		if ( $new_pass !== $confirm_pass ) {
			wp_send_json_error( [ 'message' => 'As senhas não coincidem.' ] );
		}

		// Update password
		wp_set_password( $new_pass, $user_id );

		// Re-login the user
		wp_set_auth_cookie( $user_id );
		wp_set_current_user( $user_id );

		wp_send_json_success( [ 'message' => 'Senha alterada com sucesso!' ] );
	}

	/**
	 * AJAX: Update privacy settings
	 */
	public function ajax_update_privacy(): void {
		check_ajax_referer( 'apollo_account_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Você precisa estar logado.' ] );
		}

		$user_id = get_current_user_id();

		$privacy_profile = sanitize_text_field( $_POST['privacy_profile'] ?? 'public' );
		if ( ! in_array( $privacy_profile, [ 'public', 'members', 'private' ], true ) ) {
			$privacy_profile = 'public';
		}

		update_user_meta( $user_id, '_apollo_privacy_profile', $privacy_profile );
		update_user_meta( $user_id, '_apollo_privacy_email', ! empty( $_POST['privacy_email'] ) );
		update_user_meta( $user_id, '_apollo_disable_author_url', ! empty( $_POST['disable_author_url'] ) );

		wp_send_json_success( [ 'message' => 'Privacidade atualizada!' ] );
	}

	/**
	 * AJAX: Delete account (UsersWP pattern - requires password confirmation)
	 */
	public function ajax_delete_account(): void {
		check_ajax_referer( 'apollo_account_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Você precisa estar logado.' ] );
		}

		$user_id  = get_current_user_id();
		$user     = get_userdata( $user_id );
		$password = $_POST['password'] ?? '';

		// Admins cannot delete themselves
		if ( current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Administradores não podem excluir suas contas por aqui.' ] );
		}

		// Verify password
		if ( ! wp_check_password( $password, $user->user_pass, $user_id ) ) {
			wp_send_json_error( [ 'message' => 'Senha incorreta.' ] );
		}

		// Fire hook before deletion
		do_action( 'apollo_users_before_account_delete', $user_id );

		require_once ABSPATH . 'wp-admin/includes/user.php';

		// Delete user and reassign content to admin
		$deleted = wp_delete_user( $user_id, 1 );

		if ( $deleted ) {
			do_action( 'apollo_users_after_account_delete', $user_id );
			wp_logout();
			wp_send_json_success( [
				'message'  => 'Conta excluída com sucesso.',
				'redirect' => home_url( '/' ),
			] );
		} else {
			wp_send_json_error( [ 'message' => 'Erro ao excluir conta.' ] );
		}
	}
}
