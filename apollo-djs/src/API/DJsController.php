<?php
/**
 * REST Controller — DJs
 *
 * Endpoints conforme apollo-registry.json:
 * - GET|POST   /djs
 * - GET|PUT|DEL /djs/{id}
 * - GET         /djs/{id}/events
 * - GET         /djs/by-sound/{sound}
 * - GET         /djs/search
 *
 * @package Apollo\DJs
 */

declare(strict_types=1);

namespace Apollo\DJs\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DJsController {

	private string $namespace;

	public function __construct() {
		$this->namespace = APOLLO_DJ_REST_NAMESPACE;
	}

	/**
	 * Registra todas as rotas
	 */
	public function register_routes(): void {
		// GET|POST /djs
		register_rest_route(
			$this->namespace,
			'/djs',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_djs' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'page'     => array(
							'type'    => 'integer',
							'default' => 1,
							'minimum' => 1,
						),
						'per_page' => array(
							'type'    => 'integer',
							'default' => 12,
							'minimum' => 1,
							'maximum' => 100,
						),
						'sound'    => array( 'type' => 'string' ),
						'featured' => array( 'type' => 'boolean' ),
						'orderby'  => array(
							'type'    => 'string',
							'default' => 'title',
							'enum'    => array( 'title', 'date', 'rand' ),
						),
						'order'    => array(
							'type'    => 'string',
							'default' => 'ASC',
							'enum'    => array( 'ASC', 'DESC' ),
						),
					),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_dj' ),
					'permission_callback' => array( $this, 'can_edit' ),
				),
			)
		);

		// GET|PUT|DELETE /djs/{id}
		register_rest_route(
			$this->namespace,
			'/djs/(?P<id>\d+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_dj' ),
					'permission_callback' => '__return_true',
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_dj' ),
					'permission_callback' => array( $this, 'can_edit_dj' ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_dj' ),
					'permission_callback' => array( $this, 'can_edit_dj' ),
				),
			)
		);

		// GET /djs/{id}/events
		register_rest_route(
			$this->namespace,
			'/djs/(?P<id>\d+)/events',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_dj_events' ),
				'permission_callback' => '__return_true',
			)
		);

		// GET /djs/by-sound/{sound}
		register_rest_route(
			$this->namespace,
			'/djs/by-sound/(?P<sound>[a-zA-Z0-9_-]+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_by_sound' ),
				'permission_callback' => '__return_true',
			)
		);

		// GET /djs/search
		register_rest_route(
			$this->namespace,
			'/djs/search',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'search_djs' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'q'        => array(
						'type'     => 'string',
						'required' => true,
					),
					'per_page' => array(
						'type'    => 'integer',
						'default' => 10,
						'maximum' => 50,
					),
				),
			)
		);
	}

	// ─── Callbacks ──────────────────────────────────────────────────────

	/**
	 * GET /djs — Listar DJs
	 */
	public function get_djs( \WP_REST_Request $request ): \WP_REST_Response {
		$page     = (int) $request->get_param( 'page' );
		$per_page = (int) $request->get_param( 'per_page' );

		$args = array(
			'post_type'      => APOLLO_DJ_CPT,
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => 'publish',
			'orderby'        => $request->get_param( 'orderby' ),
			'order'          => $request->get_param( 'order' ),
		);

		$sound = $request->get_param( 'sound' );
		if ( $sound ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => APOLLO_DJ_TAX_SOUND,
					'field'    => 'slug',
					'terms'    => array_map( 'trim', explode( ',', $sound ) ),
				),
			);
		}

		$featured = $request->get_param( 'featured' );
		if ( $featured ) {
			$args['meta_query'] = array(
				array(
					'key'   => '_dj_verified',
					'value' => '1',
				),
			);
		}

		$query = new \WP_Query( $args );
		$djs   = array();

		foreach ( $query->posts as $post ) {
			$djs[] = $this->prepare_dj( $post );
		}

		$response = new \WP_REST_Response( $djs, 200 );
		$response->header( 'X-WP-Total', (string) $query->found_posts );
		$response->header( 'X-WP-TotalPages', (string) $query->max_num_pages );

		return $response;
	}

	/**
	 * POST /djs — Criar DJ
	 */
	public function create_dj( \WP_REST_Request $request ): \WP_REST_Response {
		$data = $request->get_json_params();

		$post_id = wp_insert_post(
			array(
				'post_type'    => APOLLO_DJ_CPT,
				'post_title'   => sanitize_text_field( $data['title'] ?? '' ),
				'post_content' => wp_kses_post( $data['content'] ?? '' ),
				'post_status'  => current_user_can( 'publish_posts' ) ? 'publish' : 'pending',
				'post_author'  => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return new \WP_REST_Response( array( 'error' => $post_id->get_error_message() ), 400 );
		}

		$this->save_meta( $post_id, $data );
		$this->save_taxonomies( $post_id, $data );

		do_action( 'apollo_dj_rest_created', $post_id, $data );

		return new \WP_REST_Response( $this->prepare_dj( get_post( $post_id ) ), 201 );
	}

	/**
	 * GET /djs/{id}
	 */
	public function get_dj( \WP_REST_Request $request ): \WP_REST_Response {
		$post = get_post( (int) $request['id'] );

		if ( ! $post || $post->post_type !== APOLLO_DJ_CPT ) {
			return new \WP_REST_Response( array( 'error' => 'DJ não encontrado' ), 404 );
		}

		return new \WP_REST_Response( $this->prepare_dj( $post ), 200 );
	}

	/**
	 * PUT /djs/{id}
	 */
	public function update_dj( \WP_REST_Request $request ): \WP_REST_Response {
		$post_id = (int) $request['id'];
		$data    = $request->get_json_params();

		$update = array( 'ID' => $post_id );

		if ( isset( $data['title'] ) ) {
			$update['post_title'] = sanitize_text_field( $data['title'] );
		}
		if ( isset( $data['content'] ) ) {
			$update['post_content'] = wp_kses_post( $data['content'] );
		}

		wp_update_post( $update );
		$this->save_meta( $post_id, $data );
		$this->save_taxonomies( $post_id, $data );

		do_action( 'apollo_dj_rest_updated', $post_id, $data );

		return new \WP_REST_Response( $this->prepare_dj( get_post( $post_id ) ), 200 );
	}

	/**
	 * DELETE /djs/{id}
	 */
	public function delete_dj( \WP_REST_Request $request ): \WP_REST_Response {
		$post_id = (int) $request['id'];

		do_action( 'apollo_dj_rest_before_delete', $post_id );

		$result = wp_delete_post( $post_id, true );

		if ( ! $result ) {
			return new \WP_REST_Response( array( 'error' => 'Falha ao deletar DJ' ), 500 );
		}

		return new \WP_REST_Response(
			array(
				'deleted' => true,
				'id'      => $post_id,
			),
			200
		);
	}

	/**
	 * GET /djs/{id}/events — Eventos do DJ
	 */
	public function get_dj_events( \WP_REST_Request $request ): \WP_REST_Response {
		$dj_id = (int) $request['id'];

		if ( ! get_post( $dj_id ) || get_post_type( $dj_id ) !== APOLLO_DJ_CPT ) {
			return new \WP_REST_Response( array( 'error' => 'DJ não encontrado' ), 404 );
		}

		$events = \apollo_dj_get_upcoming_events( $dj_id, 50 );
		$result = array();

		foreach ( $events as $event ) {
			$result[] = array(
				'id'         => $event->ID,
				'title'      => $event->post_title,
				'permalink'  => get_permalink( $event->ID ),
				'start_date' => get_post_meta( $event->ID, '_event_start_date', true ),
				'start_time' => get_post_meta( $event->ID, '_event_start_time', true ),
				'end_date'   => get_post_meta( $event->ID, '_event_end_date', true ),
				'status'     => get_post_meta( $event->ID, '_event_status', true ),
				'loc_id'     => (int) get_post_meta( $event->ID, '_event_loc_id', true ),
			);
		}

		return new \WP_REST_Response( $result, 200 );
	}

	/**
	 * GET /djs/by-sound/{sound}
	 */
	public function get_by_sound( \WP_REST_Request $request ): \WP_REST_Response {
		$sound = sanitize_text_field( $request['sound'] );

		$query = new \WP_Query(
			array(
				'post_type'      => APOLLO_DJ_CPT,
				'posts_per_page' => 50,
				'post_status'    => 'publish',
				'tax_query'      => array(
					array(
						'taxonomy' => APOLLO_DJ_TAX_SOUND,
						'field'    => 'slug',
						'terms'    => $sound,
					),
				),
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$djs = array();
		foreach ( $query->posts as $post ) {
			$djs[] = $this->prepare_dj( $post );
		}

		return new \WP_REST_Response( $djs, 200 );
	}

	/**
	 * GET /djs/search?q=
	 */
	public function search_djs( \WP_REST_Request $request ): \WP_REST_Response {
		$q        = sanitize_text_field( $request->get_param( 'q' ) );
		$per_page = (int) $request->get_param( 'per_page' );

		$query = new \WP_Query(
			array(
				'post_type'      => APOLLO_DJ_CPT,
				'posts_per_page' => $per_page,
				'post_status'    => 'publish',
				's'              => $q,
			)
		);

		$djs = array();
		foreach ( $query->posts as $post ) {
			$djs[] = $this->prepare_dj( $post );
		}

		return new \WP_REST_Response( $djs, 200 );
	}

	// ─── Helpers ────────────────────────────────────────────────────────

	/**
	 * Prepara DJ para resposta REST
	 */
	private function prepare_dj( \WP_Post $post ): array {
		return array(
			'id'                    => $post->ID,
			'title'                 => $post->post_title,
			'slug'                  => $post->post_name,
			'content'               => apply_filters( 'the_content', $post->post_content ),
			'excerpt'               => wp_trim_words( $post->post_content, 30 ),
			'permalink'             => get_permalink( $post ),
			'image'                 => \apollo_dj_get_image( $post->ID ),
			'banner'                => \apollo_dj_get_banner( $post->ID ),
			'bio_short'             => get_post_meta( $post->ID, '_dj_bio_short', true ),
			'website'               => get_post_meta( $post->ID, '_dj_website', true ),
			'instagram'             => get_post_meta( $post->ID, '_dj_instagram', true ),
			'soundcloud'            => get_post_meta( $post->ID, '_dj_soundcloud', true ),
			'spotify'               => get_post_meta( $post->ID, '_dj_spotify', true ),
			'youtube'               => get_post_meta( $post->ID, '_dj_youtube', true ),
			'mixcloud'              => get_post_meta( $post->ID, '_dj_mixcloud', true ),
			'user_id'               => (int) get_post_meta( $post->ID, '_dj_user_id', true ),
			'verified'              => \apollo_dj_is_verified( $post->ID ),
			'sounds'                => \apollo_dj_get_sounds( $post->ID ),
			'links'                 => \apollo_dj_get_links( $post->ID ),
			'upcoming_events_count' => \apollo_dj_count_upcoming_events( $post->ID ),
			'author'                => array(
				'id'   => (int) $post->post_author,
				'name' => get_the_author_meta( 'display_name', $post->post_author ),
			),
			'date'                  => $post->post_date,
			'modified'              => $post->post_modified,
		);
	}

	/**
	 * Salva meta fields
	 */
	private function save_meta( int $post_id, array $data ): void {
		$fields = array(
			'bio_short'  => '_dj_bio_short',
			'website'    => '_dj_website',
			'instagram'  => '_dj_instagram',
			'soundcloud' => '_dj_soundcloud',
			'spotify'    => '_dj_spotify',
			'youtube'    => '_dj_youtube',
			'mixcloud'   => '_dj_mixcloud',
			'user_id'    => '_dj_user_id',
			'verified'   => '_dj_verified',
			'image'      => '_dj_image',
			'banner'     => '_dj_banner',
		);

		foreach ( $fields as $key => $meta_key ) {
			if ( isset( $data[ $key ] ) ) {
				update_post_meta( $post_id, $meta_key, $data[ $key ] );
			}
		}
	}

	/**
	 * Salva taxonomias
	 */
	private function save_taxonomies( int $post_id, array $data ): void {
		if ( isset( $data['sounds'] ) && is_array( $data['sounds'] ) ) {
			wp_set_object_terms( $post_id, $data['sounds'], APOLLO_DJ_TAX_SOUND );
		}
	}

	// ─── Permissions ────────────────────────────────────────────────────

	public function can_edit( \WP_REST_Request $request ): bool {
		return current_user_can( 'edit_posts' );
	}

	public function can_edit_dj( \WP_REST_Request $request ): bool {
		return current_user_can( 'edit_post', (int) $request['id'] );
	}
}
