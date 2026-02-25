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
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Registra todas as rotas REST
	 */
	public function register_routes(): void {

		// GET|POST /events
		register_rest_route(
			$this->namespace,
			'/events',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_events' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_event' ),
					'permission_callback' => array( $this, 'can_edit' ),
					'args'                => $this->get_create_params(),
				),
			)
		);

		// GET|PUT|DELETE /events/{id}
		register_rest_route(
			$this->namespace,
			'/events/(?P<id>\d+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_event' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'id' => array(
							'type'              => 'integer',
							'required'          => true,
							'validate_callback' => array( $this, 'validate_event_id' ),
						),
					),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_event' ),
					'permission_callback' => array( $this, 'can_edit_event' ),
					'args'                => $this->get_create_params(),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_event' ),
					'permission_callback' => array( $this, 'can_edit_event' ),
				),
			)
		);

		// GET /events/upcoming
		register_rest_route(
			$this->namespace,
			'/events/upcoming',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_upcoming' ),
				'permission_callback' => '__return_true',
				'args'                => $this->get_collection_params(),
			)
		);

		// GET /events/past
		register_rest_route(
			$this->namespace,
			'/events/past',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_past' ),
				'permission_callback' => '__return_true',
				'args'                => $this->get_collection_params(),
			)
		);

		// GET /events/today
		register_rest_route(
			$this->namespace,
			'/events/today',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_today' ),
				'permission_callback' => '__return_true',
			)
		);

		// GET /events/by-date/{date}
		register_rest_route(
			$this->namespace,
			'/events/by-date/(?P<date>[\d-]+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_by_date' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'date' => array(
						'type'              => 'string',
						'required'          => true,
						'validate_callback' => function ( $value ) {
							return (bool) preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value );
						},
					),
				),
			)
		);

		// GET /events/by-loc/{loc_id}
		register_rest_route(
			$this->namespace,
			'/events/by-loc/(?P<loc_id>\d+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_by_loc' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'loc_id' => array(
						'type'     => 'integer',
						'required' => true,
					),
				),
			)
		);

		// GET /events/by-dj/{dj_id}
		register_rest_route(
			$this->namespace,
			'/events/by-dj/(?P<dj_id>\d+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_by_dj' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'dj_id' => array(
						'type'     => 'integer',
						'required' => true,
					),
				),
			)
		);

		// GET|POST|DELETE /events/{id}/djs
		register_rest_route(
			$this->namespace,
			'/events/(?P<id>\d+)/djs',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_event_djs' ),
					'permission_callback' => '__return_true',
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'add_event_dj' ),
					'permission_callback' => array( $this, 'can_edit_event' ),
					'args'                => array(
						'dj_id' => array(
							'type'     => 'integer',
							'required' => true,
						),
						'slot'  => array(
							'type'     => 'object',
							'required' => false,
						),
					),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'remove_event_dj' ),
					'permission_callback' => array( $this, 'can_edit_event' ),
					'args'                => array(
						'dj_id' => array(
							'type'     => 'integer',
							'required' => true,
						),
					),
				),
			)
		);

		// GET /events/search
		register_rest_route(
			$this->namespace,
			'/events/search',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'search_events' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'q'        => array(
						'type'     => 'string',
						'required' => true,
					),
					'per_page' => array(
						'type'    => 'integer',
						'default' => 12,
					),
					'page'     => array(
						'type'    => 'integer',
						'default' => 1,
					),
				),
			)
		);

		// GET /events/calendar/{year}/{month}
		register_rest_route(
			$this->namespace,
			'/events/calendar/(?P<year>\d{4})/(?P<month>\d{1,2})',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_calendar_month' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'year'  => array(
						'type'     => 'integer',
						'required' => true,
					),
					'month' => array(
						'type'     => 'integer',
						'required' => true,
						'minimum'  => 1,
						'maximum'  => 12,
					),
				),
			)
		);

		// POST /events/{id}/banner
		register_rest_route(
			$this->namespace,
			'/events/(?P<id>\d+)/banner',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'upload_event_banner' ),
					'permission_callback' => array( $this, 'can_edit_event' ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_event_banner' ),
					'permission_callback' => array( $this, 'can_edit_event' ),
				),
			)
		);

		// POST /events/{id}/clone
		register_rest_route(
			$this->namespace,
			'/events/(?P<id>\d+)/clone',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'clone_event' ),
				'permission_callback' => array( $this, 'can_edit_event' ),
			)
		);

		// GET /events/{id}/stats
		register_rest_route(
			$this->namespace,
			'/events/(?P<id>\d+)/stats',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_event_stats' ),
				'permission_callback' => '__return_true',
			)
		);

		// GET /events/{id}/attendees + POST (RSVP) + DELETE (cancel)
		register_rest_route(
			$this->namespace,
			'/events/(?P<id>\d+)/attendees',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_event_attendees' ),
					'permission_callback' => array( $this, 'can_edit_event' ),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'rsvp_event' ),
					'permission_callback' => function () {
						return is_user_logged_in();
					},
					'args'                => array(
						'status' => array(
							'type'    => 'string',
							'default' => 'going',
							'enum'    => array( 'going', 'interested', 'not_going' ),
						),
					),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'cancel_rsvp' ),
					'permission_callback' => function () {
						return is_user_logged_in();
					},
				),
			)
		);

		// GET /events/{id}/attendees/check-in — mod/admin only
		register_rest_route(
			$this->namespace,
			'/events/(?P<id>\d+)/attendees/(?P<user_id>\d+)/check-in',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'check_in_attendee' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'id'      => array(
						'type'     => 'integer',
						'required' => true,
					),
					'user_id' => array(
						'type'     => 'integer',
						'required' => true,
					),
				),
			)
		);

		// GET /events/my — authenticated user's events
		register_rest_route(
			$this->namespace,
			'/events/my',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_my_events' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
				'args'                => array(
					'per_page' => array(
						'type'    => 'integer',
						'default' => 12,
					),
					'page'     => array(
						'type'    => 'integer',
						'default' => 1,
					),
				),
			)
		);

		// GET /events/my/rsvp — user's RSVP'd events
		register_rest_route(
			$this->namespace,
			'/events/my/rsvp',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_my_rsvp_events' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
				'args'                => array(
					'per_page' => array(
						'type'    => 'integer',
						'default' => 12,
					),
					'page'     => array(
						'type'    => 'integer',
						'default' => 1,
					),
					'status'   => array(
						'type'    => 'string',
						'default' => 'going',
					),
				),
			)
		);

		// POST /events/batch — bulk operations
		register_rest_route(
			$this->namespace,
			'/events/batch',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'batch_events' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'action' => array(
						'type'     => 'string',
						'required' => true,
						'enum'     => array( 'delete', 'publish', 'draft', 'cancel' ),
					),
					'ids'    => array(
						'type'     => 'array',
						'required' => true,
						'items'    => array( 'type' => 'integer' ),
					),
				),
			)
		);
	}

	// ─── Callbacks ─────────────────────────────────────────────────────

	/**
	 * GET /events — Listagem
	 */
	public function get_events( \WP_REST_Request $request ): \WP_REST_Response {
		$args  = $this->build_query_from_request( $request );
		$query = new \WP_Query( $args );

		return $this->paginated_response( $query, $request );
	}

	/**
	 * POST /events — Criar evento
	 */
	public function create_event( \WP_REST_Request $request ): \WP_REST_Response {
		$data = $request->get_json_params();
		if ( ! is_array( $data ) || empty( $data ) ) {
			$data = $request->get_params();
		}

		$post_status = sanitize_text_field( (string) ( $data['post_status'] ?? 'publish' ) );
		if ( ! in_array( $post_status, array( 'publish', 'draft', 'pending' ), true ) ) {
			$post_status = 'publish';
		}

		$post_data = array(
			'post_type'    => APOLLO_EVENT_CPT,
			'post_status'  => $post_status,
			'post_author'  => get_current_user_id(),
			'post_title'   => sanitize_text_field( $data['title'] ?? '' ),
			'post_content' => wp_kses_post( $data['content'] ?? '' ),
		);

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return new \WP_REST_Response( array( 'error' => $post_id->get_error_message() ), 400 );
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
		do_action( 'apollo/event/created', $post_id, $data );

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

		$post_data = array( 'ID' => $id );

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
		do_action( 'apollo/event/updated', $id, $data );

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
		do_action( 'apollo/event/before-delete', $id );

		$result = wp_trash_post( $id );

		if ( ! $result ) {
			return new \WP_REST_Response( array( 'error' => 'Falha ao excluir evento.' ), 500 );
		}

		do_action( 'apollo/event/deleted', $id );

		return new \WP_REST_Response(
			array(
				'deleted' => true,
				'id'      => $id,
			)
		);
	}

	/**
	 * GET /events/upcoming — Eventos futuros
	 */
	public function get_upcoming( \WP_REST_Request $request ): \WP_REST_Response {
		$args                 = $this->build_query_from_request( $request );
		$args['meta_query'][] = array(
			'key'     => '_event_start_date',
			'value'   => current_time( 'Y-m-d' ),
			'compare' => '>=',
			'type'    => 'DATE',
		);

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/past — Eventos passados
	 */
	public function get_past( \WP_REST_Request $request ): \WP_REST_Response {
		$args                 = $this->build_query_from_request( $request );
		$args['meta_query'][] = array(
			'key'     => '_event_start_date',
			'value'   => current_time( 'Y-m-d' ),
			'compare' => '<',
			'type'    => 'DATE',
		);
		$args['order']        = 'DESC';

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/today — Eventos de hoje
	 */
	public function get_today( \WP_REST_Request $request ): \WP_REST_Response {
		$today = current_time( 'Y-m-d' );

		$args = array(
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_time',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_event_start_date',
					'value'   => $today,
					'compare' => '=',
				),
			),
		);

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/by-date/{date} — Eventos por data
	 */
	public function get_by_date( \WP_REST_Request $request ): \WP_REST_Response {
		$date = sanitize_text_field( $request->get_param( 'date' ) );

		$args = array(
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_time',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_event_start_date',
					'value'   => $date,
					'compare' => '=',
				),
			),
		);

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/by-loc/{loc_id} — Eventos por local
	 */
	public function get_by_loc( \WP_REST_Request $request ): \WP_REST_Response {
		$loc_id = (int) $request->get_param( 'loc_id' );

		$args = array(
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_event_loc_id',
					'value'   => $loc_id,
					'compare' => '=',
					'type'    => 'NUMERIC',
				),
			),
		);

		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/by-dj/{dj_id} — Eventos por DJ
	 */
	public function get_by_dj( \WP_REST_Request $request ): \WP_REST_Response {
		$dj_id = (int) $request->get_param( 'dj_id' );

		// _event_dj_ids é serialized array — busca com LIKE
		$args = array(
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_event_dj_ids',
					'value'   => sprintf( '"%d"', $dj_id ),
					'compare' => 'LIKE',
				),
			),
		);

		// Fallback: busca com i:{n};i:{dj_id}
		$args_alt                           = $args;
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
		$slots    = get_post_meta( $id, '_event_dj_slots', true ) ?: array();
		$enriched = array();

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
			return new \WP_REST_Response( array( 'error' => 'DJ não encontrado.' ), 404 );
		}

		// Adicionar DJ ao array
		$dj_ids = get_post_meta( $id, '_event_dj_ids', true ) ?: array();
		if ( ! is_array( $dj_ids ) ) {
			$dj_ids = array();
		}

		if ( ! in_array( $dj_id, $dj_ids, true ) ) {
			$dj_ids[] = $dj_id;
			update_post_meta( $id, '_event_dj_ids', $dj_ids );
		}

		// Salvar slot (se fornecido)
		if ( $slot && is_array( $slot ) ) {
			$slots = get_post_meta( $id, '_event_dj_slots', true ) ?: array();
			if ( ! is_array( $slots ) ) {
				$slots = array();
			}
			$slot['dj_id'] = $dj_id;
			$slots[]       = $slot;
			update_post_meta( $id, '_event_dj_slots', $slots );
		}

		/**
		 * Ação após adicionar DJ ao evento
		 */
		do_action( 'apollo_event_dj_added', $id, $dj_id, $slot );
		do_action( 'apollo/event/dj-added', $id, $dj_id, $slot );

		return new \WP_REST_Response(
			array(
				'added'    => true,
				'event_id' => $id,
				'dj_id'    => $dj_id,
			),
			201
		);
	}

	/**
	 * DELETE /events/{id}/djs — Remover DJ do evento
	 */
	public function remove_event_dj( \WP_REST_Request $request ): \WP_REST_Response {
		$id    = (int) $request->get_param( 'id' );
		$dj_id = (int) $request->get_param( 'dj_id' );

		// Remover do array de IDs
		$dj_ids = get_post_meta( $id, '_event_dj_ids', true ) ?: array();
		if ( is_array( $dj_ids ) ) {
			$dj_ids = array_values( array_filter( $dj_ids, fn( $d ) => (int) $d !== $dj_id ) );
			update_post_meta( $id, '_event_dj_ids', $dj_ids );
		}

		// Remover slot
		$slots = get_post_meta( $id, '_event_dj_slots', true ) ?: array();
		if ( is_array( $slots ) ) {
			$slots = array_values( array_filter( $slots, fn( $s ) => (int) ( $s['dj_id'] ?? 0 ) !== $dj_id ) );
			update_post_meta( $id, '_event_dj_slots', $slots );
		}

		/**
		 * Ação após remover DJ do evento
		 */
		do_action( 'apollo_event_dj_removed', $id, $dj_id );
		do_action( 'apollo/event/dj-removed', $id, $dj_id );

		return new \WP_REST_Response(
			array(
				'removed'  => true,
				'event_id' => $id,
				'dj_id'    => $dj_id,
			)
		);
	}

	// ─── New Callbacks ─────────────────────────────────────────────────

	/**
	 * GET /events/search
	 */
	public function search_events( \WP_REST_Request $request ): \WP_REST_Response {
		$q     = sanitize_text_field( (string) $request->get_param( 'q' ) );
		$args  = array(
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => min( (int) ( $request->get_param( 'per_page' ) ?: 12 ), 100 ),
			'paged'          => max( (int) ( $request->get_param( 'page' ) ?: 1 ), 1 ),
			's'              => $q,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'order'          => 'ASC',
		);
		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/calendar/{year}/{month}
	 * Returns events grouped by day for a given month.
	 */
	public function get_calendar_month( \WP_REST_Request $request ): \WP_REST_Response {
		$year  = (int) $request->get_param( 'year' );
		$month = (int) $request->get_param( 'month' );

		$date_from = sprintf( '%04d-%02d-01', $year, $month );
		$date_to   = date( 'Y-m-t', strtotime( $date_from ) );

		$args = array(
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_event_start_date',
					'value'   => array( $date_from, $date_to ),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				),
			),
		);

		$query  = new \WP_Query( $args );
		$by_day = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id         = get_the_ID();
				$start_date = get_post_meta( $id, '_event_start_date', true );
				$day        = (int) date( 'j', strtotime( $start_date ) );
				if ( ! isset( $by_day[ $day ] ) ) {
					$by_day[ $day ] = array();
				}
				$by_day[ $day ][] = array(
					'id'         => $id,
					'title'      => get_the_title(),
					'start_date' => $start_date,
					'start_time' => get_post_meta( $id, '_event_start_time', true ),
					'status'     => get_post_meta( $id, '_event_status', true ) ?: 'scheduled',
					'is_gone'    => apollo_event_is_gone( $id ),
					'banner'     => apollo_event_get_banner( $id, 'thumbnail' ),
					'permalink'  => get_permalink( $id ),
				);
			}
			wp_reset_postdata();
		}

		return new \WP_REST_Response(
			array(
				'year'   => $year,
				'month'  => $month,
				'total'  => $query->found_posts,
				'by_day' => $by_day,
			)
		);
	}

	/**
	 * POST /events/{id}/banner — Upload banner via multipart
	 */
	public function upload_event_banner( \WP_REST_Request $request ): \WP_REST_Response {
		$id    = (int) $request->get_param( 'id' );
		$files = $request->get_file_params();

		if ( empty( $files['banner']['tmp_name'] ) ) {
			return new \WP_REST_Response( array( 'error' => 'No file uploaded.' ), 400 );
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$attachment_id = media_handle_upload( 'banner', $id );
		if ( is_wp_error( $attachment_id ) ) {
			return new \WP_REST_Response( array( 'error' => $attachment_id->get_error_message() ), 500 );
		}

		update_post_meta( $id, '_event_banner', $attachment_id );
		set_post_thumbnail( $id, $attachment_id );

		return new \WP_REST_Response(
			array(
				'banner_id'  => $attachment_id,
				'banner_url' => wp_get_attachment_image_url( $attachment_id, 'large' ),
			),
			201
		);
	}

	/**
	 * DELETE /events/{id}/banner
	 */
	public function delete_event_banner( \WP_REST_Request $request ): \WP_REST_Response {
		$id = (int) $request->get_param( 'id' );
		delete_post_meta( $id, '_event_banner' );
		delete_post_thumbnail( $id );
		return new \WP_REST_Response( array( 'deleted' => true ) );
	}

	/**
	 * POST /events/{id}/clone — Duplicates event, metadata & taxonomies
	 */
	public function clone_event( \WP_REST_Request $request ): \WP_REST_Response {
		$id   = (int) $request->get_param( 'id' );
		$post = get_post( $id );
		if ( ! $post || APOLLO_EVENT_CPT !== $post->post_type ) {
			return new \WP_REST_Response( array( 'error' => 'Event not found.' ), 404 );
		}

		$new_id = wp_insert_post(
			array(
				'post_type'    => APOLLO_EVENT_CPT,
				'post_status'  => 'draft',
				'post_author'  => get_current_user_id(),
				'post_title'   => $post->post_title . ' (cópia)',
				'post_content' => $post->post_content,
				'post_excerpt' => $post->post_excerpt,
			),
			true
		);

		if ( is_wp_error( $new_id ) ) {
			return new \WP_REST_Response( array( 'error' => $new_id->get_error_message() ), 500 );
		}

		// Copy all meta
		foreach ( APOLLO_EVENT_META_KEYS as $key ) {
			$val = get_post_meta( $id, $key, true );
			if ( '' !== $val ) {
				update_post_meta( $new_id, $key, $val );
			}
		}
		// Clear expiration flag on clone
		delete_post_meta( $new_id, '_event_is_gone' );

		// Copy taxonomies
		foreach ( array( APOLLO_EVENT_TAX_CATEGORY, APOLLO_EVENT_TAX_TYPE, APOLLO_EVENT_TAX_TAG, APOLLO_EVENT_TAX_SOUND, APOLLO_EVENT_TAX_SEASON ) as $tax ) {
			$terms = wp_get_object_terms( $id, $tax, array( 'fields' => 'slugs' ) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				wp_set_object_terms( $new_id, $terms, $tax );
			}
		}

		do_action( 'apollo/event/cloned', $new_id, $id );

		return new \WP_REST_Response( $this->prepare_event( $new_id ), 201 );
	}

	/**
	 * GET /events/{id}/stats
	 */
	public function get_event_stats( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$id    = (int) $request->get_param( 'id' );
		$table = $wpdb->prefix . 'apollo_event_rsvp';

		$views = (int) get_post_meta( $id, '_event_view_count', true );
		$fav   = (int) get_post_meta( $id, '_apollo_fav_count', true );
		$wow   = (int) get_post_meta( $id, '_apollo_wow_count', true );

		// RSVP counts
		$going      = 0;
		$interested = 0;
		$not_going  = 0;
		$checked_in = 0;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
			$going      = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE event_id=%d AND status='going'", $id ) );
			$interested = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE event_id=%d AND status='interested'", $id ) );
			$not_going  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE event_id=%d AND status='not_going'", $id ) );
			$checked_in = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE event_id=%d AND checked_in=1", $id ) );
		}

		return new \WP_REST_Response(
			array(
				'event_id'  => $id,
				'views'     => $views,
				'fav_count' => $fav,
				'wow_count' => $wow,
				'rsvp'      => array(
					'going'      => $going,
					'interested' => $interested,
					'not_going'  => $not_going,
					'checked_in' => $checked_in,
					'total'      => $going + $interested + $not_going,
				),
			)
		);
	}

	/**
	 * GET /events/{id}/attendees
	 */
	public function get_event_attendees( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$id    = (int) $request->get_param( 'id' );
		$table = $wpdb->prefix . 'apollo_event_rsvp';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			return new \WP_REST_Response(
				array(
					'attendees' => array(),
					'total'     => 0,
				)
			);
		}

		$status = sanitize_text_field( (string) ( $request->get_param( 'status' ) ?: '' ) );
		$where  = $wpdb->prepare( 'WHERE event_id = %d', $id );
		if ( in_array( $status, array( 'going', 'interested', 'not_going' ), true ) ) {
			$where .= $wpdb->prepare( ' AND status = %s', $status );
		}

		$rows = $wpdb->get_results( "SELECT user_id, status, checked_in, created_at FROM {$table} {$where} ORDER BY created_at DESC" );

		$attendees = array();
		foreach ( (array) $rows as $row ) {
			$user = get_userdata( (int) $row->user_id );
			if ( ! $user ) {
				continue;
			}
			$attendees[] = array(
				'user_id'    => (int) $row->user_id,
				'login'      => $user->user_login,
				'name'       => $user->display_name,
				'avatar'     => get_avatar_url( (int) $row->user_id ),
				'status'     => $row->status,
				'checked_in' => (bool) $row->checked_in,
				'rsvp_at'    => $row->created_at,
			);
		}

		return new \WP_REST_Response(
			array(
				'event_id'  => $id,
				'attendees' => $attendees,
				'total'     => count( $attendees ),
			)
		);
	}

	/**
	 * POST /events/{id}/attendees — RSVP to event
	 */
	public function rsvp_event( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$id     = (int) $request->get_param( 'id' );
		$uid    = get_current_user_id();
		$status = sanitize_text_field( (string) ( $request->get_param( 'status' ) ?: 'going' ) );
		if ( ! in_array( $status, array( 'going', 'interested', 'not_going' ), true ) ) {
			$status = 'going';
		}

		$table    = $wpdb->prefix . 'apollo_event_rsvp';
		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$table} WHERE event_id=%d AND user_id=%d",
				$id,
				$uid
			)
		);

		if ( $existing ) {
			$wpdb->update(
				$table,
				array( 'status' => $status ),
				array(
					'event_id' => $id,
					'user_id'  => $uid,
				)
			);
		} else {
			$wpdb->insert(
				$table,
				array(
					'event_id'   => $id,
					'user_id'    => $uid,
					'status'     => $status,
					'checked_in' => 0,
					'created_at' => current_time( 'mysql' ),
				)
			);
		}

		do_action( 'apollo/event/rsvp', $id, $uid, $status );

		// Notify event author
		$author_id = (int) get_post_field( 'post_author', $id );
		if ( $author_id && $author_id !== $uid && function_exists( 'apollo_create_notification' ) ) {
			$user = get_userdata( $uid );
			apollo_create_notification(
				$author_id,
				'event_rsvp',
				sprintf( '%s marcou "%s" no seu evento.', $user ? $user->display_name : 'Alguém', get_the_title( $id ) ),
				get_avatar_url( $uid ),
				get_permalink( $id ),
				array(
					'event_id' => $id,
					'user_id'  => $uid,
					'status'   => $status,
				),
				array(
					'icon'    => 'ri-calendar-check-line',
					'channel' => 'apollo/event',
				)
			);
		}

		return new \WP_REST_Response(
			array(
				'rsvp'     => $status,
				'event_id' => $id,
			),
			200
		);
	}

	/**
	 * DELETE /events/{id}/attendees — Cancel RSVP
	 */
	public function cancel_rsvp( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$id  = (int) $request->get_param( 'id' );
		$uid = get_current_user_id();

		$wpdb->delete(
			$wpdb->prefix . 'apollo_event_rsvp',
			array(
				'event_id' => $id,
				'user_id'  => $uid,
			)
		);
		do_action( 'apollo/event/rsvp_cancelled', $id, $uid );

		return new \WP_REST_Response( array( 'cancelled' => true ) );
	}

	/**
	 * POST /events/{id}/attendees/{user_id}/check-in
	 */
	public function check_in_attendee( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$id      = (int) $request->get_param( 'id' );
		$user_id = (int) $request->get_param( 'user_id' );
		$table   = $wpdb->prefix . 'apollo_event_rsvp';

		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$table} WHERE event_id=%d AND user_id=%d",
				$id,
				$user_id
			)
		);

		if ( ! $existing ) {
			// Auto-register as going on check-in (walk-ins)
			$wpdb->insert(
				$table,
				array(
					'event_id'   => $id,
					'user_id'    => $user_id,
					'status'     => 'going',
					'checked_in' => 1,
					'created_at' => current_time( 'mysql' ),
				)
			);
		} else {
			$wpdb->update(
				$table,
				array( 'checked_in' => 1 ),
				array(
					'event_id' => $id,
					'user_id'  => $user_id,
				)
			);
		}

		do_action( 'apollo/event/checked_in', $id, $user_id );

		return new \WP_REST_Response(
			array(
				'checked_in' => true,
				'event_id'   => $id,
				'user_id'    => $user_id,
			)
		);
	}

	/**
	 * GET /events/my
	 */
	public function get_my_events( \WP_REST_Request $request ): \WP_REST_Response {
		$uid   = get_current_user_id();
		$args  = array(
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => array( 'publish', 'draft', 'pending' ),
			'author'         => $uid,
			'posts_per_page' => min( (int) ( $request->get_param( 'per_page' ) ?: 12 ), 100 ),
			'paged'          => max( (int) ( $request->get_param( 'page' ) ?: 1 ), 1 ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * GET /events/my/rsvp
	 */
	public function get_my_rsvp_events( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$uid    = get_current_user_id();
		$status = sanitize_text_field( (string) ( $request->get_param( 'status' ) ?: 'going' ) );
		if ( ! in_array( $status, array( 'going', 'interested', 'not_going' ), true ) ) {
			$status = 'going';
		}

		$table = $wpdb->prefix . 'apollo_event_rsvp';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			return new \WP_REST_Response( array() );
		}

		$event_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT event_id FROM {$table} WHERE user_id=%d AND status=%s ORDER BY created_at DESC",
				$uid,
				$status
			)
		);

		if ( empty( $event_ids ) ) {
			$response = new \WP_REST_Response( array() );
			$response->header( 'X-WP-Total', 0 );
			$response->header( 'X-WP-TotalPages', 0 );
			return $response;
		}

		$per_page = min( (int) ( $request->get_param( 'per_page' ) ?: 12 ), 100 );
		$page     = max( (int) ( $request->get_param( 'page' ) ?: 1 ), 1 );

		$args  = array(
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'post__in'       => array_map( 'intval', $event_ids ),
			'orderby'        => 'post__in',
			'posts_per_page' => $per_page,
			'paged'          => $page,
		);
		$query = new \WP_Query( $args );
		return $this->paginated_response( $query, $request );
	}

	/**
	 * POST /events/batch
	 */
	public function batch_events( \WP_REST_Request $request ): \WP_REST_Response {
		$action = sanitize_key( (string) $request->get_param( 'action' ) );
		$ids    = array_map( 'intval', (array) $request->get_param( 'ids' ) );
		$done   = array();
		$failed = array();

		foreach ( $ids as $id ) {
			$post = get_post( $id );
			if ( ! $post || APOLLO_EVENT_CPT !== $post->post_type ) {
				$failed[] = $id;
				continue;
			}
			if ( ! current_user_can( 'edit_post', $id ) ) {
				$failed[] = $id;
				continue;
			}
			switch ( $action ) {
				case 'delete':
					wp_trash_post( $id );
					$done[] = $id;
					break;
				case 'publish':
					wp_update_post(
						array(
							'ID'          => $id,
							'post_status' => 'publish',
						)
					);
					$done[] = $id;
					break;
				case 'draft':
					wp_update_post(
						array(
							'ID'          => $id,
							'post_status' => 'draft',
						)
					);
					$done[] = $id;
					break;
				case 'cancel':
					update_post_meta( $id, '_event_status', 'cancelled' );
					$done[] = $id;
					break;
				default:
					$failed[] = $id;
			}
		}

		return new \WP_REST_Response(
			array(
				'action' => $action,
				'done'   => $done,
				'failed' => $failed,
			)
		);
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
			return array();
		}

		$djs = apollo_event_get_djs( $post_id );
		$loc = apollo_event_get_loc( $post_id );

		$data = array(
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
			'dj_ids'       => get_post_meta( $post_id, '_event_dj_ids', true ) ?: array(),
			'djs'          => $djs,
			'dj_slots'     => get_post_meta( $post_id, '_event_dj_slots', true ) ?: array(),
			'loc_id'       => (int) get_post_meta( $post_id, '_event_loc_id', true ),
			'loc'          => $loc,
			'ticket_url'   => get_post_meta( $post_id, '_event_ticket_url', true ),
			'ticket_price' => get_post_meta( $post_id, '_event_ticket_price', true ),
			'coupon_code'  => get_post_meta( $post_id, '_event_coupon_code', true ),
			'list_url'     => get_post_meta( $post_id, '_event_list_url', true ),
			'video_url'    => get_post_meta( $post_id, '_event_video_url', true ),
			'gallery'      => (array) ( get_post_meta( $post_id, '_event_gallery', true ) ?: array() ),
			'privacy'      => get_post_meta( $post_id, '_event_privacy', true ) ?: 'public',
			'event_status' => get_post_meta( $post_id, '_event_status', true ) ?: 'scheduled',
			'is_gone'      => apollo_event_is_gone( $post_id ),
			'view_count'   => (int) get_post_meta( $post_id, '_event_view_count', true ),
			'categories'   => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_CATEGORY, array( 'fields' => 'all' ) ),
			'types'        => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_TYPE, array( 'fields' => 'all' ) ),
			'tags'         => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_TAG, array( 'fields' => 'all' ) ),
			'sounds'       => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_SOUND, array( 'fields' => 'all' ) ),
			'seasons'      => wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_SEASON, array( 'fields' => 'all' ) ),
			'created'      => $post->post_date,
			'modified'     => $post->post_modified,
			'my_rsvp'      => $this->get_current_user_rsvp( $post_id ),
		);

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
		$events_per_page = (int) apollo_event_option( 'events_per_page', 12 );
		$events_per_page = max( 1, min( 100, $events_per_page ) );

		$order_by = sanitize_text_field( (string) ( $request->get_param( 'orderby' ) ?: 'start_date' ) );
		$order    = strtoupper( sanitize_text_field( (string) ( $request->get_param( 'order' ) ?: 'ASC' ) ) );
		if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			$order = 'ASC';
		}

		$args = array(
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => min( (int) ( $request->get_param( 'per_page' ) ?: $events_per_page ), 100 ),
			'paged'          => max( (int) ( $request->get_param( 'page' ) ?: 1 ), 1 ),
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'order'          => $order,
			'meta_query'     => array(),
		);

		switch ( $order_by ) {
			case 'title':
				$args['orderby'] = 'title';
				unset( $args['meta_key'] );
				break;

			case 'created':
				$args['orderby'] = 'date';
				unset( $args['meta_key'] );
				break;

			case 'start_time':
				$args['orderby']  = 'meta_value';
				$args['meta_key'] = '_event_start_time';
				break;

			case 'start_date':
			default:
				$args['orderby']  = 'meta_value';
				$args['meta_key'] = '_event_start_date';
				break;
		}

		// Busca por texto
		$search = $request->get_param( 'search' );
		if ( $search ) {
			$args['s'] = sanitize_text_field( $search );
		}

		// Filtros de taxonomia
		$tax_query = array();

		$category = $request->get_param( 'category' );
		if ( $category ) {
			$tax_query[] = array(
				'taxonomy' => APOLLO_EVENT_TAX_CATEGORY,
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $category ),
			);
		}

		$type = $request->get_param( 'type' );
		if ( $type ) {
			$tax_query[] = array(
				'taxonomy' => APOLLO_EVENT_TAX_TYPE,
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $type ),
			);
		}

		$sound = $request->get_param( 'sound' );
		if ( $sound ) {
			$tax_query[] = array(
				'taxonomy' => APOLLO_EVENT_TAX_SOUND,
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $sound ),
			);
		}

		$season = $request->get_param( 'season' );
		if ( $season ) {
			$tax_query[] = array(
				'taxonomy' => APOLLO_EVENT_TAX_SEASON,
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $season ),
			);
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		$date_from = sanitize_text_field( (string) ( $request->get_param( 'date_from' ) ?: '' ) );
		$date_to   = sanitize_text_field( (string) ( $request->get_param( 'date_to' ) ?: '' ) );

		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_from ) ) {
			$args['meta_query'][] = array(
				'key'     => '_event_start_date',
				'value'   => $date_from,
				'compare' => '>=',
				'type'    => 'DATE',
			);
		}

		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date_to ) ) {
			$args['meta_query'][] = array(
				'key'     => '_event_start_date',
				'value'   => $date_to,
				'compare' => '<=',
				'type'    => 'DATE',
			);
		}

		$loc_id = (int) ( $request->get_param( 'loc_id' ) ?: 0 );
		if ( $loc_id > 0 ) {
			$args['meta_query'][] = array(
				'key'     => '_event_loc_id',
				'value'   => $loc_id,
				'compare' => '=',
				'type'    => 'NUMERIC',
			);
		}

		$dj_id = (int) ( $request->get_param( 'dj_id' ) ?: 0 );
		if ( $dj_id > 0 ) {
			$args['meta_query'][] = array(
				'key'     => '_event_dj_ids',
				'value'   => sprintf( '"%d"', $dj_id ),
				'compare' => 'LIKE',
			);
		}

		$privacy = sanitize_text_field( (string) ( $request->get_param( 'privacy' ) ?: '' ) );
		if ( in_array( $privacy, array( 'public', 'private', 'invite' ), true ) ) {
			$args['meta_query'][] = array(
				'key'     => '_event_privacy',
				'value'   => $privacy,
				'compare' => '=',
			);
		}

		$event_status = sanitize_text_field( (string) ( $request->get_param( 'event_status' ) ?: '' ) );
		if ( in_array( $event_status, array( 'scheduled', 'cancelled', 'postponed', 'ongoing', 'finished' ), true ) ) {
			$args['meta_query'][] = array(
				'key'     => '_event_status',
				'value'   => $event_status,
				'compare' => '=',
			);
		}

		// Filtro: ocultar gone se configurado
		$include_gone = $request->get_param( 'include_gone' );
		$allow_gone   = apollo_event_option( 'show_gone_events', true );
		if ( null !== $include_gone ) {
			$allow_gone = filter_var( $include_gone, FILTER_VALIDATE_BOOLEAN );
		}

		if ( ! $allow_gone ) {
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => '_event_is_gone',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_event_is_gone',
					'value'   => '1',
					'compare' => '!=',
				),
			);
		}

		return $args;
	}

	/**
	 * Resposta paginada
	 */
	private function paginated_response( \WP_Query $query, \WP_REST_Request $request ): \WP_REST_Response {
		$events = array();

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
		$meta_map = array(
			'start_date'   => '_event_start_date',
			'end_date'     => '_event_end_date',
			'start_time'   => '_event_start_time',
			'end_time'     => '_event_end_time',
			'loc_id'       => '_event_loc_id',
			'banner'       => '_event_banner',
			'ticket_url'   => '_event_ticket_url',
			'ticket_price' => '_event_ticket_price',
			'coupon_code'  => '_event_coupon_code',
			'list_url'     => '_event_list_url',
			'video_url'    => '_event_video_url',
			'privacy'      => '_event_privacy',
			'event_status' => '_event_status',
		);

		foreach ( $meta_map as $key => $meta_key ) {
			if ( isset( $data[ $key ] ) ) {
				update_post_meta( $post_id, $meta_key, $this->sanitize_meta_value( $meta_key, $data[ $key ] ) );
			}
		}

		// Arrays
		if ( isset( $data['dj_ids'] ) && is_array( $data['dj_ids'] ) ) {
			update_post_meta( $post_id, '_event_dj_ids', array_map( 'intval', $data['dj_ids'] ) );
		}

		if ( isset( $data['dj_slots'] ) && is_array( $data['dj_slots'] ) ) {
			update_post_meta( $post_id, '_event_dj_slots', $data['dj_slots'] );
		}

		// Gallery
		if ( isset( $data['gallery'] ) && is_array( $data['gallery'] ) ) {
			update_post_meta( $post_id, '_event_gallery', array_map( 'absint', $data['gallery'] ) );
		}

		// Banner como thumbnail
		if ( isset( $data['banner'] ) ) {
			set_post_thumbnail( $post_id, (int) $data['banner'] );
		}
	}

	/**
	 * Sanitiza valor de meta baseado na chave oficial do registry.
	 */
	private function sanitize_meta_value( string $meta_key, $value ) {
		switch ( $meta_key ) {
			case '_event_loc_id':
			case '_event_banner':
				return absint( $value );

			case '_event_ticket_url':
			case '_event_list_url':
			case '_event_video_url':
				return esc_url_raw( (string) $value );

			case '_event_privacy':
				$privacy = sanitize_text_field( (string) $value );
				return in_array( $privacy, array( 'public', 'private', 'invite' ), true ) ? $privacy : 'public';

			case '_event_status':
				$event_status = sanitize_text_field( (string) $value );
				return in_array( $event_status, array( 'scheduled', 'cancelled', 'postponed', 'ongoing', 'finished' ), true ) ? $event_status : 'scheduled';

			default:
				return sanitize_text_field( (string) $value );
		}
	}

	/**
	 * Retorna o status RSVP do usuário logado para um evento.
	 */
	private function get_current_user_rsvp( int $post_id ): ?string {
		if ( ! is_user_logged_in() ) {
			return null;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'apollo_event_rsvp';

		// Verifica se a tabela existe
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
			return null;
		}

		$row = $wpdb->get_row(
			$wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT status, checked_in FROM {$table} WHERE event_id = %d AND user_id = %d",
				$post_id,
				get_current_user_id()
			)
		);

		if ( ! $row ) {
			return null;
		}

		return $row->status . ( $row->checked_in ? ':checked_in' : '' );
	}

	/**
	 * Salva taxonomias do evento
	 */
	private function save_event_taxonomies( int $post_id, array $data ): void {
		$tax_map = array(
			'categories' => APOLLO_EVENT_TAX_CATEGORY,
			'types'      => APOLLO_EVENT_TAX_TYPE,
			'tags'       => APOLLO_EVENT_TAX_TAG,
			'sounds'     => APOLLO_EVENT_TAX_SOUND,
			'seasons'    => APOLLO_EVENT_TAX_SEASON,
		);

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
		return array(
			'per_page'     => array(
				'type'    => 'integer',
				'default' => 12,
				'minimum' => 1,
				'maximum' => 100,
			),
			'page'         => array(
				'type'    => 'integer',
				'default' => 1,
				'minimum' => 1,
			),
			'search'       => array( 'type' => 'string' ),
			'category'     => array( 'type' => 'string' ),
			'type'         => array( 'type' => 'string' ),
			'sound'        => array( 'type' => 'string' ),
			'season'       => array( 'type' => 'string' ),
			'date_from'    => array( 'type' => 'string' ),
			'date_to'      => array( 'type' => 'string' ),
			'loc_id'       => array( 'type' => 'integer' ),
			'dj_id'        => array( 'type' => 'integer' ),
			'privacy'      => array( 'type' => 'string' ),
			'event_status' => array( 'type' => 'string' ),
			'include_gone' => array( 'type' => 'boolean' ),
			'orderby'      => array( 'type' => 'string' ),
			'order'        => array( 'type' => 'string' ),
		);
	}

	/**
	 * Parâmetros de criação/atualização
	 */
	private function get_create_params(): array {
		return array(
			'title'        => array(
				'type'     => 'string',
				'required' => true,
			),
			'content'      => array( 'type' => 'string' ),
			'post_status'  => array(
				'type' => 'string',
				'enum' => array( 'publish', 'draft', 'pending' ),
			),
			'start_date'   => array( 'type' => 'string' ),
			'end_date'     => array( 'type' => 'string' ),
			'start_time'   => array( 'type' => 'string' ),
			'end_time'     => array( 'type' => 'string' ),
			'loc_id'       => array( 'type' => 'integer' ),
			'banner'       => array( 'type' => 'integer' ),
			'ticket_url'   => array(
				'type'   => 'string',
				'format' => 'uri',
			),
			'ticket_price' => array( 'type' => 'string' ),
			'privacy'      => array(
				'type' => 'string',
				'enum' => array( 'public', 'private', 'invite' ),
			),
			'event_status' => array(
				'type' => 'string',
				'enum' => array( 'scheduled', 'cancelled', 'postponed', 'ongoing', 'finished' ),
			),
			'dj_ids'       => array(
				'type'  => 'array',
				'items' => array( 'type' => 'integer' ),
			),
			'dj_slots'     => array( 'type' => 'array' ),
			'categories'   => array( 'type' => 'array' ),
			'types'        => array( 'type' => 'array' ),
			'tags'         => array( 'type' => 'array' ),
			'sounds'       => array( 'type' => 'array' ),
			'seasons'      => array( 'type' => 'array' ),
		);
	}
}
