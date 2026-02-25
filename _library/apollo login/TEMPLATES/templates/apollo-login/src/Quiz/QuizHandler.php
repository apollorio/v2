<?php
/**
 * Quiz Handler
 *
 * Manages the 4-stage mandatory aptitude quiz:
 * 1. Pattern Recognition, 2. Simon Memory, 3. Ethics/Respect, 4. Reaction Test
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Quiz;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuizHandler {

	public function __construct() {
		add_action( 'wp_ajax_nopriv_apollo_quiz_submit', [ $this, 'handle_submit' ] );
		add_action( 'wp_ajax_apollo_quiz_submit', [ $this, 'handle_submit' ] );
		add_action( 'wp_ajax_nopriv_apollo_quiz_questions', [ $this, 'get_questions' ] );
		add_action( 'wp_ajax_nopriv_apollo_simon_submit', [ $this, 'handle_simon_submit' ] );
		add_action( 'wp_ajax_apollo_simon_submit', [ $this, 'handle_simon_submit' ] );
	}

	public function get_questions(): void {
		wp_send_json_success([
			'stages' => [
				[
					'type'     => 'pattern_recognition',
					'sequence' => [ '♪', '♫', '♪♪', '♫♫' ],
					'correct'  => '♫♫♫',
					'options'  => [ '♪♪♪', '♪♪♫', '♫♫♫', '♫♪♫' ],
				],
				[
					'type'   => 'simon_memory_game',
					'levels' => 4,
					'colors' => [ 'red', 'blue', 'green', 'yellow' ],
				],
				[
					'type'    => 'ethics_respect_quiz',
					'question' => '"Não gosto de Eletrônica com Funk / de Tribal / de Techno / de Melódico", logo..',
					'correct' => 'correct',
					'options' => [
						[ 'value' => '1', 'text' => 'Critíco e não me importo' ],
						[ 'value' => '2', 'text' => 'A depender da lua, posso hablar mal e pesar mão sobre' ],
						[ 'value' => 'correct', 'text' => 'É trabalho, renda, a sonoridade e arte favorita de alguem.' ],
						[ 'value' => '4', 'text' => 'Tenho dúvidas, mas hablo mal e deixo arder.' ],
					],
				],
				[
					'type'    => 'reaction_test',
					'targets' => 4,
					'timeout' => 2000,
				],
			],
		]);
	}

	public function handle_submit(): void {
		if ( ! check_ajax_referer( 'apollo_auth_nonce', 'nonce', false ) ) {
			wp_send_json_error([ 'message' => __( 'Verificação falhou.', 'apollo-login' ) ], 403 );
		}

		$quiz_data = json_decode( stripslashes( $_POST['quiz_data'] ?? '{}' ), true );

		if ( ! is_array( $quiz_data ) ) {
			wp_send_json_error([ 'message' => __( 'Dados do quiz inválidos.', 'apollo-login' ) ], 400 );
		}

		// Validate quiz results
		$pattern_passed  = ! empty( $quiz_data['pattern_passed'] );
		$simon_passed    = ! empty( $quiz_data['simon_passed'] );
		$ethics_passed   = ! empty( $quiz_data['ethics_passed'] );
		$reaction_passed = ! empty( $quiz_data['reaction_passed'] );

		$all_passed = $pattern_passed && $simon_passed && $ethics_passed && $reaction_passed;

		// Store quiz result
		global $wpdb;
		$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_QUIZ_RESULTS;

		$wpdb->insert( $table, [
			'user_id'    => get_current_user_id() ?: 0,
			'test_type'  => 'full_aptitude',
			'score'      => (int) ( $quiz_data['total_score'] ?? 0 ),
			'answers'    => wp_json_encode( $quiz_data ),
			'passed'     => $all_passed ? 1 : 0,
			'ip_address' => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ),
			'created_at' => current_time( 'mysql' ),
		], [ '%d', '%s', '%d', '%s', '%d', '%s', '%s' ] );

		if ( $all_passed ) {
			wp_send_json_success([
				'message' => __( 'Teste de aptidão concluído com sucesso!', 'apollo-login' ),
				'passed'  => true,
			]);
		} else {
			wp_send_json_error([
				'message' => __( 'Teste de aptidão não aprovado.', 'apollo-login' ),
				'passed'  => false,
			], 400 );
		}
	}

	public function handle_simon_submit(): void {
		if ( ! check_ajax_referer( 'apollo_auth_nonce', 'nonce', false ) ) {
			wp_send_json_error([ 'message' => __( 'Verificação falhou.', 'apollo-login' ) ], 403 );
		}

		global $wpdb;
		$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_SIMON_SCORES;

		$wpdb->insert( $table, [
			'user_id'    => get_current_user_id() ?: 0,
			'score'      => (int) ( $_POST['score'] ?? 0 ),
			'max_level'  => (int) ( $_POST['max_level'] ?? 0 ),
			'time_ms'    => (int) ( $_POST['time_ms'] ?? 0 ),
			'created_at' => current_time( 'mysql' ),
		], [ '%d', '%d', '%d', '%d', '%s' ] );

		wp_send_json_success([ 'message' => 'Score recorded' ]);
	}
}
