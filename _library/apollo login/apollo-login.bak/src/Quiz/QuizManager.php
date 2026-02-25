<?php
/**
 * Quiz Manager - Orchestrates 4-stage aptitude quiz
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
 * Quiz Manager class
 */
class QuizManager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue quiz assets
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		// Only on register page
		if ( get_query_var( 'apollo_login_page' ) !== 'register' ) {
			return;
		}

		wp_enqueue_style(
			'apollo-quiz',
			APOLLO_LOGIN_URL . 'assets/css/quiz.css',
			array(),
			APOLLO_LOGIN_VERSION
		);

		wp_enqueue_script(
			'apollo-quiz',
			APOLLO_LOGIN_URL . 'assets/js/quiz.js',
			array( 'jquery' ),
			APOLLO_LOGIN_VERSION,
			true
		);

		wp_localize_script(
			'apollo-quiz',
			'apolloQuiz',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'restUrl'   => rest_url( APOLLO_LOGIN_REST_NAMESPACE ),
				'nonce'     => wp_create_nonce( 'apollo_quiz' ),
				'stages'    => APOLLO_LOGIN_QUIZ_STAGES,
				'mandatory' => true,
			)
		);
	}

	/**
	 * Get quiz questions
	 *
	 * @param string $stage Stage name.
	 * @return array
	 */
	public static function get_questions( string $stage ): array {
		switch ( $stage ) {
			case 'pattern':
				return self::get_pattern_questions();

			case 'ethics':
				return self::get_ethics_questions();

			case 'reaction':
				return self::get_reaction_config();

			default:
				return array();
		}
	}

	/**
	 * Get pattern recognition questions
	 *
	 * @return array
	 */
	private static function get_pattern_questions(): array {
		return array(
			array(
				'id'       => 1,
				'type'     => 'sequence',
				'question' => 'Complete the sequence: 2, 4, 8, 16, ?',
				'options'  => array( 20, 24, 32, 40 ),
				'correct'  => 32,
			),
			array(
				'id'       => 2,
				'type'     => 'sequence',
				'question' => 'Complete the sequence: A, C, F, J, ?',
				'options'  => array( 'M', 'N', 'O', 'P' ),
				'correct'  => 'O',
			),
			array(
				'id'       => 3,
				'type'     => 'visual',
				'question' => 'Which shape does not belong?',
				'options'  => array( 'circle', 'square', 'triangle', 'pentagon' ),
				'correct'  => 'pentagon',
			),
		);
	}

	/**
	 * Get ethics & respect questions
	 *
	 * @return array
	 */
	private static function get_ethics_questions(): array {
		return array(
			array(
				'id'       => 1,
				'question' => 'Someone posts content you disagree with. What do you do?',
				'options'  => array(
					'a' => 'Attack them personally',
					'b' => 'Report if it violates rules, otherwise ignore or discuss respectfully',
					'c' => 'Share their profile to mock them',
					'd' => 'Create fake accounts to harass them',
				),
				'correct'  => 'b',
				'weight'   => 10,
			),
			array(
				'id'       => 2,
				'question' => 'You see someone being bullied in comments. What do you do?',
				'options'  => array(
					'a' => 'Join in for fun',
					'b' => 'Ignore it',
					'c' => 'Defend the person or report the harassment',
					'd' => 'Screenshot and share elsewhere',
				),
				'correct'  => 'c',
				'weight'   => 15,
			),
			array(
				'id'       => 3,
				'question' => 'What is the best way to handle disagreements?',
				'options'  => array(
					'a' => 'Insult and block',
					'b' => 'Respectful dialogue and understanding different views',
					'c' => 'Spam their posts',
					'd' => 'Dox their personal information',
				),
				'correct'  => 'b',
				'weight'   => 15,
			),
		);
	}

	/**
	 * Get reaction test configuration
	 *
	 * @return array
	 */
	private static function get_reaction_config(): array {
		return array(
			'targets'  => APOLLO_LOGIN_REACTION_TARGETS,
			'duration' => 30, // seconds
			'message'  => 'Click the targets as fast as you can!',
		);
	}
}
