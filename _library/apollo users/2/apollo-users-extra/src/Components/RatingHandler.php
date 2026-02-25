<?php
/**
 * Rating Handler Component
 *
 * Anonymous user-to-user voting system.
 * Each logged-in user can vote ONCE per category per target user.
 * Public view: aggregated averages only (no voter identity revealed).
 * Admin view: full voter list + ability to adjust votes.
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RatingHandler {

	/**
	 * Rating categories with icons and colors
	 */
	public const CATEGORIES = [
		'sexy'      => [ 'icon' => 'ri-heart-3-fill',   'label' => 'Sexy',      'color' => '#f45f00' ],
		'legal'     => [ 'icon' => 'ri-user-smile-line', 'label' => 'Legal',     'color' => '#22c55e' ],
		'confiavel' => [ 'icon' => 'ri-instance-line',   'label' => 'Confiável', 'color' => '#3b82f6' ],
	];

	/**
	 * Max score per category (number of icons)
	 */
	public const MAX_SCORE = 3;

	public function __construct() {
		// Public AJAX (logged-in users)
		add_action( 'wp_ajax_apollo_submit_rating',  [ $this, 'ajax_submit_rating' ] );
		add_action( 'wp_ajax_apollo_get_ratings',    [ $this, 'ajax_get_ratings' ] );

		// Admin AJAX
		add_action( 'wp_ajax_apollo_admin_get_voters',    [ $this, 'ajax_admin_get_voters' ] );
		add_action( 'wp_ajax_apollo_admin_adjust_rating', [ $this, 'ajax_admin_adjust_rating' ] );
		add_action( 'wp_ajax_apollo_admin_delete_rating', [ $this, 'ajax_admin_delete_rating' ] );
	}

	// ─── PUBLIC ─────────────────────────────────────────────────────────

	/**
	 * Submit or update a vote.
	 * One vote per voter per target per category (UPSERT via UNIQUE KEY).
	 */
	public function ajax_submit_rating(): void {
		check_ajax_referer( 'apollo_profile_nonce', 'nonce' );

		$voter_id  = get_current_user_id();
		$target_id = absint( $_POST['target_id'] ?? 0 );
		$category  = sanitize_text_field( $_POST['category'] ?? '' );
		$score     = absint( $_POST['score'] ?? 0 );

		// Validation
		if ( ! $voter_id ) {
			wp_send_json_error( [ 'message' => 'Login required.' ] );
		}
		if ( $voter_id === $target_id ) {
			wp_send_json_error( [ 'message' => 'Você não pode votar em si mesmo.' ] );
		}
		if ( ! array_key_exists( $category, self::CATEGORIES ) ) {
			wp_send_json_error( [ 'message' => 'Categoria inválida.' ] );
		}
		if ( $score < 0 || $score > self::MAX_SCORE ) {
			wp_send_json_error( [ 'message' => 'Pontuação inválida.' ] );
		}
		if ( ! get_userdata( $target_id ) ) {
			wp_send_json_error( [ 'message' => 'Usuário não encontrado.' ] );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'apollo_user_ratings';

		// UPSERT: INSERT ... ON DUPLICATE KEY UPDATE
		$wpdb->query( $wpdb->prepare(
			"INSERT INTO {$table} (voter_id, target_id, category, score, created_at, updated_at)
			 VALUES (%d, %d, %s, %d, NOW(), NOW())
			 ON DUPLICATE KEY UPDATE score = %d, updated_at = NOW()",
			$voter_id, $target_id, $category, $score, $score
		) );

		// Return updated averages
		wp_send_json_success( [
			'averages'   => self::get_averages( $target_id ),
			'user_votes' => self::get_user_votes( $voter_id, $target_id ),
		] );
	}

	/**
	 * Get ratings for a user (public — no voter identities).
	 */
	public function ajax_get_ratings(): void {
		$target_id = absint( $_GET['target_id'] ?? $_POST['target_id'] ?? 0 );
		if ( ! $target_id ) {
			wp_send_json_error( [ 'message' => 'target_id required.' ] );
		}

		$voter_id = get_current_user_id(); // 0 if guest

		wp_send_json_success( [
			'averages'    => self::get_averages( $target_id ),
			'total_votes' => self::get_total_voters( $target_id ),
			'user_votes'  => $voter_id ? self::get_user_votes( $voter_id, $target_id ) : [],
		] );
	}

	// ─── ADMIN ──────────────────────────────────────────────────────────

	/**
	 * Admin: Get full voter list for a target user.
	 */
	public function ajax_admin_get_voters(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Unauthorized.' ] );
		}

		$target_id = absint( $_GET['target_id'] ?? $_POST['target_id'] ?? 0 );
		if ( ! $target_id ) {
			wp_send_json_error( [ 'message' => 'target_id required.' ] );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'apollo_user_ratings';

		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT r.id, r.voter_id, r.category, r.score, r.created_at, r.updated_at,
			        u.user_login, u.display_name
			 FROM {$table} r
			 LEFT JOIN {$wpdb->users} u ON r.voter_id = u.ID
			 WHERE r.target_id = %d
			 ORDER BY r.updated_at DESC",
			$target_id
		), ARRAY_A );

		wp_send_json_success( [
			'voters'   => $rows,
			'averages' => self::get_averages( $target_id ),
		] );
	}

	/**
	 * Admin: Adjust a specific vote.
	 */
	public function ajax_admin_adjust_rating(): void {
		check_ajax_referer( 'apollo_admin_rating_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Unauthorized.' ] );
		}

		$rating_id = absint( $_POST['rating_id'] ?? 0 );
		$new_score = absint( $_POST['score'] ?? 0 );

		if ( ! $rating_id || $new_score > self::MAX_SCORE ) {
			wp_send_json_error( [ 'message' => 'Invalid parameters.' ] );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'apollo_user_ratings';

		$wpdb->update(
			$table,
			[ 'score' => $new_score, 'updated_at' => current_time( 'mysql' ) ],
			[ 'id' => $rating_id ],
			[ '%d', '%s' ],
			[ '%d' ]
		);

		wp_send_json_success( [ 'message' => 'Vote updated.' ] );
	}

	/**
	 * Admin: Delete a specific vote (hate attack removal).
	 */
	public function ajax_admin_delete_rating(): void {
		check_ajax_referer( 'apollo_admin_rating_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Unauthorized.' ] );
		}

		$rating_id = absint( $_POST['rating_id'] ?? 0 );
		if ( ! $rating_id ) {
			wp_send_json_error( [ 'message' => 'rating_id required.' ] );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'apollo_user_ratings';

		$wpdb->delete( $table, [ 'id' => $rating_id ], [ '%d' ] );

		wp_send_json_success( [ 'message' => 'Vote removed.' ] );
	}

	// ─── STATIC HELPERS (used in templates) ─────────────────────────────

	/**
	 * Get averages per category for a target user.
	 *
	 * @return array [ 'sexy' => 2.3, 'legal' => 1.0, 'confiavel' => 0 ]
	 */
	public static function get_averages( int $target_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_user_ratings';

		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT category, AVG(score) as avg_score, COUNT(*) as vote_count
			 FROM {$table}
			 WHERE target_id = %d
			 GROUP BY category",
			$target_id
		), ARRAY_A );

		$averages = [];
		foreach ( self::CATEGORIES as $key => $meta ) {
			$averages[ $key ] = 0;
		}
		foreach ( $rows as $row ) {
			if ( isset( $averages[ $row['category'] ] ) ) {
				$averages[ $row['category'] ] = round( (float) $row['avg_score'], 1 );
			}
		}

		return $averages;
	}

	/**
	 * Get a specific voter's votes for a target user.
	 *
	 * @return array [ 'sexy' => 2, 'legal' => 0, 'confiavel' => 1 ]
	 */
	public static function get_user_votes( int $voter_id, int $target_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_user_ratings';

		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT category, score FROM {$table}
			 WHERE voter_id = %d AND target_id = %d",
			$voter_id, $target_id
		), ARRAY_A );

		$votes = [];
		foreach ( self::CATEGORIES as $key => $meta ) {
			$votes[ $key ] = 0;
		}
		foreach ( $rows as $row ) {
			if ( isset( $votes[ $row['category'] ] ) ) {
				$votes[ $row['category'] ] = (int) $row['score'];
			}
		}

		return $votes;
	}

	/**
	 * Get total unique voters for a target user.
	 */
	public static function get_total_voters( int $target_id ): int {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_user_ratings';

		return (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(DISTINCT voter_id) FROM {$table} WHERE target_id = %d",
			$target_id
		) );
	}
}
