<?php
/**
 * Quiz REST Controller
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\API;

use Apollo\Login\Quiz\QuizManager;
use Apollo\Login\Quiz\SimonGame;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Quiz Controller class
 */
class QuizController extends WP_REST_Controller {

	/**
	 * Namespace
	 *
	 * @var string
	 */
	protected $namespace = APOLLO_LOGIN_REST_NAMESPACE;

	/**
	 * Register routes
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// GET /quiz/questions
		register_rest_route(
			$this->namespace,
			'/quiz/questions',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_questions' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'stage' => array(
						'required' => true,
						'type'     => 'string',
						'enum'     => array( 'pattern', 'ethics', 'reaction' ),
					),
				),
			)
		);

		// POST /quiz/submit
		register_rest_route(
			$this->namespace,
			'/quiz/submit',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'submit_quiz' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'stage'   => array(
						'required' => true,
						'type'     => 'string',
					),
					'answers' => array(
						'required' => true,
						'type'     => 'array',
					),
					'token'   => array(
						'required' => false,
						'type'     => 'string',
					),
				),
			)
		);

		// POST /simon/submit
		register_rest_route(
			$this->namespace,
			'/simon/submit',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'submit_simon' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'level'    => array(
						'required' => true,
						'type'     => 'integer',
					),
					'sequence' => array(
						'required' => true,
						'type'     => 'array',
					),
					'success'  => array(
						'required' => true,
						'type'     => 'boolean',
					),
					'token'    => array(
						'required' => false,
						'type'     => 'string',
					),
				),
			)
		);

		// GET /simon/highscores
		register_rest_route(
			$this->namespace,
			'/simon/highscores',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_simon_highscores' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'limit' => array(
						'required' => false,
						'type'     => 'integer',
						'default'  => 10,
					),
				),
			)
		);
	}

	/**
	 * Get quiz questions
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_questions( WP_REST_Request $request ): WP_REST_Response {
		$stage     = $request->get_param( 'stage' );
		$questions = QuizManager::get_questions( $stage );

		return new WP_REST_Response(
			array(
				'stage'     => $stage,
				'questions' => $questions,
			),
			200
		);
	}

	/**
	 * Submit quiz answers
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function submit_quiz( WP_REST_Request $request ): WP_REST_Response {
		$stage   = $request->get_param( 'stage' );
		$answers = $request->get_param( 'answers' );
		$token   = $request->get_param( 'token' ) ?: wp_generate_password( 32, false );

		// Calculate score
		$score = $this->calculate_score( $stage, $answers );

		// Save to transient (60 minutes)
		$quiz_data           = get_transient( 'apollo_quiz_' . $token ) ?: array();
		$quiz_data[ $stage ] = array(
			'score'   => $score,
			'answers' => $answers,
		);
		set_transient( 'apollo_quiz_' . $token, $quiz_data, 60 * MINUTE_IN_SECONDS );

		return new WP_REST_Response(
			array(
				'success' => true,
				'stage'   => $stage,
				'score'   => $score,
				'token'   => $token,
			),
			200
		);
	}

	/**
	 * Submit Simon game result
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function submit_simon( WP_REST_Request $request ): WP_REST_Response {
		$level    = $request->get_param( 'level' );
		$sequence = $request->get_param( 'sequence' );
		$success  = $request->get_param( 'success' );
		$token    = $request->get_param( 'token' ) ?: wp_generate_password( 32, false );

		// Score based on level and success
		$score = $success ? $level * 25 : 0;

		// Save to transient
		$quiz_data          = get_transient( 'apollo_quiz_' . $token ) ?: array();
		$quiz_data['simon'] = array(
			'score'    => $score,
			'level'    => $level,
			'sequence' => $sequence,
			'success'  => $success,
		);
		set_transient( 'apollo_quiz_' . $token, $quiz_data, 60 * MINUTE_IN_SECONDS );

		return new WP_REST_Response(
			array(
				'success' => true,
				'level'   => $level,
				'score'   => $score,
				'token'   => $token,
			),
			200
		);
	}

	/**
	 * Get Simon highscores
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_simon_highscores( WP_REST_Request $request ): WP_REST_Response {
		$limit  = $request->get_param( 'limit' );
		$scores = SimonGame::get_leaderboard( $limit );

		return new WP_REST_Response(
			array(
				'scores' => $scores,
			),
			200
		);
	}

	/**
	 * Calculate quiz score
	 *
	 * @param string $stage   Stage name.
	 * @param array  $answers User answers.
	 * @return int
	 */
	private function calculate_score( string $stage, array $answers ): int {
		$questions = QuizManager::get_questions( $stage );
		$score     = 0;

		foreach ( $questions as $question ) {
			$user_answer = $answers[ $question['id'] ] ?? null;

			if ( $user_answer === $question['correct'] ) {
				$weight = $question['weight'] ?? 10;
				$score += $weight;
			}
		}

		return $score;
	}
}
