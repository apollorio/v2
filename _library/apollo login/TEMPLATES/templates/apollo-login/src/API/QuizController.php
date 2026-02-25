<?php
/**
 * Quiz REST Controller
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuizController {

	public function register_routes(): void {
		$namespace = APOLLO_LOGIN_REST_NAMESPACE;

		register_rest_route( $namespace, '/quiz/submit', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'submit' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/quiz/questions', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'questions' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/simon/submit', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'simon_submit' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/simon/highscores', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'simon_highscores' ],
			'permission_callback' => '__return_true',
		]);
	}

	public function questions( \WP_REST_Request $request ): \WP_REST_Response {
		$handler = new \Apollo\Login\Quiz\QuizHandler();
		// Reuse the AJAX handler logic
		return new \WP_REST_Response([
			'stages' => 4,
			'tests'  => [ 'pattern_recognition', 'simon_memory_game', 'ethics_respect_quiz', 'reaction_test' ],
		], 200 );
	}

	public function submit( \WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response([ 'message' => 'Use AJAX endpoint for quiz submission' ], 200 );
	}

	public function simon_submit( \WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response([ 'message' => 'Use AJAX endpoint' ], 200 );
	}

	public function simon_highscores( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$table  = $wpdb->prefix . APOLLO_LOGIN_TABLE_SIMON_SCORES;
		$scores = $wpdb->get_results(
			"SELECT user_id, MAX(score) as best_score, MAX(max_level) as best_level
			 FROM {$table}
			 GROUP BY user_id
			 ORDER BY best_score DESC
			 LIMIT 20"
		);

		return new \WP_REST_Response([ 'highscores' => $scores ], 200 );
	}
}
