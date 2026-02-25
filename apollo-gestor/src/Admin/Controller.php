<?php

/**
 * Admin Controller — menu registration, asset enqueueing, AJAX handlers
 *
 * @package Apollo\Gestor
 */

declare(strict_types=1);

namespace Apollo\Gestor\Admin;

use Apollo\Gestor\Model\Task;
use Apollo\Gestor\Model\Team;
use Apollo\Gestor\Model\Payment;
use Apollo\Gestor\Model\Milestone;
use Apollo\Gestor\Model\Activity;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Controller {


	/** Admin page hook suffix */
	private string $hook = '';

	/**
	 * Bootstrap admin hooks
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		// AJAX handlers (logged-in only)
		$ajax_actions = array(
			'apollo_gestor_load_events',
			'apollo_gestor_load_overview',
			'apollo_gestor_load_tasks',
			'apollo_gestor_toggle_task',
			'apollo_gestor_create_task',
			'apollo_gestor_update_task',
			'apollo_gestor_delete_task',
			'apollo_gestor_load_team',
			'apollo_gestor_add_team_member',
			'apollo_gestor_remove_team_member',
			'apollo_gestor_update_team_member',
			'apollo_gestor_load_payments',
			'apollo_gestor_create_payment',
			'apollo_gestor_update_payment',
			'apollo_gestor_delete_payment',
			'apollo_gestor_load_milestones',
			'apollo_gestor_create_milestone',
			'apollo_gestor_toggle_milestone',
			'apollo_gestor_update_event_status',
			'apollo_gestor_save_permissions',
			'apollo_gestor_load_activity',
			'apollo_gestor_load_suppliers',
		);

		foreach ( $ajax_actions as $action ) {
			$method = str_replace( 'apollo_gestor_', '', $action );
			add_action( "wp_ajax_{$action}", array( $this, $method ) );
		}
	}

	/**
	 * Register admin menu page
	 */
	public function register_menu(): void {
		$this->hook = add_menu_page(
			'Gestor Apollo',
			'Gestor',
			'edit_posts',
			'apollo-gestor',
			array( $this, 'render_page' ),
			'dashicons-clipboard',
			27
		);
	}

	/**
	 * Enqueue CSS + JS only on our page
	 */
	public function enqueue_assets( string $hook ): void {
		if ( $hook !== $this->hook ) {
			return;
		}

		// Remove admin bar padding
		remove_action( 'wp_head', '_admin_bar_bump_cb' );

		wp_enqueue_style(
			'apollo-gestor',
			APOLLO_GESTOR_URL . 'assets/css/gestor.css',
			array(),
			APOLLO_GESTOR_VERSION
		);

		wp_enqueue_script(
			'apollo-gestor',
			APOLLO_GESTOR_URL . 'assets/js/gestor.js',
			array( 'jquery' ),
			APOLLO_GESTOR_VERSION,
			true
		);

		wp_localize_script(
			'apollo-gestor',
			'ApolloGestor',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'apollo_gestor_nonce' ),
				'docsNonce'   => wp_create_nonce( 'apollo_docs_nonce' ),
				'restUrl'     => rest_url( 'apollo/v1/' ),
				'cdnUrl'      => defined( 'APOLLO_CDN_URL' ) ? APOLLO_CDN_URL : 'https://cdn.apollo.rio.br/v1.0.0/',
				'assetsUrl'   => 'https://assets.apollo.rio.br/i/',
				'currentUser' => get_current_user_id(),
				'isAdmin'     => current_user_can( 'manage_options' ),
				'i18n'        => array(
					'loading'   => 'Carregando…',
					'error'     => 'Erro ao carregar dados',
					'saved'     => 'Salvo com sucesso',
					'confirm'   => 'Tem certeza?',
					'noEvents'  => 'Nenhum evento encontrado',
					'noTasks'   => 'Sem tarefas pendentes',
					'copied'    => 'Copiado!',
					'planning'  => 'Planejando',
					'preparing' => 'Preparando',
					'live'      => 'Ao Vivo',
					'done'      => 'Concluído',
					'cancelled' => 'Cancelado',
				),
			)
		);
	}

	/**
	 * Render admin page (loads template)
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Acesso negado.', 'apollo-gestor' ) );
		}

		include APOLLO_GESTOR_DIR . 'templates/gestor.php';
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Security + JSON helpers
	 * ═══════════════════════════════════════════════════════════ */

	private function verify_nonce(): bool {
		return check_ajax_referer( 'apollo_gestor_nonce', 'nonce', false ) !== false;
	}

	private function json_success( $data = null ): void {
		wp_send_json_success( $data );
	}

	private function json_error( string $msg = 'Erro', int $code = 400 ): void {
		wp_send_json_error( array( 'message' => $msg ), $code );
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Events
	 * ═══════════════════════════════════════════════════════════ */

	/**
	 * Load events for the selector + kanban
	 */
	public function load_events(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$user_id  = get_current_user_id();
		$is_admin = current_user_can( 'manage_options' );

		$args = array(
			'post_type'      => 'event',
			'posts_per_page' => 50,
			'post_status'    => 'publish',
			'orderby'        => 'meta_value',
			'meta_key'       => '_event_start_date',
			'order'          => 'ASC',
		);

		// If not admin, only show events where user is on the team
		if ( ! $is_admin ) {
			$event_ids = Team::get_user_event_ids( $user_id );
			if ( empty( $event_ids ) ) {
				$this->json_success( array() );
			}
			$args['post__in'] = $event_ids;
		}

		$query  = new \WP_Query( $args );
		$events = array();

		foreach ( $query->posts as $post ) {
			$event_id   = $post->ID;
			$status     = get_post_meta( $event_id, '_event_status', true ) ?: 'planning';
			$start_date = get_post_meta( $event_id, '_event_start_date', true );
			$loc_id     = (int) get_post_meta( $event_id, '_event_loc_id', true );
			$loc_name   = $loc_id ? get_the_title( $loc_id ) : '';
			$budget     = (float) get_post_meta( $event_id, '_event_budget', true );
			$tasks      = Task::get_counts( $event_id );
			$team       = Team::get_by_event( $event_id );
			$progress   = $tasks['total'] > 0 ? round( ( $tasks['done'] / $tasks['total'] ) * 100 ) : 0;

			$team_avatars = array();
			foreach ( array_slice( $team, 0, 3 ) as $member ) {
				$team_avatars[] = $member['avatar_url'] ?? '';
			}

			$events[] = array(
				'id'           => $event_id,
				'title'        => $post->post_title,
				'status'       => $status,
				'start_date'   => $start_date,
				'start_fmt'    => $start_date ? wp_date( 'd M', strtotime( $start_date ) ) : '',
				'loc_name'     => $loc_name,
				'budget'       => $budget,
				'budget_fmt'   => 'R$ ' . number_format( $budget, 0, ',', '.' ),
				'tasks'        => $tasks,
				'progress'     => $progress,
				'team_count'   => count( $team ),
				'team_avatars' => $team_avatars,
				'can_manage'   => $is_admin || Team::can_manage( $user_id, $event_id ),
				'can_finance'  => $is_admin || Team::can_view_finance( $user_id, $event_id ),
			);
		}

		$this->json_success( $events );
	}

	/**
	 * Load overview stats (aggregated)
	 */
	public function load_overview(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$user_id  = get_current_user_id();
		$is_admin = current_user_can( 'manage_options' );

		// Get event IDs accessible to this user
		$args = array(
			'post_type'      => 'event',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		);

		if ( ! $is_admin ) {
			$event_ids = Team::get_user_event_ids( $user_id );
			if ( empty( $event_ids ) ) {
				$this->json_success(
					array(
						'active_events' => 0,
						'tasks_done'    => 0,
						'tasks_overdue' => 0,
						'team_total'    => 0,
						'total_budget'  => 0,
						'budget_fmt'    => 'R$ 0',
					)
				);
			}
			$args['post__in'] = $event_ids;
		}

		$all_ids = get_posts( $args );

		$total_done    = 0;
		$total_overdue = 0;
		$total_budget  = 0.0;
		$team_ids      = array();

		foreach ( $all_ids as $eid ) {
			$counts         = Task::get_counts( (int) $eid );
			$total_done    += $counts['done'];
			$total_overdue += $counts['overdue'];
			$total_budget  += (float) get_post_meta( (int) $eid, '_event_budget', true );

			$members = Team::get_by_event( (int) $eid );
			foreach ( $members as $m ) {
				$team_ids[ $m['user_id'] ] = true;
			}
		}

		$this->json_success(
			array(
				'active_events' => count( $all_ids ),
				'tasks_done'    => $total_done,
				'tasks_overdue' => $total_overdue,
				'team_total'    => count( $team_ids ),
				'total_budget'  => $total_budget,
				'budget_fmt'    => 'R$ ' . number_format( $total_budget / 1000, 0, ',', '.' ) . 'k',
			)
		);
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Tasks
	 * ═══════════════════════════════════════════════════════════ */

	public function load_tasks(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		if ( ! $event_id ) {
			$this->json_error( 'Evento inválido' );
		}

		$status = sanitize_key( $_POST['status'] ?? '' ) ?: null;
		$tasks  = Task::get_by_event( $event_id, $status );

		// Enrich with assignee info
		foreach ( $tasks as &$task ) {
			if ( ! empty( $task['assignee_id'] ) ) {
				$user                    = get_userdata( (int) $task['assignee_id'] );
				$task['assignee_name']   = $user ? $user->display_name : '';
				$task['assignee_avatar'] = $user ? get_avatar_url( $user->ID, array( 'size' => 44 ) ) : '';
			} else {
				$task['assignee_name']   = '';
				$task['assignee_avatar'] = '';
			}
		}

		$this->json_success(
			array(
				'tasks'  => $tasks,
				'counts' => Task::get_counts( $event_id ),
			)
		);
	}

	public function toggle_task(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$task_id = absint( $_POST['task_id'] ?? 0 );
		if ( ! $task_id ) {
			$this->json_error( 'Tarefa inválida' );
		}

		$new_status = Task::toggle( $task_id );
		if ( $new_status === false ) {
			$this->json_error( 'Tarefa não encontrada' );
		}

		$task = Task::get( $task_id );

		Activity::log(
			array(
				'event_id'    => $task ? (int) $task['event_id'] : 0,
				'action'      => $new_status === 'done' ? 'task_completed' : 'task_updated',
				'entity_type' => 'task',
				'entity_id'   => $task_id,
				'meta'        => array(
					'title'  => $task['title'] ?? '',
					'status' => $new_status,
				),
			)
		);

		$this->json_success(
			array(
				'status' => $new_status,
				'counts' => $task ? Task::get_counts( (int) $task['event_id'] ) : null,
			)
		);
	}

	public function create_task(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		if ( ! $event_id ) {
			$this->json_error( 'Evento inválido' );
		}

		$user_id = get_current_user_id();
		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, $event_id ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		$id = Task::create(
			array(
				'event_id'    => $event_id,
				'title'       => sanitize_text_field( $_POST['title'] ?? '' ),
				'description' => sanitize_textarea_field( $_POST['description'] ?? '' ),
				'assignee_id' => absint( $_POST['assignee_id'] ?? 0 ),
				'category'    => sanitize_text_field( $_POST['category'] ?? '' ),
				'priority'    => sanitize_key( $_POST['priority'] ?? 'medium' ),
				'due_date'    => sanitize_text_field( $_POST['due_date'] ?? '' ),
			)
		);

		if ( ! $id ) {
			$this->json_error( 'Falha ao criar tarefa' );
		}

		Activity::log(
			array(
				'event_id'    => $event_id,
				'action'      => 'task_created',
				'entity_type' => 'task',
				'entity_id'   => $id,
				'meta'        => array( 'title' => sanitize_text_field( $_POST['title'] ?? '' ) ),
			)
		);

		$this->json_success(
			array(
				'id'   => $id,
				'task' => Task::get( $id ),
			)
		);
	}

	public function update_task(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$task_id = absint( $_POST['task_id'] ?? 0 );
		$task    = Task::get( $task_id );
		if ( ! $task ) {
			$this->json_error( 'Tarefa não encontrada' );
		}

		$user_id = get_current_user_id();
		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, (int) $task['event_id'] ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		$data   = array();
		$fields = array( 'title', 'description', 'assignee_id', 'category', 'priority', 'status', 'due_date' );
		foreach ( $fields as $f ) {
			if ( isset( $_POST[ $f ] ) ) {
				$data[ $f ] = sanitize_text_field( $_POST[ $f ] );
			}
		}

		Task::update( $task_id, $data );
		$this->json_success( array( 'task' => Task::get( $task_id ) ) );
	}

	public function delete_task(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$task_id = absint( $_POST['task_id'] ?? 0 );
		$task    = Task::get( $task_id );
		if ( ! $task ) {
			$this->json_error( 'Tarefa não encontrada' );
		}

		$user_id = get_current_user_id();
		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, (int) $task['event_id'] ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		Task::delete( $task_id );

		Activity::log(
			array(
				'event_id'    => (int) $task['event_id'],
				'action'      => 'task_deleted',
				'entity_type' => 'task',
				'entity_id'   => $task_id,
				'meta'        => array( 'title' => $task['title'] ?? '' ),
			)
		);

		$this->json_success();
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Team
	 * ═══════════════════════════════════════════════════════════ */

	public function load_team(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		if ( ! $event_id ) {
			$this->json_error( 'Evento inválido' );
		}

		$this->json_success( Team::get_by_event( $event_id ) );
	}

	public function add_team_member(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		$user_id  = get_current_user_id();

		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, $event_id ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		$id = Team::add(
			array(
				'event_id'     => $event_id,
				'user_id'      => absint( $_POST['user_id'] ?? 0 ),
				'role'         => sanitize_key( $_POST['role'] ?? 'team' ),
				'job_function' => sanitize_text_field( $_POST['job_function'] ?? '' ),
				'pix_key'      => sanitize_text_field( $_POST['pix_key'] ?? '' ),
			)
		);

		if ( ! $id ) {
			$this->json_error( 'Falha ao adicionar membro' );
		}

		$member_user = get_userdata( absint( $_POST['user_id'] ?? 0 ) );
		Activity::log(
			array(
				'event_id'    => $event_id,
				'action'      => 'member_added',
				'entity_type' => 'team',
				'entity_id'   => $id,
				'meta'        => array(
					'name' => $member_user ? $member_user->display_name : '',
					'role' => sanitize_key( $_POST['role'] ?? 'team' ),
				),
			)
		);

		$this->json_success( array( 'id' => $id ) );
	}

	public function remove_team_member(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$member_id = absint( $_POST['member_id'] ?? 0 );
		if ( ! $member_id ) {
			$this->json_error( 'Membro inválido' );
		}

		Team::remove( $member_id );

		Activity::log(
			array(
				'event_id'    => absint( $_POST['event_id'] ?? 0 ),
				'action'      => 'member_removed',
				'entity_type' => 'team',
				'entity_id'   => $member_id,
			)
		);

		$this->json_success();
	}

	/**
	 * Update team member role / function / pix
	 */
	public function update_team_member(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$member_id = absint( $_POST['member_id'] ?? 0 );
		if ( ! $member_id ) {
			$this->json_error( 'Membro inválido' );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		$user_id  = get_current_user_id();

		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, $event_id ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		$data = array();
		if ( isset( $_POST['role'] ) ) {
			$data['role'] = sanitize_key( $_POST['role'] );
		}
		if ( isset( $_POST['job_function'] ) ) {
			$data['job_function'] = sanitize_text_field( $_POST['job_function'] );
		}
		if ( isset( $_POST['pix_key'] ) ) {
			$data['pix_key'] = sanitize_text_field( $_POST['pix_key'] );
		}

		Team::update( $member_id, $data );

		Activity::log(
			array(
				'event_id'    => $event_id,
				'action'      => 'member_updated',
				'entity_type' => 'team',
				'entity_id'   => $member_id,
				'meta'        => $data,
			)
		);

		$this->json_success();
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Payments (Finance)
	 * ═══════════════════════════════════════════════════════════ */

	public function load_payments(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		if ( ! $event_id ) {
			$this->json_error( 'Evento inválido' );
		}

		$user_id = get_current_user_id();
		if ( ! current_user_can( 'manage_options' ) && ! Team::can_view_finance( $user_id, $event_id ) ) {
			$this->json_error( 'Sem acesso financeiro', 403 );
		}

		$this->json_success(
			array(
				'payments' => Payment::get_by_event( $event_id ),
				'summary'  => Payment::get_summary( $event_id ),
			)
		);
	}

	public function create_payment(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		$user_id  = get_current_user_id();

		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, $event_id ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		$id = Payment::create(
			array(
				'event_id'    => $event_id,
				'payee_type'  => sanitize_key( $_POST['payee_type'] ?? 'staff' ),
				'payee_id'    => absint( $_POST['payee_id'] ?? 0 ),
				'description' => sanitize_text_field( $_POST['description'] ?? '' ),
				'category'    => sanitize_text_field( $_POST['category'] ?? '' ),
				'amount'      => floatval( $_POST['amount'] ?? 0 ),
				'pix_key'     => sanitize_text_field( $_POST['pix_key'] ?? '' ),
				'status'      => sanitize_key( $_POST['status'] ?? 'pending' ),
				'due_date'    => sanitize_text_field( $_POST['due_date'] ?? '' ),
			)
		);

		if ( ! $id ) {
			$this->json_error( 'Falha ao criar pagamento' );
		}

		Activity::log(
			array(
				'event_id'    => $event_id,
				'action'      => 'payment_created',
				'entity_type' => 'payment',
				'entity_id'   => $id,
				'meta'        => array(
					'amount'      => floatval( $_POST['amount'] ?? 0 ),
					'description' => sanitize_text_field( $_POST['description'] ?? '' ),
				),
			)
		);

		$this->json_success( array( 'id' => $id ) );
	}

	public function update_payment(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$payment_id = absint( $_POST['payment_id'] ?? 0 );
		if ( ! $payment_id ) {
			$this->json_error( 'Pagamento inválido' );
		}

		Payment::update(
			$payment_id,
			array(
				'status'  => sanitize_key( $_POST['status'] ?? '' ),
				'amount'  => floatval( $_POST['amount'] ?? 0 ),
				'paid_at' => sanitize_text_field( $_POST['paid_at'] ?? '' ),
			)
		);

		$new_status = sanitize_key( $_POST['status'] ?? '' );
		Activity::log(
			array(
				'event_id'    => absint( $_POST['event_id'] ?? 0 ),
				'action'      => $new_status === 'paid' ? 'payment_paid' : 'payment_updated',
				'entity_type' => 'payment',
				'entity_id'   => $payment_id,
				'meta'        => array( 'status' => $new_status ),
			)
		);

		$this->json_success();
	}

	/**
	 * Delete a payment
	 */
	public function delete_payment(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$payment_id = absint( $_POST['payment_id'] ?? 0 );
		if ( ! $payment_id ) {
			$this->json_error( 'Pagamento inválido' );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		$user_id  = get_current_user_id();

		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, $event_id ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		Payment::delete( $payment_id );

		Activity::log(
			array(
				'event_id'    => $event_id,
				'action'      => 'payment_deleted',
				'entity_type' => 'payment',
				'entity_id'   => $payment_id,
			)
		);

		$this->json_success();
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Milestones
	 * ═══════════════════════════════════════════════════════════ */

	public function load_milestones(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		if ( ! $event_id ) {
			$this->json_error( 'Evento inválido' );
		}

		$this->json_success(
			array(
				'milestones' => Milestone::get_by_event( $event_id ),
				'progress'   => Milestone::get_progress( $event_id ),
			)
		);
	}

	/**
	 * Create a milestone
	 */
	public function create_milestone(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		if ( ! $event_id ) {
			$this->json_error( 'Evento inválido' );
		}

		$user_id = get_current_user_id();
		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, $event_id ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		$id = Milestone::create(
			array(
				'event_id' => $event_id,
				'title'    => sanitize_text_field( $_POST['title'] ?? '' ),
				'icon'     => sanitize_text_field( $_POST['icon'] ?? 'ri-flag-line' ),
				'due_date' => sanitize_text_field( $_POST['due_date'] ?? '' ),
			)
		);

		if ( ! $id ) {
			$this->json_error( 'Falha ao criar marco' );
		}

		Activity::log(
			array(
				'event_id'    => $event_id,
				'action'      => 'milestone_created',
				'entity_type' => 'milestone',
				'entity_id'   => $id,
				'meta'        => array( 'title' => sanitize_text_field( $_POST['title'] ?? '' ) ),
			)
		);

		$this->json_success(
			array(
				'id'        => $id,
				'milestone' => Milestone::get( $id ),
			)
		);
	}

	public function toggle_milestone(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$milestone_id = absint( $_POST['milestone_id'] ?? 0 );
		if ( ! $milestone_id ) {
			$this->json_error( 'Marco inválido' );
		}

		$new_status = Milestone::toggle( $milestone_id );
		if ( $new_status === false ) {
			$this->json_error( 'Marco não encontrado' );
		}

		$ms = Milestone::get( $milestone_id );
		Activity::log(
			array(
				'event_id'    => $ms ? (int) $ms['event_id'] : 0,
				'action'      => 'milestone_toggled',
				'entity_type' => 'milestone',
				'entity_id'   => $milestone_id,
				'meta'        => array(
					'title'  => $ms['title'] ?? '',
					'status' => $new_status,
				),
			)
		);

		$this->json_success( array( 'status' => $new_status ) );
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Event Status Update (Kanban drag)
	 * ═══════════════════════════════════════════════════════════ */

	public function update_event_status(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		$status   = sanitize_key( $_POST['status'] ?? '' );

		if ( ! $event_id || ! $status ) {
			$this->json_error( 'Dados inválidos' );
		}

		$valid = array( 'planning', 'preparing', 'live', 'done', 'cancelled' );
		if ( ! in_array( $status, $valid, true ) ) {
			$this->json_error( 'Status inválido' );
		}

		$user_id = get_current_user_id();
		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, $event_id ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		update_post_meta( $event_id, '_event_status', $status );

		Activity::log(
			array(
				'event_id'    => $event_id,
				'action'      => 'status_changed',
				'entity_type' => 'event',
				'entity_id'   => $event_id,
				'meta'        => array( 'status' => $status ),
			)
		);

		do_action( 'apollo/gestor/event_status_changed', $event_id, $status, $user_id );

		$this->json_success( array( 'status' => $status ) );
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Permissions
	 * ═══════════════════════════════════════════════════════════ */

	/**
	 * Save team member permissions (batch role updates)
	 */
	public function save_permissions(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		if ( ! $event_id ) {
			$this->json_error( 'Evento inválido' );
		}

		$user_id = get_current_user_id();
		if ( ! current_user_can( 'manage_options' ) && ! Team::can_manage( $user_id, $event_id ) ) {
			$this->json_error( 'Sem permissão', 403 );
		}

		$perms   = isset( $_POST['permissions'] ) ? (array) $_POST['permissions'] : array();
		$updated = 0;

		foreach ( $perms as $perm ) {
			$member_id = absint( $perm['member_id'] ?? 0 );
			$role      = sanitize_key( $perm['role'] ?? '' );
			if ( $member_id && $role && in_array( $role, array( 'adm', 'gestor', 'tgestor', 'team' ), true ) ) {
				Team::update( $member_id, array( 'role' => $role ) );
				++$updated;
			}
		}

		Activity::log(
			array(
				'event_id'    => $event_id,
				'action'      => 'perms_updated',
				'entity_type' => 'team',
				'entity_id'   => 0,
				'meta'        => array( 'count' => $updated ),
			)
		);

		$this->json_success( array( 'updated' => $updated ) );
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Activity Log
	 * ═══════════════════════════════════════════════════════════ */

	/**
	 * Load activity log for an event (or global)
	 */
	public function load_activity(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id = absint( $_POST['event_id'] ?? 0 );
		$limit    = absint( $_POST['limit'] ?? 30 );
		$offset   = absint( $_POST['offset'] ?? 0 );

		if ( $limit > 100 ) {
			$limit = 100;
		}

		$activities = Activity::get_by_event( $event_id, $limit, $offset );

		foreach ( $activities as &$a ) {
			$a['action_label'] = Activity::action_label( $a['action'] );
		}

		$this->json_success( array( 'activities' => $activities ) );
	}

	/*
	═══════════════════════════════════════════════════════════
	 * AJAX: Suppliers
	 * ═══════════════════════════════════════════════════════════ */

	/**
	 * Load suppliers from apollo-suppliers CPT (or payments with payee_type='supplier')
	 */
	public function load_suppliers(): void {
		if ( ! $this->verify_nonce() ) {
			$this->json_error( 'Nonce inválido', 403 );
		}

		$event_id  = absint( $_POST['event_id'] ?? 0 );
		$suppliers = array();

		// 1. Get suppliers linked via payments
		if ( $event_id ) {
			$payment_suppliers = Payment::get_by_event( $event_id );
			foreach ( $payment_suppliers as $p ) {
				if ( $p['payee_type'] === 'supplier' && $p['payee_id'] ) {
					$suppliers[ $p['payee_id'] ] = array(
						'id'      => (int) $p['payee_id'],
						'source'  => 'payment',
						'amount'  => (float) $p['amount'],
						'status'  => $p['status'],
						'pix_key' => $p['pix_key'],
					);
				}
			}
		}

		// 2. If apollo-suppliers CPT exists, load from it
		if ( post_type_exists( 'supplier' ) ) {
			$query = new \WP_Query(
				array(
					'post_type'      => 'supplier',
					'posts_per_page' => 50,
					'post_status'    => 'publish',
					'orderby'        => 'title',
					'order'          => 'ASC',
				)
			);

			foreach ( $query->posts as $post ) {
				$sid = $post->ID;
				if ( isset( $suppliers[ $sid ] ) ) {
					$suppliers[ $sid ]['title']    = $post->post_title;
					$suppliers[ $sid ]['category'] = get_post_meta( $sid, '_supplier_category', true ) ?: '';
					$suppliers[ $sid ]['phone']    = get_post_meta( $sid, '_supplier_phone', true ) ?: '';
					$suppliers[ $sid ]['email']    = get_post_meta( $sid, '_supplier_email', true ) ?: '';
				} else {
					$suppliers[ $sid ] = array(
						'id'       => $sid,
						'title'    => $post->post_title,
						'source'   => 'cpt',
						'category' => get_post_meta( $sid, '_supplier_category', true ) ?: '',
						'phone'    => get_post_meta( $sid, '_supplier_phone', true ) ?: '',
						'email'    => get_post_meta( $sid, '_supplier_email', true ) ?: '',
						'amount'   => 0,
						'status'   => '',
					);
				}
			}
		}

		$this->json_success( array_values( $suppliers ) );
	}
}
