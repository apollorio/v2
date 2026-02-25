<?php
/**
 * Depoimento Handler Component
 *
 * Uses WordPress $comment system for user testimonials.
 * comment_type = 'apollo_depoimento'
 * comment_post_ID = 0 (not tied to a post)
 * comment_parent = target user ID (the user being reviewed)
 * comment_author = voter display name
 * user_id = voter user ID
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DepoimentoHandler {

	public function __construct() {
		add_action( 'wp_ajax_apollo_submit_depoimento', [ $this, 'ajax_submit' ] );
		add_action( 'wp_ajax_apollo_delete_depoimento', [ $this, 'ajax_delete' ] );
		add_action( 'wp_ajax_apollo_get_depoimentos', [ $this, 'ajax_get' ] );
		add_action( 'wp_ajax_nopriv_apollo_get_depoimentos', [ $this, 'ajax_get' ] );

		// Exclude depoimentos from regular comment queries
		add_action( 'pre_get_comments', [ $this, 'exclude_from_queries' ] );
	}

	/**
	 * AJAX: Submit a depoimento
	 */
	public function ajax_submit(): void {
		check_ajax_referer( 'apollo_profile_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Você precisa estar logado.' ] );
		}

		$author_id  = get_current_user_id();
		$target_id  = absint( $_POST['target_user_id'] ?? 0 );
		$content    = sanitize_textarea_field( $_POST['content'] ?? '' );

		if ( ! $target_id || ! get_userdata( $target_id ) ) {
			wp_send_json_error( [ 'message' => 'Usuário não encontrado.' ] );
		}

		if ( $author_id === $target_id ) {
			wp_send_json_error( [ 'message' => 'Você não pode escrever um depoimento para si mesmo.' ] );
		}

		if ( empty( $content ) || mb_strlen( $content ) < 10 ) {
			wp_send_json_error( [ 'message' => 'Depoimento muito curto (mínimo 10 caracteres).' ] );
		}

		if ( mb_strlen( $content ) > 1000 ) {
			wp_send_json_error( [ 'message' => 'Depoimento muito longo (máximo 1000 caracteres).' ] );
		}

		// Check if user already left a depoimento for this target
		$existing = self::get_user_depoimento( $author_id, $target_id );
		if ( $existing ) {
			wp_send_json_error( [ 'message' => 'Você já deixou um depoimento para este usuário.' ] );
		}

		$author = get_userdata( $author_id );

		$comment_id = wp_insert_comment( [
			'comment_post_ID'  => 0,
			'comment_author'   => $author->display_name,
			'comment_author_email' => $author->user_email,
			'comment_content'  => $content,
			'comment_type'     => APOLLO_USERS_DEPOIMENTO_TYPE,
			'comment_parent'   => $target_id, // Stores target user ID
			'user_id'          => $author_id,
			'comment_approved' => 1,
			'comment_date'     => current_time( 'mysql' ),
		] );

		if ( ! $comment_id ) {
			wp_send_json_error( [ 'message' => 'Erro ao salvar depoimento.' ] );
		}

		wp_send_json_success( [
			'message'     => 'Depoimento publicado!',
			'depoimento'  => self::format_depoimento( get_comment( $comment_id ) ),
		] );
	}

	/**
	 * AJAX: Delete own depoimento
	 */
	public function ajax_delete(): void {
		check_ajax_referer( 'apollo_profile_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Você precisa estar logado.' ] );
		}

		$comment_id = absint( $_POST['comment_id'] ?? 0 );
		$comment    = get_comment( $comment_id );

		if ( ! $comment || $comment->comment_type !== APOLLO_USERS_DEPOIMENTO_TYPE ) {
			wp_send_json_error( [ 'message' => 'Depoimento não encontrado.' ] );
		}

		$user_id = get_current_user_id();

		// Only the author, the target user, or admins can delete
		$can_delete = (
			(int) $comment->user_id === $user_id ||
			(int) $comment->comment_parent === $user_id ||
			current_user_can( 'manage_options' )
		);

		if ( ! $can_delete ) {
			wp_send_json_error( [ 'message' => 'Sem permissão.' ] );
		}

		wp_delete_comment( $comment_id, true );

		wp_send_json_success( [ 'message' => 'Depoimento removido.' ] );
	}

	/**
	 * AJAX: Get depoimentos for a user
	 */
	public function ajax_get(): void {
		$target_id = absint( $_GET['target_user_id'] ?? $_POST['target_user_id'] ?? 0 );
		$page      = max( 1, absint( $_GET['page'] ?? 1 ) );
		$per_page  = 10;

		if ( ! $target_id ) {
			wp_send_json_error( [ 'message' => 'ID inválido.' ] );
		}

		$depoimentos = self::get_depoimentos( $target_id, $page, $per_page );
		$total        = self::count_depoimentos( $target_id );

		wp_send_json_success( [
			'depoimentos' => $depoimentos,
			'total'       => $total,
			'pages'       => ceil( $total / $per_page ),
			'page'        => $page,
		] );
	}

	/**
	 * Get depoimentos for a target user
	 */
	public static function get_depoimentos( int $target_id, int $page = 1, int $per_page = 10 ): array {
		$comments = get_comments( [
			'type'    => APOLLO_USERS_DEPOIMENTO_TYPE,
			'parent'  => $target_id,
			'status'  => 'approve',
			'number'  => $per_page,
			'offset'  => ( $page - 1 ) * $per_page,
			'orderby' => 'comment_date',
			'order'   => 'DESC',
		] );

		return array_map( [ self::class, 'format_depoimento' ], $comments );
	}

	/**
	 * Count depoimentos for a user
	 */
	public static function count_depoimentos( int $target_id ): int {
		return (int) get_comments( [
			'type'    => APOLLO_USERS_DEPOIMENTO_TYPE,
			'parent'  => $target_id,
			'status'  => 'approve',
			'count'   => true,
		] );
	}

	/**
	 * Check if user already left a depoimento
	 */
	public static function get_user_depoimento( int $author_id, int $target_id ): ?object {
		$comments = get_comments( [
			'type'    => APOLLO_USERS_DEPOIMENTO_TYPE,
			'parent'  => $target_id,
			'user_id' => $author_id,
			'number'  => 1,
		] );

		return ! empty( $comments ) ? $comments[0] : null;
	}

	/**
	 * Format a depoimento for JSON response
	 */
	public static function format_depoimento( $comment ): array {
		$author_id = (int) $comment->user_id;
		$author    = get_userdata( $author_id );

		$avatar_url = '';
		if ( function_exists( 'Apollo\Users\apollo_get_user_avatar_url' ) ) {
			$avatar_url = \Apollo\Users\apollo_get_user_avatar_url( $author_id, 'thumb' );
		} else {
			$avatar_url = get_avatar_url( $author_id, [ 'size' => 96 ] );
		}

		$membership = '';
		if ( function_exists( 'Apollo\Users\apollo_get_user_membership' ) ) {
			$badge = \Apollo\Users\apollo_get_user_membership( $author_id );
			$membership = $badge['label'] ?? '';
		}

		return [
			'id'          => (int) $comment->comment_ID,
			'author_id'   => $author_id,
			'author_name' => $author ? $author->display_name : $comment->comment_author,
			'author_login' => $author ? $author->user_login : '',
			'avatar_url'  => $avatar_url,
			'membership'  => $membership,
			'content'     => wp_kses_post( $comment->comment_content ),
			'date'        => $comment->comment_date,
			'date_human'  => human_time_diff( strtotime( $comment->comment_date ), current_time( 'timestamp' ) ) . ' atrás',
			'can_delete'  => (
				is_user_logged_in() && (
					get_current_user_id() === $author_id ||
					get_current_user_id() === (int) $comment->comment_parent ||
					current_user_can( 'manage_options' )
				)
			),
		];
	}

	/**
	 * Exclude depoimentos from normal comment queries
	 */
	public function exclude_from_queries( $query ): void {
		if ( is_admin() ) {
			return;
		}

		// Only exclude from queries that don't specifically ask for our type
		if ( ! isset( $query->query_vars['type'] ) || $query->query_vars['type'] !== APOLLO_USERS_DEPOIMENTO_TYPE ) {
			$query->query_vars['type__not_in'] = array_merge(
				$query->query_vars['type__not_in'] ?? [],
				[ APOLLO_USERS_DEPOIMENTO_TYPE ]
			);
		}
	}
}
