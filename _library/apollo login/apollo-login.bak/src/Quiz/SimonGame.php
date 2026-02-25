<?php
/**
 * Simon Memory Game - 4 levels MANDATORY
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Quiz;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Simon Game class
 */
class SimonGame {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue Simon game assets
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		// Only on register page
		if ( get_query_var( 'apollo_login_page' ) !== 'register' ) {
			return;
		}

		wp_enqueue_style(
			'apollo-simon',
			APOLLO_LOGIN_URL . 'assets/css/simon.css',
			array(),
			APOLLO_LOGIN_VERSION
		);

		wp_enqueue_script(
			'apollo-simon',
			APOLLO_LOGIN_URL . 'assets/js/simon.js',
			array( 'jquery' ),
			APOLLO_LOGIN_VERSION,
			true
		);

		wp_localize_script(
			'apollo-simon',
			'apolloSimon',
			array(
				'levels'   => APOLLO_LOGIN_SIMON_LEVELS,
				'colors'   => array( 'red', 'green', 'blue', 'yellow' ),
				'restUrl'  => rest_url( APOLLO_LOGIN_REST_NAMESPACE ),
				'nonce'    => wp_create_nonce( 'apollo_simon' ),
			)
		);
	}

	/**
	 * Save Simon score
	 *
	 * @param int   $user_id  User ID.
	 * @param int   $level    Level (1-4).
	 * @param array $sequence Color sequence.
	 * @param bool  $success  Success status.
	 * @return bool
	 */
	public static function save_score( int $user_id, int $level, array $sequence, bool $success ): bool {
		global $wpdb;

		$table = $wpdb->prefix . \APOLLO_LOGIN_TABLE_SIMON_SCORES;

		$result = $wpdb->insert(
			$table,
			array(
				'user_id'    => $user_id,
				'level'      => $level,
				'sequence'   => wp_json_encode( $sequence ),
				'success'    => $success ? 1 : 0,
				'attempts'   => 1,
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%d', '%s', '%d', '%d', '%s' )
		);

		// Update user meta with highest level
		if ( $success ) {
			$current_high = (int) get_user_meta( $user_id, '_apollo_simon_highscore', true );
			if ( $level > $current_high ) {
				update_user_meta( $user_id, '_apollo_simon_highscore', $level );
			}
		}

		return (bool) $result;
	}

	/**
	 * Get leaderboard
	 *
	 * @param int $limit Limit.
	 * @return array
	 */
	public static function get_leaderboard( int $limit = 10 ): array {
		global $wpdb;

		$table = $wpdb->prefix . \APOLLO_LOGIN_TABLE_SIMON_SCORES;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id, MAX(level) as highest_level, COUNT(*) as total_games
				FROM {$table}
				WHERE success = 1
				GROUP BY user_id
				ORDER BY highest_level DESC, total_games ASC
				LIMIT %d",
				$limit
			)
		);

		return $results ?: array();
	}
}
