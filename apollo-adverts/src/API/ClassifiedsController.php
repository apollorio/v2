<?php

/**
 * REST API: Classifieds Controller
 *
 * Endpoints: /classifieds (GET,POST), /classifieds/{id} (GET,PUT,DELETE), /classifieds/my (GET)
 * Adapted from WPAdverts REST patterns + WP_REST_Controller base.
 *
 * @package Apollo\Adverts
 */

declare(strict_types=1);

namespace Apollo\Adverts\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ClassifiedsController extends \WP_REST_Controller {


	protected $namespace = APOLLO_ADVERTS_REST_NAMESPACE;
	protected $rest_base = 'classifieds';

	/**
	 * Register routes
	 * Registry spec: /classifieds (GET,POST), /classifieds/{id} (GET,PUT,DELETE), /classifieds/my (GET)
	 */
	public function register_routes(): void {

		// /classifieds — list + create
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_create_params(),
				),
			)
		);

		// /classifieds/{id} — single CRUD
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'id' => array(
							'type'     => 'integer',
							'required' => true,
						),
					),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				),
			)
		);

		// /classifieds/my — authenticated user's ads
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/my',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_my_items' ),
					'permission_callback' => function () {
						return is_user_logged_in();
					},
					'args'                => $this->get_collection_params(),
				),
			)
		);
	}

	/**
	 * GET /classifieds — List
	 */
	public function get_items( $request ): \WP_REST_Response {
		$args = array(
			'post_type'      => APOLLO_CPT_CLASSIFIED,
			'post_status'    => 'publish',
			'posts_per_page' => $request->get_param( 'per_page' ) ?: APOLLO_ADVERTS_POSTS_PER_PAGE,
			'paged'          => $request->get_param( 'page' ) ?: 1,
			'orderby'        => $request->get_param( 'orderby' ) ?: 'date',
			'order'          => $request->get_param( 'order' ) ?: 'DESC',
		);

		// Taxonomy filters
		$tax_query = array();
		$domain    = $request->get_param( 'domain' );
		if ( $domain ) {
			$tax_query[] = array(
				'taxonomy' => APOLLO_TAX_CLASSIFIED_DOMAIN,
				'field'    => 'slug',
				'terms'    => $domain,
			);
		}
		$intent = $request->get_param( 'intent' );
		if ( $intent ) {
			$tax_query[] = array(
				'taxonomy' => APOLLO_TAX_CLASSIFIED_INTENT,
				'field'    => 'slug',
				'terms'    => $intent,
			);
		}
		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		// Featured filter
		$featured = $request->get_param( 'featured' );
		if ( $featured ) {
			$args['meta_query'][] = array(
				'key'   => '_classified_featured',
				'value' => '1',
			);
		}

		$query = new \WP_Query( $args );
		$items = array();

		foreach ( $query->posts as $post ) {
			$items[] = $this->prepare_item( $post );
		}

		$response = rest_ensure_response( $items );
		$response->header( 'X-WP-Total', (string) $query->found_posts );
		$response->header( 'X-WP-TotalPages', (string) $query->max_num_pages );

		return $response;
	}

	/**
	 * GET /classifieds/{id} — Single
	 */
	public function get_item( $request ): \WP_REST_Response|\WP_Error {
		$post = get_post( $request->get_param( 'id' ) );

		if ( ! $post || $post->post_type !== APOLLO_CPT_CLASSIFIED ) {
			return new \WP_Error( 'not_found', __( 'Anúncio não encontrado.', 'apollo-adverts' ), array( 'status' => 404 ) );
		}

		if ( $post->post_status !== 'publish' ) {
			$can_view = is_user_logged_in() && ( (int) $post->post_author === get_current_user_id() || current_user_can( 'manage_options' ) );
			if ( ! $can_view ) {
				return new \WP_Error( 'not_found', __( 'Anúncio não encontrado.', 'apollo-adverts' ), array( 'status' => 404 ) );
			}
		}

		return rest_ensure_response( $this->prepare_item( $post ) );
	}

	/**
	 * POST /classifieds — Create
	 */
	public function create_item( $request ): \WP_REST_Response|\WP_Error {
		$config = apollo_adverts_config();
		$status = $config['moderation'] === 'manual' ? 'pending' : 'publish';

		$post_data = array(
			'post_type'    => APOLLO_CPT_CLASSIFIED,
			'post_status'  => $status,
			'post_title'   => sanitize_text_field( $request->get_param( 'title' ) ?? '' ),
			'post_content' => sanitize_textarea_field( $request->get_param( 'content' ) ?? '' ),
			'post_author'  => get_current_user_id(),
		);

		$post_id = wp_insert_post( $post_data, true );
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// Save meta
		$this->save_meta_from_request( $post_id, $request );

		// Set expiration
		apollo_adverts_set_expiration( $post_id );
		update_post_meta( $post_id, '_classified_currency', 'BRL' );

		// Assign taxonomies
		$domain = $request->get_param( 'domain' );
		if ( $domain ) {
			wp_set_object_terms( $post_id, $domain, APOLLO_TAX_CLASSIFIED_DOMAIN );
		}
		$intent = $request->get_param( 'intent' );
		if ( $intent ) {
			wp_set_object_terms( $post_id, $intent, APOLLO_TAX_CLASSIFIED_INTENT );
		}

		do_action( 'apollo/classifieds/created', $post_id, $request->get_params() );

		$post = get_post( $post_id );
		return rest_ensure_response( $this->prepare_item( $post ) );
	}

	/**
	 * PUT /classifieds/{id} — Update
	 */
	public function update_item( $request ): \WP_REST_Response|\WP_Error {
		$post_id = (int) $request->get_param( 'id' );
		$post    = get_post( $post_id );

		if ( ! $post || $post->post_type !== APOLLO_CPT_CLASSIFIED ) {
			return new \WP_Error( 'not_found', __( 'Anúncio não encontrado.', 'apollo-adverts' ), array( 'status' => 404 ) );
		}

		$update = array( 'ID' => $post_id );

		$title = $request->get_param( 'title' );
		if ( $title !== null ) {
			$update['post_title'] = sanitize_text_field( $title );
		}
		$content = $request->get_param( 'content' );
		if ( $content !== null ) {
			$update['post_content'] = sanitize_textarea_field( $content );
		}

		wp_update_post( $update );
		$this->save_meta_from_request( $post_id, $request );

		// Taxonomies
		$domain = $request->get_param( 'domain' );
		if ( $domain !== null ) {
			wp_set_object_terms( $post_id, $domain, APOLLO_TAX_CLASSIFIED_DOMAIN );
		}
		$intent = $request->get_param( 'intent' );
		if ( $intent !== null ) {
			wp_set_object_terms( $post_id, $intent, APOLLO_TAX_CLASSIFIED_INTENT );
		}

		do_action( 'apollo/classifieds/updated', $post_id, $request->get_params() );

		return rest_ensure_response( $this->prepare_item( get_post( $post_id ) ) );
	}

	/**
	 * DELETE /classifieds/{id}
	 */
	public function delete_item( $request ): \WP_REST_Response|\WP_Error {
		$post_id = (int) $request->get_param( 'id' );
		$post    = get_post( $post_id );

		if ( ! $post || $post->post_type !== APOLLO_CPT_CLASSIFIED ) {
			return new \WP_Error( 'not_found', __( 'Anúncio não encontrado.', 'apollo-adverts' ), array( 'status' => 404 ) );
		}

		wp_trash_post( $post_id );
		do_action( 'apollo/classifieds/deleted', $post_id );

		return rest_ensure_response(
			array(
				'deleted' => true,
				'id'      => $post_id,
			)
		);
	}

	/**
	 * GET /classifieds/my — Current user's ads
	 */
	public function get_my_items( $request ): \WP_REST_Response {
		$args = array(
			'post_type'      => APOLLO_CPT_CLASSIFIED,
			'post_status'    => array( 'publish', 'pending', 'draft', 'expired' ),
			'author'         => get_current_user_id(),
			'posts_per_page' => $request->get_param( 'per_page' ) ?: APOLLO_ADVERTS_POSTS_PER_PAGE,
			'paged'          => $request->get_param( 'page' ) ?: 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$query = new \WP_Query( $args );
		$items = array();

		foreach ( $query->posts as $post ) {
			$items[] = $this->prepare_item( $post );
		}

		$response = rest_ensure_response( $items );
		$response->header( 'X-WP-Total', (string) $query->found_posts );
		$response->header( 'X-WP-TotalPages', (string) $query->max_num_pages );

		return $response;
	}

	/**
	 * Prepare item for response
	 */
	protected function prepare_item( \WP_Post $post ): array {
		$meta = array();
		foreach ( APOLLO_ADVERTS_META_KEYS as $key => $config ) {
			$meta[ $key ] = get_post_meta( $post->ID, $key, true );
		}

		$domains = wp_get_object_terms( $post->ID, APOLLO_TAX_CLASSIFIED_DOMAIN, array( 'fields' => 'all' ) );
		$intents = wp_get_object_terms( $post->ID, APOLLO_TAX_CLASSIFIED_INTENT, array( 'fields' => 'all' ) );

		$image = apollo_adverts_get_main_image( $post->ID, 'classified-medium' );

		return array(
			'id'          => $post->ID,
			'title'       => $post->post_title,
			'content'     => $post->post_content,
			'excerpt'     => get_the_excerpt( $post ),
			'status'      => $post->post_status,
			'author'      => (int) $post->post_author,
			'author_name' => get_the_author_meta( 'display_name', $post->post_author ),
			'date'        => $post->post_date,
			'modified'    => $post->post_modified,
			'link'        => get_permalink( $post->ID ),
			'image'       => $image,
			'price'       => apollo_adverts_get_the_price( $post->ID ),
			'meta'        => $meta,
			'domains'     => is_wp_error( $domains ) ? array() : array_map(
				function ( $t ) {
					return array(
						'slug' => $t->slug,
						'name' => $t->name,
					);
				},
				$domains
			),
			'intents'     => is_wp_error( $intents ) ? array() : array_map(
				function ( $t ) {
					return array(
						'slug' => $t->slug,
						'name' => $t->name,
					);
				},
				$intents
			),
			'is_expired'  => apollo_adverts_is_expired( $post->ID ),
			'is_featured' => apollo_adverts_is_featured( $post->ID ),
			'views'       => (int) get_post_meta( $post->ID, '_classified_views', true ),
		);
	}

	/**
	 * Save meta from REST request
	 */
	protected function save_meta_from_request( int $post_id, \WP_REST_Request $request ): void {
		$map = array(
			'price'      => '_classified_price',
			'currency'   => '_classified_currency',
			'negotiable' => '_classified_negotiable',
			'condition'  => '_classified_condition',
			'location'   => '_classified_location',
			'phone'      => '_classified_contact_phone',
			'whatsapp'   => '_classified_contact_whatsapp',
			'featured'   => '_classified_featured',
			'expires_at' => '_classified_expires_at',
		);

		foreach ( $map as $param => $meta_key ) {
			$value = $request->get_param( $param );
			if ( $value !== null ) {
				update_post_meta( $post_id, $meta_key, sanitize_text_field( (string) $value ) );
			}
		}
	}

	/**
	 * Permission checks
	 */
	public function create_item_permissions_check( $request ): bool|\WP_Error {
		if ( ! is_user_logged_in() ) {
			return new \WP_Error( 'unauthorized', __( 'Faça login para criar anúncios.', 'apollo-adverts' ), array( 'status' => 401 ) );
		}
		return true;
	}

	public function update_item_permissions_check( $request ): bool|\WP_Error {
		if ( ! is_user_logged_in() ) {
			return new \WP_Error( 'unauthorized', __( 'Faça login primeiro.', 'apollo-adverts' ), array( 'status' => 401 ) );
		}
		$post = get_post( $request->get_param( 'id' ) );
		if ( ! $post ) {
			return new \WP_Error( 'not_found', __( 'Anúncio não encontrado.', 'apollo-adverts' ), array( 'status' => 404 ) );
		}
		if ( (int) $post->post_author !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error( 'forbidden', __( 'Permissão negada.', 'apollo-adverts' ), array( 'status' => 403 ) );
		}
		return true;
	}

	public function delete_item_permissions_check( $request ): bool|\WP_Error {
		return $this->update_item_permissions_check( $request );
	}

	/**
	 * Collection params schema
	 */
	public function get_collection_params(): array {
		return array(
			'page'     => array(
				'type'    => 'integer',
				'default' => 1,
				'minimum' => 1,
			),
			'per_page' => array(
				'type'    => 'integer',
				'default' => APOLLO_ADVERTS_POSTS_PER_PAGE,
				'minimum' => 1,
				'maximum' => 100,
			),
			'orderby'  => array(
				'type'    => 'string',
				'default' => 'date',
				'enum'    => array( 'date', 'title', 'modified', 'meta_value_num' ),
			),
			'order'    => array(
				'type'    => 'string',
				'default' => 'DESC',
				'enum'    => array( 'ASC', 'DESC' ),
			),
			'domain'   => array(
				'type'        => 'string',
				'description' => 'Filter by classified_domain slug',
			),
			'intent'   => array(
				'type'        => 'string',
				'description' => 'Filter by classified_intent slug',
			),
			'featured' => array(
				'type'        => 'boolean',
				'description' => 'Only featured ads',
			),
		);
	}

	/**
	 * Create params schema
	 */
	protected function get_create_params(): array {
		return array(
			'title'      => array(
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => 'sanitize_text_field',
			),
			'content'    => array(
				'type'     => 'string',
				'required' => true,
			),
			'domain'     => array( 'type' => 'string' ),
			'intent'     => array( 'type' => 'string' ),
			'price'      => array( 'type' => 'string' ),
			'negotiable' => array( 'type' => 'string' ),
			'condition'  => array( 'type' => 'string' ),
			'location'   => array( 'type' => 'string' ),
			'phone'      => array( 'type' => 'string' ),
			'whatsapp'   => array( 'type' => 'string' ),
		);
	}
}
