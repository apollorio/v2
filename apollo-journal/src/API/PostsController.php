<?php

/**
 * REST Controller — Journal Posts
 *
 * Endpoint: GET /apollo/v1/journal/posts
 * Used by the load-more JS and external consumers.
 *
 * @package Apollo\Journal
 */

declare(strict_types=1);

namespace Apollo\Journal\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Posts REST controller.
 */
class PostsController {


	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			APOLLO_JOURNAL_REST_NAMESPACE,
			'/journal/posts',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_posts' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_collection_params(),
				),
			)
		);
	}

	/**
	 * Get paginated journal posts.
	 *
	 * @param \WP_REST_Request $request Full request data.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_posts( \WP_REST_Request $request ) {
		$page     = $request->get_param( 'page' ) ? absint( $request->get_param( 'page' ) ) : 1;
		$per_page = $request->get_param( 'per_page' ) ? absint( $request->get_param( 'per_page' ) ) : 6;
		$category = $request->get_param( 'category' ) ? sanitize_text_field( $request->get_param( 'category' ) ) : '';
		$taxonomy = $request->get_param( 'taxonomy' ) ? sanitize_key( $request->get_param( 'taxonomy' ) ) : '';
		$term     = $request->get_param( 'term' ) ? sanitize_text_field( $request->get_param( 'term' ) ) : '';

		$per_page = min( $per_page, 24 );

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( ! empty( $category ) ) {
			$args['category_name'] = $category;
		}

		if ( ! empty( $taxonomy ) && ! empty( $term ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $term,
				),
			);
		}

		$query = new \WP_Query( $args );
		$data  = array();

		foreach ( $query->posts as $post ) {
			$cats     = get_the_category( $post->ID );
			$cat_name = ! empty( $cats ) ? $cats[0]->name : 'News';
			$nrep     = get_post_meta( $post->ID, '_nrep_code', true );

			$time_ago = function_exists( 'apollo_time_ago' )
				? apollo_time_ago( get_post_time( 'Y-m-d H:i:s', false, $post ) )
				: human_time_diff( get_post_time( 'U', false, $post ), current_time( 'timestamp' ) );

			$data[] = array(
				'id'          => $post->ID,
				'title'       => array( 'rendered' => get_the_title( $post ) ),
				'link'        => get_permalink( $post ),
				'excerpt'     => wp_trim_words( get_the_excerpt( $post ), 20 ),
				'thumbnail'   => get_the_post_thumbnail_url( $post, 'medium' ) ?: '',
				'badge'       => $nrep ?: strtoupper( $cat_name ),
				'is_nrep'     => (bool) $nrep,
				'author_name' => get_the_author_meta( 'display_name', $post->post_author ),
				'time_ago'    => $time_ago,
				'date'        => get_the_date( 'c', $post ),
			);
		}

		$response = new \WP_REST_Response( $data, 200 );
		$response->header( 'X-WP-Total', (string) $query->found_posts );
		$response->header( 'X-WP-TotalPages', (string) $query->max_num_pages );

		return $response;
	}

	/**
	 * Define REST params schema.
	 *
	 * @return array<string, array>
	 */
	private function get_collection_params(): array {
		return array(
			'page'     => array(
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'type'              => 'integer',
				'default'           => 6,
				'minimum'           => 1,
				'maximum'           => 24,
				'sanitize_callback' => 'absint',
			),
			'category' => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'taxonomy' => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_key',
			),
			'term'     => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}
