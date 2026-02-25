<?php
/**
 * Rating Handler Component
 *
 * Manages user-to-user ratings (sexy, legal, confiável).
 * Each user can rate another user ONCE per category.
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RatingHandler {

	public function __construct() {
		add_action( 'wp_ajax_apollo_submit_rating', [ $this, 'ajax_submit_rating' ] );
		add_action( 'wp_ajax_apollo_get_ratings', [ $this, 'ajax_get_ratings' ] );
	}

	/**
	 * AJAX: Submit a rating
	 */
	public function ajax_submit_rating(): void {
		check_ajax_referer( 'apollo_profile_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Você precisa estar logado.' ] );
		}

		$voter_id    = get_current_user_id();
		$target_id   = absint( $_POST['target_user_id'] ?? 0 );
		$category    = sanitize_text_field( $_POST['category'] ?? '' );
		$score       = absint( $_POST['score'] ?? 0 );

		// Validate
		if ( ! $target_id || ! get_userdata( $target_id ) ) {
			wp_send_json_error( [ 'message' => 'Usuário não encontrado.' ] );
		}

		if ( $voter_id === $target_id ) {
			wp_send_json_error( [ 'message' => 'Você não pode se avaliar.' ] );
		}

		$valid_categories = array_keys( APOLLO_USERS_RATING_CATEGORIES );
		if ( ! in_array( $category, $valid_categories, true ) ) {
			wp_send_json_error( [ 'message' => 'Categoria inválida.' ] );
		}

		if ( $score < 0 || $score > 3 ) {
			wp_send_json_error( [ 'message' => 'Pontuação inválida.' ] );
		}

		// Check if already voted in this category
		$existing = self::get_user_vote( $voter_id, $target_id, $category );

		global $wpdb;
		$table = $wpdb->prefix . APOLLO_USERS_TABLE_RATINGS;

		if ( $existing ) {
			// Update existing vote
			$wpdb->update(
				$table,
				[
					'score'      => $score,
					'updated_at' => current_time( 'mysql' ),
				],
				[
					'voter_id'  => $voter_id,
					'target_id' => $target_id,
					'category'  => $category,
				],
				[ '%d', '%s' ],
				[ '%d', '%d', '%s' ]
			);
		} else {
			// Insert new vote
			$wpdb->insert(
				$table,
				[
					'voter_id'   => $voter_id,
					'target_id'  => $target_id,
					'category'   => $category,
					'score'      => $score,
					'created_at' => current_time( 'mysql' ),
					'updated_at' => current_time( 'mysql' ),
				],
				[ '%d', '%d', '%s', '%d', '%s', '%s' ]
			);
		}

		// Return updated averages
		$averages = self::get_user_rating_averages( $target_id );

		wp_send_json_success( [
			'message'  => 'Avaliação registrada!',
			'averages' => $averages,
		] );
	}

	/**
	 * AJAX: Get ratings for a user
	 */
	public function ajax_get_ratings(): void {
		$target_id = absint( $_GET['target_user_id'] ?? $_POST['target_user_id'] ?? 0 );

		if ( ! $target_id ) {
			wp_send_json_error( [ 'message' => 'ID inválido.' ] );
		}

		$averages   = self::get_user_rating_averages( $target_id );
		$my_votes   = [];
		$voter_id   = get_current_user_id();

		if ( $voter_id && $voter_id !== $target_id ) {
			foreach ( array_keys( APOLLO_USERS_RATING_CATEGORIES ) as $cat ) {
				$vote = self::get_user_vote( $voter_id, $target_id, $cat );
				$my_votes[ $cat ] = $vote ? (int) $vote->score : 0;
			}
		}

		wp_send_json_success( [
			'averages' => $averages,
			'my_votes' => $my_votes,
		] );
	}

	/**
	 * Get a specific vote
	 */
	public static function get_user_vote( int $voter_id, int $target_id, string $category ): ?object {
		global $wpdb;
		$table = $wpdb->prefix . APOLLO_USERS_TABLE_RATINGS;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			return null;
		}

		return $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$table} WHERE voter_id = %d AND target_id = %d AND category = %s",
			$voter_id, $target_id, $category
		) );
	}

	/**
	 * Get average ratings for a user across all categories
	 */
	public static function get_user_rating_averages( int $target_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . APOLLO_USERS_TABLE_RATINGS;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			return self::empty_averages();
		}

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT category, AVG(score) as avg_score, COUNT(*) as vote_count
			 FROM {$table}
			 WHERE target_id = %d AND score > 0
			 GROUP BY category",
			$target_id
		) );

		$averages = self::empty_averages();
		foreach ( $results as $row ) {
			if ( isset( $averages[ $row->category ] ) ) {
				$averages[ $row->category ] = [
					'avg'   => round( (float) $row->avg_score, 1 ),
					'count' => (int) $row->vote_count,
				];
			}
		}

		return $averages;
	}

	/**
	 * Get total vote count for a user
	 */
	public static function get_total_votes( int $target_id ): int {
		global $wpdb;
		$table = $wpdb->prefix . APOLLO_USERS_TABLE_RATINGS;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			return 0;
		}

		return (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(DISTINCT voter_id) FROM {$table} WHERE target_id = %d AND score > 0",
			$target_id
		) );
	}

	private static function empty_averages(): array {
		return [
			'sexy'      => [ 'avg' => 0, 'count' => 0 ],
			'legal'     => [ 'avg' => 0, 'count' => 0 ],
			'confiavel' => [ 'avg' => 0, 'count' => 0 ],
		];
	}
}
