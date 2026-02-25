<?php
/**
 * Rating REST API Controller
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Apollo\Users\Components\RatingHandler;
use Apollo\Users\Components\DepoimentoHandler;

class RatingController {

	private string $namespace = 'apollo/v1';

	public function register_routes(): void {
		// Get ratings for a user
		register_rest_route( $this->namespace, '/users/(?P<id>\d+)/ratings', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_ratings' ],
			'permission_callback' => '__return_true',
			'args' => [
				'id' => [
					'required'          => true,
					'sanitize_callback' => 'absint',
				],
			],
		] );

		// Submit rating
		register_rest_route( $this->namespace, '/users/(?P<id>\d+)/ratings', [
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'submit_rating' ],
			'permission_callback' => 'is_user_logged_in',
			'args' => [
				'id' => [
					'required'          => true,
					'sanitize_callback' => 'absint',
				],
				'category' => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				],
				'score' => [
					'required'          => true,
					'sanitize_callback' => 'absint',
				],
			],
		] );

		// Get depoimentos for a user
		register_rest_route( $this->namespace, '/users/(?P<id>\d+)/depoimentos', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_depoimentos' ],
			'permission_callback' => '__return_true',
			'args' => [
				'id' => [
					'required'          => true,
					'sanitize_callback' => 'absint',
				],
				'page' => [
					'default'           => 1,
					'sanitize_callback' => 'absint',
				],
			],
		] );

		// Submit depoimento
		register_rest_route( $this->namespace, '/users/(?P<id>\d+)/depoimentos', [
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'submit_depoimento' ],
			'permission_callback' => 'is_user_logged_in',
			'args' => [
				'id' => [
					'required'          => true,
					'sanitize_callback' => 'absint',
				],
				'content' => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_textarea_field',
				],
			],
		] );
	}

	public function get_ratings( \WP_REST_Request $request ): \WP_REST_Response {
		$target_id = $request->get_param( 'id' );
		$averages  = RatingHandler::get_user_rating_averages( $target_id );
		$total     = RatingHandler::get_total_votes( $target_id );

		$my_votes = [];
		if ( is_user_logged_in() ) {
			$voter_id = get_current_user_id();
			foreach ( array_keys( APOLLO_USERS_RATING_CATEGORIES ) as $cat ) {
				$vote = RatingHandler::get_user_vote( $voter_id, $target_id, $cat );
				$my_votes[ $cat ] = $vote ? (int) $vote->score : 0;
			}
		}

		return rest_ensure_response( [
			'averages'    => $averages,
			'total_votes' => $total,
			'my_votes'    => $my_votes,
		] );
	}

	public function submit_rating( \WP_REST_Request $request ): \WP_REST_Response {
		$target_id = $request->get_param( 'id' );
		$category  = $request->get_param( 'category' );
		$score     = $request->get_param( 'score' );
		$voter_id  = get_current_user_id();

		if ( $voter_id === $target_id ) {
			return new \WP_REST_Response( [ 'error' => 'Você não pode se avaliar.' ], 400 );
		}

		$valid_categories = array_keys( APOLLO_USERS_RATING_CATEGORIES );
		if ( ! in_array( $category, $valid_categories, true ) ) {
			return new \WP_REST_Response( [ 'error' => 'Categoria inválida.' ], 400 );
		}

		if ( $score < 0 || $score > 3 ) {
			return new \WP_REST_Response( [ 'error' => 'Pontuação inválida (0-3).' ], 400 );
		}

		global $wpdb;
		$table = $wpdb->prefix . APOLLO_USERS_TABLE_RATINGS;

		$wpdb->replace( $table, [
			'voter_id'   => $voter_id,
			'target_id'  => $target_id,
			'category'   => $category,
			'score'      => $score,
			'created_at' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' ),
		] );

		$averages = RatingHandler::get_user_rating_averages( $target_id );

		return rest_ensure_response( [
			'success'  => true,
			'averages' => $averages,
		] );
	}

	public function get_depoimentos( \WP_REST_Request $request ): \WP_REST_Response {
		$target_id = $request->get_param( 'id' );
		$page      = $request->get_param( 'page' ) ?: 1;

		$depoimentos = DepoimentoHandler::get_depoimentos( $target_id, $page );
		$total       = DepoimentoHandler::count_depoimentos( $target_id );

		return rest_ensure_response( [
			'depoimentos' => $depoimentos,
			'total'       => $total,
		] );
	}

	public function submit_depoimento( \WP_REST_Request $request ): \WP_REST_Response {
		$target_id = $request->get_param( 'id' );
		$content   = $request->get_param( 'content' );
		$author_id = get_current_user_id();

		if ( $author_id === $target_id ) {
			return new \WP_REST_Response( [ 'error' => 'Você não pode escrever um depoimento para si mesmo.' ], 400 );
		}

		$existing = DepoimentoHandler::get_user_depoimento( $author_id, $target_id );
		if ( $existing ) {
			return new \WP_REST_Response( [ 'error' => 'Você já deixou um depoimento.' ], 400 );
		}

		$author = get_userdata( $author_id );

		$comment_id = wp_insert_comment( [
			'comment_post_ID'      => 0,
			'comment_author'       => $author->display_name,
			'comment_author_email' => $author->user_email,
			'comment_content'      => $content,
			'comment_type'         => APOLLO_USERS_DEPOIMENTO_TYPE,
			'comment_parent'       => $target_id,
			'user_id'              => $author_id,
			'comment_approved'     => 1,
			'comment_date'         => current_time( 'mysql' ),
		] );

		if ( ! $comment_id ) {
			return new \WP_REST_Response( [ 'error' => 'Erro ao salvar.' ], 500 );
		}

		return rest_ensure_response( [
			'success'    => true,
			'depoimento' => DepoimentoHandler::format_depoimento( get_comment( $comment_id ) ),
		] );
	}
}
