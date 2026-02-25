<?php
/**
 * REST API Controller — /apollo/v1/events
 *
 * Endpoints do registry:
 *   GET|POST   /events
 *   GET|PUT|DEL /events/{id}
 *   GET        /events/upcoming
 *   GET        /events/past
 *   GET        /events/today
 *   GET        /events/by-date/{date}
 *   GET        /events/by-loc/{loc_id}
 *   GET        /events/by-dj/{dj_id}
 *   GET|POST|DEL /events/{id}/djs
 *
 * @package Apollo\Event
 */

declare(strict_types=1);

namespace Apollo\Event\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EventsController {

	/** @var string */
	private string $namespace;

	public function __construct() {
		$this->namespace = APOLLO_EVENT_REST_NAMESPACE;
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Registra todas as rotas REST
	 */
	public function register_routes(): void {

		// GET|POST /events
		register_rest_route( $this->namespace, '/events', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_events' ],
				'permission_callback' => '__return_true',
				'args'                => $this->get_collection_params(),
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_event' ],
				'permission_callback' => [ $this, 'can_edit' ],
				'args'                => $this->get_create_params(),
			],
		] );

		// GET|PUT|DELETE /events/{id}
		register_rest_route( $this->namespace, '/events/(?P<id>\d+)', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_event' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'id' => [
						'type'              => 'integer',
						'required'          => true,
						'validate_callback' => [ $this, 'validate_event_id' ],
					],
				],
			],
			[
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_event' ],
				'permission_callback' => [ $this, 'can_edit_event' ],
				'args'                => $this->get_create_params(),
			],
			[
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_event' ],
				'permission_callback' => [ $this, 'can_edit_event' ],
			],
		] );

		// GET /events/upcoming
		register_rest_route( $this->namespace, '/events/upcoming', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_upcoming' ],
			'permission_callback' => '__return_true',
			'args'                => $this->get_collection_params(),
		] );

		// GET /events/past
		register_rest_route( $this->namespace, '/events/past', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_past' ],
			'permission_callback' => '__return_true',
			'args'                => $this->get_collection_params(),
		] );

		// GET /events/today
		register_rest_route( $this->namespace, '/events/today', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_today' ],
			'permission_callback' => '__return_true',
		] );

		// GET /events/by-date/{date}
		register_rest_route( $this->namespace, '/events/by-date/(?P<date>[\d-]+)', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_by_date' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'date' => [
					'type'              => 'string',
					'required'          => true,
					'validate_callback' => function ( $value ) {
						return (bool) preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value );
					},
				],
			],
		] );

		// GET /events/by-loc/{loc_id}
		register_rest_route( $this->namespace, '/events/by-loc/(?P<loc_id>\d+)', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_by_loc' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'loc_id' => [ 'type' => 'integer', 'required' => true ],
			],
		] );

		// GET /events/by-dj/{dj_id}
		register_rest_route( $this->namespace, '/events/by-dj/(?P<dj_id>\d+)', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_by_dj' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'dj_id' => [ 'type' => 'integer', 'required' => true ],
			],
		] );

		// GET|POST|DELETE /events/{id}/djs
		register_rest_route( $this->namespace, '/events/(?P<id>\d+)/djs', [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_event_djs' ],
				'permission_callback' => '__return_true',
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'add_event_dj' ],
				'permission_callback' => [ $this, 'can_edit_event' ],
				'args'                => [
					'dj_id' => [ 'type' => 'integer', 'required' => true ],
					'slot'  => [ 'type' => 'object', 'required' => false ],
				],
			],
			[
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'remove_event_dj' ],
				'permission_callback' => [ $this, 'can_edit_event' ],
				'args'                => [
					'dj_id' => [ 'type' => 'integer', 'required' => true ],
				],
			],
		] );
	}

	// ─── Callbacks ─────────────────────────────────────────────────────

	/**
	 * GET /events — Listagem
	 */
	public function get_events( \WP_REST_Request $request ): \WP_REST_Response {
		$args = $this->build_query_from_request( $request );
		$query = new \WP_Query( $args );

		return $this->paginated_response( $query, $request );
	}

	/**
	 * POST /events — Criar evento
	 */
	public function create_event( \WP_REST_Request $request ): \WP_REST_Response {
		$data = $request->get_json_params();

		$post_data = [
			'post_type'   => APOLLO_EVENT_CPT,
			'post_status' => 'publish',
			'post_title'  => sanitize_text_field( $data['title'] ?? '' ),
			'post_content' => wp_kses_post( $data['content'] ?? '' ),
		];

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return new \WP_REST_Response( [ 'error' => $post_id->get_error_message() ], 400 );
		}

		$this->save_event_meta( $post_id, $data );

		// Taxonomias
		$this->save_event_taxonomies( $post_id, $data );

		/**
		 * Ação após criar evento via REST
		 *
		 * @param int   $post_id ID do evento criado.
		 * @param array $data    Dados do request.
		 */
		do_action( 'apollo_event_rest_created', $post_id, $data );

		return new \WP_REST_Response( $this->prepare_event( $post_id ), 201 );
	}

	/**
	 * GET /events/{id} — Evento individual
	 */
	public function get_event( \WP_REST_Request $request ): \WP_REST_Response {
		$id = (int) $request->get_param( 'id' );

		return new \WP_REST_Response( $this->prepare_event( $id ) );
	}

	/**
	 * PUT /events/{id} — Atualizar evento
	 */
	public function update_event( \WP_REST_Request $request ): \WP_REST_Response {
		$id   = (int) $request->get_param( 'id' );
		$data = $request->get_json_params();

		$post_data = [ 'ID' => $id ];

		if ( isset( $data['title'] ) ) {
			$post_data['post_title'] = sanitize_text_field( $data['title'] );
		}
		if ( isset( $data['content'] ) ) {
			$post_data['post_content'] = wp_kses_post( $data['content'] );
		}
		if ( isset( $data['status'] ) ) {
			$post_data['post_status'] = sanitize_text_field( $data['status'] );
		}

		wp_update_post( $post_data );

		$this->save_event_meta( $id, $data );
		$this->save_event_taxonomies( $id, $data );

		/**
		 * Ação após atualizar evento via REST
		 */
		do_action( 'apollo_event_rest_updated', $id, $data );

		return new \WP_REST_Response( $this->prepare_event( $id ) );
	}

	/**
	 * DELETE /events/{id} — Excluir evento
	 */
	public function delete_event( \WP_REST_Request $request ): \WP_REST_Response {
		$id = (int) $request->get_param( 'id' );

		/**
		 * Ação antes de excluir evento via REST
		 */
		do_action( 'apollo_event_rest_before_delete', $id );

		$result = wp_trash_post( $id );

		if ( ! $result ) {
			return new \WP_REST_Response( [ 'error' => 'Falha ao excluir evento.' ], 500 );
		}

		return new \WP_REST_Response( [ 'deleted' => true, 'id' => $id ] );
	}

	/**
	 * GET /events/upcoming — Eventos futuros
	 */
	public function get_upcoming( \WP_REST_Request $request ): \WP_REST_Response {
		$args = $this->build_query_from_request( $request );
		$args['meta_query'][] = [
			'key'     => '_event_start_date',
			'value'   => current_time( 'Y-m-d' ),
			'compare' => '>=',
			'type'    => 'DATE',
		];

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/past — Eventos passados
	 */
	public function get_past( \WP_REST_Request $request ): \WP_REST_Response {
		$args = $this->build_query_from_request( $request );
		$args['meta_query'][] = [
			'key'     => '_event_start_date',
			'value'   => current_time( 'Y-m-d' ),
			'compare' => '<',
			'type'    => 'DATE',
		];
		$args['order'] = 'DESC';

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/today — Eventos de hoje
	 */
	public function get_today( \WP_REST_Request $request ): \WP_REST_Response {
		$today = current_time( 'Y-m-d' );

		$args = [
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_time',
			'order'          => 'ASC',
			'meta_query'     => [
				[
					'key'     => '_event_start_date',
					'value'   => $today,
					'compare' => '=',
				],
			],
		];

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/by-date/{date} — Eventos por data
	 */
	public function get_by_date( \WP_REST_Request $request ): \WP_REST_Response {
		$date = sanitize_text_field( $request->get_param( 'date' ) );

		$args = [
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_time',
			'order'          => 'ASC',
			'meta_query'     => [
				[
					'key'     => '_event_start_date',
					'value'   => $date,
					'compare' => '=',
				],
			],
		];

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/by-loc/{loc_id} — Eventos por local
	 */
	public function get_by_loc( \WP_REST_Request $request ): \WP_REST_Response {
		$loc_id = (int) $request->get_param( 'loc_id' );

		$args = [
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'order'          => 'ASC',
			'meta_query'     => [
				[
					'key'     => '_event_loc_id',
					'value'   => $loc_id,
					'compare' => '=',
					'type'    => 'NUMERIC',
				],
			],
		];

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/by-dj/{dj_id} — Eventos por DJ
	 */
	public function get_by_dj( \WP_REST_Request $request ): \WP_REST_Response {
		$dj_id = (int) $request->get_param( 'dj_id' );

		// _event_dj_ids é serialized array — busca com LIKE
		$args = [
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'order'          => 'ASC',
			'meta_query'     => [
				[
					'key'     => '_event_dj_ids',
					'value'   => sprintf( '"%d"', $dj_id ),
					'compare' => 'LIKE',
				],
			],
		];

		// Fallback: busca com i:{n};i:{dj_id}
		$args_alt = $args;
		$args_alt['meta_query'][0]['value'] = sprintf( 'i:%d;', $dj_id );

		$query = new \WP_Query( $args );

		// Se não encontrou, tentar formato serializado alternativo
		if ( ! $query->have_posts() ) {
			$query = new \WP_Query( $args_alt );
		}

		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/{id}/djs — DJs do evento
	 */
	public function get_event_djs( \WP_REST_Request $request ): \WP_REST_Response {
		$id  = (int) $request->get_param( 'id' );
		$djs = apollo_event_get_djs( $id );

		// Incluir slots
		$slots   = get_post_meta( $id, '_event_dj_slots', true ) ?: [];
		$enriched = [];

		foreach ( $djs as $dj ) {
			$dj_data = (array) $dj;
			// Procurar slot do DJ
			foreach ( $slots as $slot ) {
				if ( isset( $slot['dj_id'] ) && (int) $slot['dj_id'] === $dj['id'] ) {
					$dj_data['slot'] = $slot;
					break;
				}
			}
			$enriched[] = $dj_data;
		}

		return new \WP_REST_Response( $enriched );
	}

	/**
	 * POST /events/{id}/djs — Adicionar DJ ao evento
	 */
	public function add_event_dj( \WP_REST_Request $request ): \WP_REST_Response {
		$id    = (int) $request->get_param( 'id' );
		$dj_id = (int) $request->get_param( 'dj_id' );
		$slot  = $request->get_param( 'slot' );

		// Verificar se DJ existe
		$dj_post = get_post( $dj_id );
		if ( ! $dj_post || 'dj' !== $dj_post->post_type ) {
			return new \WP_REST_Response( [ 'error' => 'DJ não encontrado.' ], 404 );
		}

		// Adicionar DJ ao array
		$dj_ids = get_post_meta( $id, '_event_dj_ids', true ) ?: [];
		if ( ! is_array( $dj_ids ) ) {
			$dj_ids = [];
		}

		if ( ! in_array( $dj_id, $dj_ids, true ) ) {
			$dj_ids[] = $dj_id;
			update_post_meta( $id, '_event_dj_ids', $dj_ids );
		}

		// Salvar slot (se fornecido)
		if ( $slot && is_array( $slot ) ) {
			$slots = get_post_meta( $id, '_event_dj_slots', true ) ?: [];
			if ( ! is_array( $slots ) ) {
				$slots = [];
			}
			$slot['dj_id'] = $dj_id;
			$slots[]       = $slot;
			update_post_meta( $id, '_event_dj_slots', $slots );
		}

		/**
		 * Ação após adicionar DJ ao evento
		 */
		do_action( 'apollo_event_dj_added', $id, $dj_id, $slot );

		return new \WP_REST_Response( [
			'added' => true,
			'event_id' => $id,
			'dj_id'    => $dj_id,
		], 201 );
	}

	/**
	 * DELETE /events/{id}/djs — Remover DJ do evento
	 */
	public function remove_event_dj( \WP_REST_Request $request ): \WP_REST_Response {
		$id    = (int) $request->get_param( 'id' );
		$dj_id = (int) $request->get_param( 'dj_id' );

		// Remover do array de IDs
		$dj_ids = get_post_meta( $id, '_event_dj_ids', true ) ?: [];
		if ( is_array( $dj_ids ) ) {
			$dj_ids = array_values( array_filter( $dj_ids, fn( $d ) => (int) $d !== $dj_id ) );
			update_post_meta( $id, '_event_dj_ids', $dj_ids );
		}

		// Remover slot
		$slots = get_post_meta( $id, '_event_dj_slots', true ) ?: [];
		if ( is_array( $slots ) ) {
			$slots = array_values( array_filter( $slots, fn( $s ) => (int) ( $s['dj_id'] ?? 0 ) !== $dj_id ) );
			update_post_meta( $id, '_event_dj_slots', $slots );
		}

		/**
		 * Ação após remover DJ do evento
		 */
		do_action( 'apollo_event_dj_removed', $id, $dj_id );

		return new \WP_REST_Response( [
			'removed'  => true,
			'event_id' => $id,
			'dj_id'    => $dj_id,
		] );
	}

	// ─── Permissions ───────────────────────────────────────────────────

	/**
	 * Pode criar eventos?
	 */
	public function can_edit( \WP_REST_Request $request ): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Pode editar/excluir este evento?
	 */
	public function can_edit_event( \WP_REST_Request $request ): bool {
		$id = (int) $request->get_param( 'id' );
		return current_user_can( 'edit_post', $id );
	}

	/**
	 * Valida se o ID é de um evento
	 */
	public function validate_event_id( $value ): bool {
		$post = get_post( (int) $value );
		return $post && APOLLO_EVENT_CPT === $post->post_type;
	}

	// ─── Helpers ───────────────────────────────────────────────────────

	/**
	 * Prepara dados do evento para resposta REST
	 */
	private function prepare_event( int $post_id ): array {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return [];
		}

		$djs = apollo_event_get_djs( $post_id );
		$loc = apollo_event_get_loc( $post_id );

		$data = [
			'id'           => $post_id,
			'title'        => $post->post_title,
			'slug'         => $post->post_name,
			'content'      => $post->post_content,
			'excerpt'      => $post->post_excerpt,
			'status'       => $post->post_status,
			'author'       => (int) $post->post_author,
			'permalink'    => get_permalink( $post_id ),
			'banner'       => apollo_event_get_banner( $post_id ),
			'thumbnail'    => get_the_post_thumbnail_url( $post_id, 'medium' ) ?: null,
			'start_date'   => get_post_meta( $post_id, '_event_start_date', true ),
			'end_date'     => get_post_meta( $post_id, '_event_end_date', true ),
			'start_time'   => get_post_meta( $post_id, '_event_start_time', true ),
			'end_time'     => get_post_meta( $post_id, '_event_end_time', true ),
			'dj_ids'       => get_post_meta( $post_id, '_event_dj_ids', true ) ?: [],
			'djs'          => $djs,
			'dj_slots'     => get_post_meta( $post_id, '_event_dj_slots', true ) ?: [],
			'loc_id'       => (int) get_post_meta( $post_id, '_event_loc_id', true ),
			'loc'          => $loc,
			'ticket_url'   => get_post_meta( $post_id, '_event_ticket_url', true ),
			'ticket_price' => get_post_meta( $post_id, '_event_ticket_price', true ),
			'privacy'      => get_post_meta( $post_id, '_event_privacy', true ) ?: 'public',
			'event_status' => get_post_meta( $post_id, '_event_status', true ) ?: 'scheduled',
			'is_gone'      => apollo_event_is_gone( $post_id ),
			'categories'   => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_CATEGORY, [ 'fields' => 'all' ] ),
			'types'        => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_TYPE, [ 'fields' => 'all' ] ),
			'tags'         => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_TAG, [ 'fields' => 'all' ] ),
			'sounds'       => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_SOUND, [ 'fields' => 'all' ] ),
			'seasons'      => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_SEASON, [ 'fields' => 'all' ] ),
			'created'      => $post->post_date,
			'modified'     => $post->post_modified,
		];

		/**
		 * Filtra dados do evento na resposta REST
		 *
		 * @param array $data    Dados preparados.
		 * @param int   $post_id ID do evento.
		 */
		return apply_filters( 'apollo_event_rest_data', $data, $post_id );
	}

	/**
	 * Monta query WP a partir dos parâmetros da requisição
	 */
	private function build_query_from_request( \WP_REST_Request $request ): array {
		$args = [
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => min( (int) ( $request->get_param( 'per_page' ) ?: 12 ), 100 ),
			'paged'          => max( (int) ( $request->get_param( 'page' ) ?: 1 ), 1 ),
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'order'          => 'ASC',
			'meta_query'     => [],
		];

		// Busca por texto
		$search = $request->get_param( 'search' );
		if ( $search ) {
			$args['s'] = sanitize_text_field( $search );
		}

		// Filtros de taxonomia
		$tax_query = [];

		$category = $request->get_param( 'category' );
		if ( $category ) {
			$tax_query[] = [
				'taxonomy' => APOLLO_EVENT_TAX_CATEGORY,
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $category ),
			];
		}

		$type = $request->get_param( 'type' );
		if ( $type ) {
			$tax_query[] = [
				'taxonomy' => APOLLO_EVENT_TAX_TYPE,
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $type ),
			];
		}

		$sound = $request->get_param( 'sound' );
		if ( $sound ) {
			$tax_query[] = [
				'taxonomy' => APOLLO_EVENT_TAX_SOUND,
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $sound ),
			];
		}

		$season = $request->get_param( 'season' );
		if ( $season ) {
			$tax_query[] = [
				'taxonomy' => APOLLO_EVENT_TAX_SEASON,
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $season ),
			];
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		// Filtro: ocultar gone se configurado
		if ( ! apollo_event_option( 'show_gone_events', true ) ) {
			$args['meta_query'][] = [
				'relation' => 'OR',
				[
					'key'     => '_event_is_gone',
					'compare' => 'NOT EXISTS',
				],
				[
					'key'     => '_event_is_gone',
					'value'   => '1',
					'compare' => '!=',
				],
			];
		}

		return $args;
	}

	/**
	 * Resposta paginada
	 */
	private function paginated_response( \WP_Query $query, \WP_REST_Request $request ): \WP_REST_Response {
		$events = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$events[] = $this->prepare_event( get_the_ID() );
			}
			wp_reset_postdata();
		}

		$response = new \WP_REST_Response( $events );
		$response->header( 'X-WP-Total', $query->found_posts );
		$response->header( 'X-WP-TotalPages', $query->max_num_pages );

		return $response;
	}

	/**
	 * Salva metas do evento
	 */
	private function save_event_meta( int $post_id, array $data ): void {
		$meta_map = [
			'start_date'   => '_event_start_date',
			'end_date'     => '_event_end_date',
			'start_time'   => '_event_start_time',
			'end_time'     => '_event_end_time',
			'loc_id'       => '_event_loc_id',
			'banner'       => '_event_banner',
			'ticket_url'   => '_event_ticket_url',
			'ticket_price' => '_event_ticket_price',
			'privacy'      => '_event_privacy',
			'event_status' => '_event_status',
		];

		foreach ( $meta_map as $key => $meta_key ) {
			if ( isset( $data[ $key ] ) ) {
				update_post_meta( $post_id, $meta_key, sanitize_text_field( $data[ $key ] ) );
			}
		}

		// Arrays
		if ( isset( $data['dj_ids'] ) && is_array( $data['dj_ids'] ) ) {
			update_post_meta( $post_id, '_event_dj_ids', array_map( 'intval', $data['dj_ids'] ) );
		}

		if ( isset( $data['dj_slots'] ) && is_array( $data['dj_slots'] ) ) {
			update_post_meta( $post_id, '_event_dj_slots', $data['dj_slots'] );
		}

		// Banner como thumbnail
		if ( isset( $data['banner'] ) ) {
			set_post_thumbnail( $post_id, (int) $data['banner'] );
		}
	}

	/**
	 * Salva taxonomias do evento
	 */
	private function save_event_taxonomies( int $post_id, array $data ): void {
		$tax_map = [
			'categories' => APOLLO_EVENT_TAX_CATEGORY,
			'types'      => APOLLO_EVENT_TAX_TYPE,
			'tags'       => APOLLO_EVENT_TAX_TAG,
			'sounds'     => APOLLO_EVENT_TAX_SOUND,
			'seasons'    => APOLLO_EVENT_TAX_SEASON,
		];

		foreach ( $tax_map as $key => $taxonomy ) {
			if ( isset( $data[ $key ] ) && is_array( $data[ $key ] ) ) {
				wp_set_object_terms( $post_id, $data[ $key ], $taxonomy );
			}
		}
	}

	/**
	 * Parâmetros de coleção (paginação + filtros)
	 */
	private function get_collection_params(): array {
		return [
			'per_page' => [
				'type'    => 'integer',
				'default' => 12,
				'minimum' => 1,
				'maximum' => 100,
			],
			'page' => [
				'type'    => 'integer',
				'default' => 1,
				'minimum' => 1,
			],
			'search'   => [ 'type' => 'string' ],
			'category' => [ 'type' => 'string' ],
			'type'     => [ 'type' => 'string' ],
			'sound'    => [ 'type' => 'string' ],
			'season'   => [ 'type' => 'string' ],
		];
	}

	/**
	 * Parâmetros de criação/atualização
	 */
	private function get_create_params(): array {
		return [
			'title'        => [ 'type' => 'string', 'required' => true ],
			'content'      => [ 'type' => 'string' ],
			'start_date'   => [ 'type' => 'string' ],
			'end_date'     => [ 'type' => 'string' ],
			'start_time'   => [ 'type' => 'string' ],
			'end_time'     => [ 'type' => 'string' ],
			'loc_id'       => [ 'type' => 'integer' ],
			'banner'       => [ 'type' => 'integer' ],
			'ticket_url'   => [ 'type' => 'string', 'format' => 'uri' ],
			'ticket_price' => [ 'type' => 'string' ],
			'privacy'      => [ 'type' => 'string', 'enum' => [ 'public', 'private', 'invite' ] ],
			'event_status' => [ 'type' => 'string', 'enum' => [ 'scheduled', 'cancelled', 'postponed', 'ongoing', 'finished' ] ],
			'dj_ids'       => [ 'type' => 'array', 'items' => [ 'type' => 'integer' ] ],
			'dj_slots'     => [ 'type' => 'array' ],
			'categories'   => [ 'type' => 'array' ],
			'types'        => [ 'type' => 'array' ],
			'tags'         => [ 'type' => 'array' ],
			'sounds'       => [ 'type' => 'array' ],
			'seasons'      => [ 'type' => 'array' ],
		];
	}
}
