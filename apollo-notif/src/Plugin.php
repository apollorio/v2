<?php

/**
 * Apollo Notif — Main Plugin Class
 *
 * Notifications engine adapted from BNFW patterns: per-event triggers,
 * shortcode-based templates, in-app + email channels.
 *
 * Registry compliance:
 *   REST: /notifications, /notifications/{id}/read, /notifications/read-all, /notifications/unread-count, /notifications/preferences
 *   Pages: /notificacoes
 *   Shortcodes: [apollo_notif], [apollo_notif_badge]
 *   Tables: apollo_notifications (core), apollo_notif_prefs (plugin)
 *   Meta: _apollo_notif_prefs, _apollo_notif_unread
 *
 * @package Apollo\Notif
 */

declare(strict_types=1);

namespace Apollo\Notif;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {


	private static ?Plugin $instance = null;

	public static function instance(): Plugin {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_rewrite_rules' ), 1 );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_filter( 'cron_schedules', array( $this, 'register_cron_schedules' ) );
		add_action( 'template_redirect', array( $this, 'handle_virtual_pages' ), 5 );

		// BNFW-style event triggers: hook into WordPress & Apollo actions
		add_action( 'apollo/chat/message_sent', array( $this, 'on_message_sent' ), 10, 3 );
		add_action( 'apollo/social/activity_created', array( $this, 'on_activity_created' ), 10, 2 );
		add_action( 'apollo/social/reply_created', array( $this, 'on_reply_created' ), 10, 3 );
		add_action( 'apollo/groups/user_joined', array( $this, 'on_group_joined' ), 10, 2 );
		add_action( 'apollo/wow/added', array( $this, 'on_wow_added' ), 10, 4 );
		add_action( 'apollo/mod/reported', array( $this, 'on_content_reported' ), 10, 3 );
		add_action( 'comment_post', array( $this, 'on_depoimento' ), 10, 3 );

		// Extended triggers — WP native
		add_action( 'user_register', array( $this, 'on_user_register' ), 10, 1 );
		add_action( 'wp_login', array( $this, 'on_user_login' ), 10, 2 );

		// Extended triggers — Apollo ecosystem
		add_action( 'apollo/social/follow', array( $this, 'on_follow' ), 10, 2 );
		add_action( 'apollo/notif/mention', array( $this, 'on_mention' ), 10, 3 );
		add_action( 'apollo/event/published', array( $this, 'on_event_published' ), 10, 2 );
		add_action( 'apollo/fav/saved', array( $this, 'on_fav_saved' ), 10, 3 );
		add_action( 'apollo/coauthor/invited', array( $this, 'on_coauthor_invited' ), 10, 3 );
		add_action( 'apollo/membership/upgraded', array( $this, 'on_membership_upgraded' ), 10, 2 );
		add_action( 'apollo/users/profile_visited', array( $this, 'on_profile_visited' ), 10, 2 );

		// Admin page
		add_action( 'admin_menu', array( $this, 'register_admin_page' ) );

		// Navbar integration: provide notifications data
		add_filter( 'apollo_navbar_notifications', array( $this, 'get_navbar_notifications' ) );

		// Cron cleanup
		if ( ! wp_next_scheduled( 'apollo_notif_cleanup' ) ) {
			wp_schedule_event( time(), 'daily', 'apollo_notif_cleanup' );
		}
		add_action( 'apollo_notif_cleanup', array( $this, 'cleanup_old_notifications' ) );

		// Weekly digest dispatcher (legacy + segmented)
		if ( ! wp_next_scheduled( 'apollo_notif_digest_dispatch' ) ) {
			wp_schedule_event( time(), 'weekly', 'apollo_notif_digest_dispatch' );
		}
		add_action( 'apollo_notif_digest_dispatch', array( $this, 'dispatch_scheduled_digests' ) );

		// NOTE: digest is weekly-only (batch). Per-notification emails are
		// handled by dedicated handlers in apollo-email (onChatMessage, etc.).
	}

	/**
	 * Register custom cron schedules if needed.
	 *
	 * @param array $schedules Existing schedules.
	 * @return array
	 */
	public function register_cron_schedules( array $schedules ): array {
		if ( ! isset( $schedules['weekly'] ) ) {
			$schedules['weekly'] = array(
				'interval' => 7 * DAY_IN_SECONDS,
				'display'  => __( 'Once Weekly', 'apollo-notif' ),
			);
		}

		return $schedules;
	}

	// ─── Rewrite Rules ──────────────────────────────────────────────
	public function register_rewrite_rules(): void {
		add_rewrite_rule( '^notificacoes/?$', 'index.php?apollo_notif_page=notifications', 'top' );
	}

	public function register_query_vars( array $vars ): array {
		$vars[] = 'apollo_notif_page';
		return $vars;
	}

	public function handle_virtual_pages(): void {
		$page = get_query_var( 'apollo_notif_page' );
		if ( ! $page ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_redirect( home_url( '/acesso' ) );
			exit;
		}

		$template = APOLLO_NOTIF_PATH . 'templates/notifications.php';
		if ( file_exists( $template ) ) {
			include $template;
			exit;
		}
	}

	// ─── REST API ──────────────────────────────────────────────────
	public function register_rest_routes(): void {
		$ns = 'apollo/v1';

		register_rest_route(
			$ns,
			'/notifications',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_notifications' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
				'args'                => array(
					'per_page'    => array(
						'default'           => 20,
						'sanitize_callback' => 'absint',
					),
					'page'        => array(
						'default'           => 1,
						'sanitize_callback' => 'absint',
					),
					'unread_only' => array(
						'default'           => false,
						'sanitize_callback' => 'rest_sanitize_boolean',
					),
					'since'       => array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'type'        => array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_key',
					),
					'channel'     => array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'severity'    => array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);

		register_rest_route(
			$ns,
			'/notifications/(?P<id>\d+)/read',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_mark_read' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			)
		);

		register_rest_route(
			$ns,
			'/notifications/read-all',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_mark_all_read' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			)
		);

		register_rest_route(
			$ns,
			'/notifications/unread-count',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_unread_count' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			)
		);

		register_rest_route(
			$ns,
			'/notifications/preferences',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'rest_get_prefs' ),
					'permission_callback' => function () {
						return is_user_logged_in();
					},
				),
				array(
					'methods'             => 'PUT',
					'callback'            => array( $this, 'rest_update_prefs' ),
					'permission_callback' => function () {
						return is_user_logged_in();
					},
				),
			)
		);

		// ── New: delete single notification
		register_rest_route(
			$ns,
			'/notifications/(?P<id>\d+)',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'rest_delete_notification' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			)
		);

		// ── New: delete all read notifications
		register_rest_route(
			$ns,
			'/notifications/read',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'rest_delete_read' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			)
		);

		// ── New: mark a single notification as displayed (seen in dropdown)
		register_rest_route(
			$ns,
			'/notifications/(?P<id>\d+)/displayed',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_mark_displayed' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			)
		);

		// ── New: snooze a notification type
		register_rest_route(
			$ns,
			'/notifications/preferences/snooze',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_snooze_type' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
				'args'                => array(
					'type'  => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_key',
					),
					'hours' => array(
						'default'           => 24,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// ── New: unsnooze a type
		register_rest_route(
			$ns,
			'/notifications/preferences/snooze',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'rest_unsnooze_type' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
				'args'                => array(
					'type' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);

		// ── Web Push: subscribe / unsubscribe ────────────────────────
		register_rest_route(
			$ns,
			'/notifications/push/subscribe',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_push_subscribe' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
				'args'                => array(
					'endpoint' => array(
						'required'          => true,
						'sanitize_callback' => 'esc_url_raw',
					),
					'keys'     => array(
						'required'          => true,
						'validate_callback' => function ( $v ) {
							return is_array( $v ) && isset( $v['p256dh'], $v['auth'] );
						},
					),
				),
			)
		);

		register_rest_route(
			$ns,
			'/notifications/push/unsubscribe',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'rest_push_unsubscribe' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
				'args'                => array(
					'endpoint' => array(
						'required'          => true,
						'sanitize_callback' => 'esc_url_raw',
					),
				),
			)
		);

		// ── Web Push: VAPID public key ───────────────────────────────
		register_rest_route(
			$ns,
			'/notifications/push/vapid-public-key',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_push_vapid_key' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function rest_get_notifications( \WP_REST_Request $request ): \WP_REST_Response {
		$user_id  = get_current_user_id();
		$per_page = $request->get_param( 'per_page' );
		$page     = $request->get_param( 'page' );
		$offset   = ( $page - 1 ) * $per_page;
		$unread   = $request->get_param( 'unread_only' ) ? true : null;

		// Extended filters from libraries analysis
		$filters = array_filter(
			array(
				'since'    => $request->get_param( 'since' ),
				'type'     => $request->get_param( 'type' ),
				'channel'  => $request->get_param( 'channel' ),
				'severity' => $request->get_param( 'severity' ),
			)
		);

		$notifs = apollo_get_notifications( $user_id, $per_page, $offset, $unread, $filters );

		// Format for frontend
		foreach ( $notifs as &$n ) {
			$n['time_ago']       = function_exists( 'apollo_time_ago' ) ? apollo_time_ago( $n['created_at'] ) : $n['created_at'];
			$n['data']           = $n['data'] ? json_decode( $n['data'], true ) : null;
			$n['is_read']        = (bool) $n['is_read'];
			$n['is_dismissible'] = isset( $n['is_dismissible'] ) ? (bool) $n['is_dismissible'] : true;
			$n['severity']       = $n['severity'] ?? 'info';
		}

		return new \WP_REST_Response( $notifs, 200 );
	}

	public function rest_mark_read( \WP_REST_Request $request ): \WP_REST_Response {
		$notif_id = (int) $request->get_param( 'id' );
		$success  = apollo_mark_notif_read( $notif_id, get_current_user_id() );
		return new \WP_REST_Response( array( 'success' => $success ), $success ? 200 : 404 );
	}

	public function rest_mark_all_read(): \WP_REST_Response {
		$count = apollo_mark_all_notifs_read( get_current_user_id() );
		return new \WP_REST_Response( array( 'marked' => $count ), 200 );
	}

	public function rest_unread_count(): \WP_REST_Response {
		$count = apollo_get_unread_notif_count( get_current_user_id() );
		return new \WP_REST_Response( array( 'count' => $count ), 200 );
	}

	public function rest_get_prefs(): \WP_REST_Response {
		$prefs = get_user_meta( get_current_user_id(), '_apollo_notif_prefs', true );
		return new \WP_REST_Response( $prefs ?: array(), 200 );
	}

	public function rest_update_prefs( \WP_REST_Request $request ): \WP_REST_Response {
		$prefs = $request->get_json_params();
		update_user_meta( get_current_user_id(), '_apollo_notif_prefs', $prefs );
		return new \WP_REST_Response( array( 'success' => true ), 200 );
	}

	// ── New REST callbacks ──────────────────────────────────────────

	public function rest_delete_notification( \WP_REST_Request $request ): \WP_REST_Response {
		$notif_id = (int) $request->get_param( 'id' );
		$success  = apollo_delete_notification( $notif_id, get_current_user_id() );
		return new \WP_REST_Response( array( 'success' => $success ), $success ? 200 : 404 );
	}

	public function rest_delete_read(): \WP_REST_Response {
		$deleted = apollo_delete_all_read_notifications( get_current_user_id() );
		return new \WP_REST_Response( array( 'deleted' => $deleted ), 200 );
	}

	public function rest_mark_displayed( \WP_REST_Request $request ): \WP_REST_Response {
		$notif_id = (int) $request->get_param( 'id' );
		$success  = apollo_mark_notif_displayed( $notif_id, get_current_user_id() );
		return new \WP_REST_Response( array( 'success' => $success ), 200 );
	}

	public function rest_snooze_type( \WP_REST_Request $request ): \WP_REST_Response {
		$type  = (string) $request->get_param( 'type' );
		$hours = (int) $request->get_param( 'hours' );
		$ok    = apollo_snooze_type( get_current_user_id(), $type, $hours );
		return new \WP_REST_Response(
			array(
				'success' => $ok,
				'type'    => $type,
				'hours'   => $hours,
			),
			200
		);
	}

	public function rest_unsnooze_type( \WP_REST_Request $request ): \WP_REST_Response {
		$type = (string) $request->get_param( 'type' );
		$ok   = apollo_unsnooze_type( get_current_user_id(), $type );
		return new \WP_REST_Response( array( 'success' => $ok ), 200 );
	}

	// ─── Shortcodes ─────────────────────────────────────────────────
	public function register_shortcodes(): void {
		add_shortcode( 'apollo_notif', array( $this, 'shortcode_notif' ) );
		add_shortcode( 'apollo_notif_badge', array( $this, 'shortcode_badge' ) );
	}

	public function shortcode_notif( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}
		$notifs = apollo_get_notifications( get_current_user_id(), 10 );
		ob_start();
		echo '<div class="apollo-notif-list">';
		if ( empty( $notifs ) ) {
			echo '<p style="text-align:center;color:var(--ap-text-muted);">Sem notificações</p>';
		}
		foreach ( $notifs as $n ) {
			$class = $n['is_read'] ? 'read' : 'unread';
			$time  = function_exists( 'apollo_time_ago' ) ? apollo_time_ago( $n['created_at'] ) : $n['created_at'];
			echo '<div class="notif-item ' . esc_attr( $class ) . '" data-id="' . esc_attr( $n['id'] ) . '">';
			echo '<div class="notif-content"><div class="notif-title">' . esc_html( $n['title'] ) . '</div>';
			if ( $n['message'] ) {
				echo '<div class="notif-desc">' . esc_html( $n['message'] ) . '</div>';
			}
			echo '<div class="notif-time">' . esc_html( $time ) . '</div></div></div>';
		}
		echo '</div>';
		return ob_get_clean();
	}

	public function shortcode_badge( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}
		$count = apollo_get_unread_notif_count( get_current_user_id() );
		if ( $count <= 0 ) {
			return '';
		}
		return '<span class="apollo-notif-badge-count">' . esc_html( (string) $count ) . '</span>';
	}

	// ─── Event Triggers (BNFW-style) ─────────────────────────────────
	public function on_message_sent( int $msg_id, int $thread_id, int $sender_id ): void {
		// Handled directly in apollo_send_message() function
	}

	public function on_activity_created( int $activity_id, array $data ): void {
		// Mentions are handled in apollo_process_mentions()
	}

	public function on_reply_created( int $reply_id, int $parent_id, int $sender_id ): void {
		global $wpdb;
		$table  = $wpdb->prefix . 'apollo_activity';
		$parent = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT user_id, content FROM {$table} WHERE id = %d",
				$parent_id
			)
		);
		if ( ! $parent ) {
			return;
		}
		$recipient_id = (int) $parent->user_id;
		// Don't notify if replying to own post.
		if ( $recipient_id === $sender_id ) {
			return;
		}
		$sender = get_userdata( $sender_id );
		if ( ! $sender ) {
			return;
		}
		$excerpt = wp_trim_words( $parent->content, 8, '...' );
		apollo_create_notification(
			$recipient_id,
			'reply',
			sprintf( '%s respondeu sua publicação: "%s"', $sender->display_name, $excerpt ),
			get_avatar_url( $sender_id ),
			home_url( '/id/' . $sender->user_login ),
			array(
				'reply_id'  => $reply_id,
				'parent_id' => $parent_id,
				'sender_id' => $sender_id,
			),
			array(
				'severity' => 'info',
				'icon'     => 'ri-reply-line',
				'channel'  => 'apollo/social',
			)
		);
	}

	// ── WordPress native triggers ────────────────────────────────────

	/**
	 * Notify site admin(s) when a new user registers.
	 */
	public function on_user_register( int $user_id ): void {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return;
		}
		$admins = get_users(
			array(
				'role'   => 'administrator',
				'fields' => 'ids',
			)
		);
		foreach ( $admins as $admin_id ) {
			apollo_create_notification(
				(int) $admin_id,
				'new_user',
				sprintf( '%s se cadastrou', $user->display_name ),
				$user->user_email,
				admin_url( 'user-edit.php?user_id=' . $user_id ),
				array( 'user_id' => $user_id ),
				array(
					'severity' => 'info',
					'icon'     => 'ri-user-add-line',
					'channel'  => 'apollo/admin',
				)
			);
		}
	}

	/**
	 * Notify admin on user login (optional — skipped if prefs disabled).
	 * Toggle via filter apollo/notif/admin_login_alert.
	 */
	public function on_user_login( string $user_login, \WP_User $user ): void {
		if ( ! apply_filters( 'apollo/notif/admin_login_alert', false, $user ) ) {
			return;
		}
		$admins = get_users(
			array(
				'role'   => 'administrator',
				'fields' => 'ids',
			)
		);
		foreach ( $admins as $admin_id ) {
			if ( (int) $admin_id === $user->ID ) {
				continue;
			}
			apollo_create_notification(
				(int) $admin_id,
				'user_login',
				sprintf( '%s fez login', $user->display_name ),
				'',
				admin_url( 'user-edit.php?user_id=' . $user->ID ),
				array( 'user_id' => $user->ID ),
				array(
					'severity' => 'info',
					'icon'     => 'ri-login-circle-line',
					'channel'  => 'apollo/admin',
				)
			);
		}
	}

	// ── Apollo ecosystem triggers ─────────────────────────────────────

	/**
	 * Fired when User A follows User B.
	 * Hook: apollo/social/follow (int $follower_id, int $followed_id)
	 */
	public function on_follow( int $follower_id, int $followed_id ): void {
		if ( $follower_id === $followed_id ) {
			return;
		}
		$follower = get_userdata( $follower_id );
		if ( ! $follower ) {
			return;
		}
		apollo_create_notification(
			$followed_id,
			'follow',
			sprintf( '%s começou a te seguir', $follower->display_name ),
			'',
			home_url( '/id/' . $follower->user_login ),
			array( 'follower_id' => $follower_id ),
			array(
				'severity' => 'success',
				'icon'     => 'ri-user-follow-line',
				'channel'  => 'apollo/social',
			)
		);
	}

	/**
	 * Fired when someone @mentions a user in content.
	 * Hook: apollo/notif/mention (int $mention_user_id, int $author_id, string $content_url)
	 */
	public function on_mention( int $mention_user_id, int $author_id, string $content_url ): void {
		if ( $mention_user_id === $author_id ) {
			return;
		}
		$author = get_userdata( $author_id );
		if ( ! $author ) {
			return;
		}
		apollo_create_notification(
			$mention_user_id,
			'mention',
			sprintf( '%s te mencionou', $author->display_name ),
			'',
			$content_url,
			array( 'author_id' => $author_id ),
			array(
				'severity' => 'info',
				'icon'     => 'ri-at-line',
				'channel'  => 'apollo/social',
			)
		);
	}

	/**
	 * Fired when an event is published.
	 * Hook: apollo/event/published (int $post_id, array $event_data)
	 * Notifies users who favorited the organizer.
	 */
	public function on_event_published( int $post_id, array $event_data ): void {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return;
		}
		$author_id = (int) $post->post_author;
		// Notify followers of the author — hook into fav/social layer
		$followers = apply_filters( 'apollo/social/get_followers', array(), $author_id );
		foreach ( $followers as $follower_id ) {
			apollo_create_notification(
				(int) $follower_id,
				'new_event',
				sprintf( 'Novo evento: %s', get_the_title( $post_id ) ),
				'',
				get_permalink( $post_id ),
				array(
					'post_id'   => $post_id,
					'author_id' => $author_id,
				),
				array(
					'severity'       => 'info',
					'icon'           => 'ri-calendar-event-line',
					'channel'        => 'apollo/event',
					'is_dismissible' => true,
					'action_label'   => 'Ver evento',
					'action_link'    => get_permalink( $post_id ),
				)
			);
		}
	}

	/**
	 * Fired when a user saves (favorites) content.
	 * Hook: apollo/fav/saved (int $user_id, string $object_type, int $object_id)
	 * Notifies the content author.
	 */
	public function on_fav_saved( int $user_id, string $object_type, int $object_id ): void {
		$post = get_post( $object_id );
		if ( ! $post || (int) $post->post_author === $user_id ) {
			return;
		}
		$saver = get_userdata( $user_id );
		if ( ! $saver ) {
			return;
		}
		apollo_create_notification(
			(int) $post->post_author,
			'fav_saved',
			sprintf( '%s salvou seu conteúdo', $saver->display_name ),
			get_the_title( $object_id ),
			get_permalink( $post ),
			array(
				'user_id' => $user_id,
				'post_id' => $object_id,
				'type'    => $object_type,
			),
			array(
				'severity' => 'success',
				'icon'     => 'ri-heart-line',
				'channel'  => 'apollo/fav',
			)
		);
	}

	/**
	 * Fired when a coauthor invitation is sent.
	 * Hook: apollo/coauthor/invited (int $post_id, int $inviter_id, int $invitee_id)
	 */
	public function on_coauthor_invited( int $post_id, int $inviter_id, int $invitee_id ): void {
		$inviter = get_userdata( $inviter_id );
		if ( ! $inviter ) {
			return;
		}
		apollo_create_notification(
			$invitee_id,
			'coauthor_invite',
			sprintf( '%s te convidou para coautoria', $inviter->display_name ),
			get_the_title( $post_id ),
			get_permalink( $post_id ),
			array(
				'post_id'    => $post_id,
				'inviter_id' => $inviter_id,
			),
			array(
				'severity'       => 'warning',
				'icon'           => 'ri-user-shared-line',
				'channel'        => 'apollo/coauthor',
				'is_dismissible' => true,
				'action_label'   => 'Ver convite',
				'action_link'    => get_permalink( $post_id ),
			)
		);
	}

	/**
	 * Fired when user membership changes.
	 * Hook: apollo/membership/upgraded (int $user_id, array $membership_data)
	 */
	public function on_membership_upgraded( int $user_id, array $membership_data ): void {
		$plan = sanitize_text_field( $membership_data['plan_name'] ?? 'Plano atualizado' );
		apollo_create_notification(
			$user_id,
			'membership_upgrade',
			sprintf( 'Seu plano foi atualizado: %s', $plan ),
			'',
			home_url( '/dashboard' ),
			$membership_data,
			array(
				'severity'       => 'success',
				'icon'           => 'ri-vip-crown-line',
				'channel'        => 'apollo/membership',
				'is_dismissible' => false,
				'action_label'   => 'Ver minha conta',
				'action_link'    => home_url( '/dashboard' ),
			)
		);
	}

	/**
	 * Fired when someone visits a user profile.
	 * Hook: apollo/users/profile_visited (int $visited_user_id, int $visitor_id)
	 * Throttled to once per 24h per visitor per visited.
	 */
	public function on_profile_visited( int $visited_user_id, int $visitor_id ): void {
		if ( $visited_user_id === $visitor_id || ! $visitor_id ) {
			return;
		}
		// Throttle: only notify once per 24h per pair
		$throttle_key = 'apollo_pv_' . $visited_user_id . '_' . $visitor_id;
		if ( get_transient( $throttle_key ) ) {
			return;
		}
		set_transient( $throttle_key, 1, DAY_IN_SECONDS );

		$visitor = get_userdata( $visitor_id );
		if ( ! $visitor ) {
			return;
		}
		apollo_create_notification(
			$visited_user_id,
			'profile_visit',
			sprintf( '%s visitou seu perfil', $visitor->display_name ),
			'',
			home_url( '/id/' . $visitor->user_login ),
			array( 'visitor_id' => $visitor_id ),
			array(
				'severity' => 'info',
				'icon'     => 'ri-eye-line',
				'channel'  => 'apollo/social',
			)
		);
	}

	public function on_group_joined( int $group_id, int $user_id ): void {
		$group = function_exists( 'apollo_get_group' ) ? apollo_get_group( $group_id ) : null;
		$user  = get_userdata( $user_id );
		if ( ! $group || ! $user ) {
			return;
		}

		// Notify group admin
		apollo_create_notification(
			(int) $group['creator_id'],
			'group_join',
			sprintf( '%s entrou na comuna "%s"', $user->display_name, $group['name'] ),
			'',
			home_url( '/grupo/' . $group['slug'] ),
			array(
				'group_id' => $group_id,
				'user_id'  => $user_id,
			)
		);
	}

	public function on_wow_added( int $user_id, string $object_type, int $object_id, string $reaction_type ): void {
		// Notify the author of the reacted content
		$post = get_post( $object_id );
		if ( ! $post || (int) $post->post_author === $user_id ) {
			return;
		}

		$reactor = get_userdata( $user_id );
		$types   = function_exists( 'apollo_get_wow_types' ) ? apollo_get_wow_types() : array();
		$emoji   = $types[ $reaction_type ]['emoji'] ?? '🤩';

		apollo_create_notification(
			(int) $post->post_author,
			'wow',
			sprintf( '%s reagiu %s ao seu conteúdo', $reactor->display_name, $emoji ),
			'',
			get_permalink( $post ),
			array(
				'reaction_type' => $reaction_type,
				'post_id'       => $object_id,
			)
		);
	}

	public function on_content_reported( int $report_id, string $object_type, int $object_id ): void {
		// Handled directly in apollo_report_content() function
	}

	public function on_depoimento( int $comment_id, $approved, array $data ): void {
		$comment = get_comment( $comment_id );
		if ( ! $comment || $comment->comment_type !== 'apollo_depoimento' ) {
			return;
		}

		$target_id = (int) $comment->comment_parent;
		if ( $target_id <= 0 || $target_id === (int) $comment->user_id ) {
			return;
		}

		$author = get_userdata( (int) $comment->user_id );
		if ( ! $author ) {
			return;
		}

		apollo_create_notification(
			$target_id,
			'depoimento',
			sprintf( '%s deixou um depoimento no seu perfil', $author->display_name ),
			wp_trim_words( $comment->comment_content, 15 ),
			home_url( '/id/' . get_userdata( $target_id )->user_login ),
			array( 'comment_id' => $comment_id )
		);
	}

	// ─── Navbar Integration ──────────────────────────────────────────
	public function get_navbar_notifications( array $notifications ): array {
		if ( ! is_user_logged_in() ) {
			return $notifications;
		}

		$notifs = apollo_get_notifications( get_current_user_id(), 5, 0, true );
		foreach ( $notifs as $n ) {
			$notifications[] = array(
				'id'      => $n['id'],
				'title'   => $n['title'],
				'message' => $n['message'],
				'link'    => $n['link'],
				'time'    => function_exists( 'apollo_time_ago' ) ? apollo_time_ago( $n['created_at'] ) : $n['created_at'],
				'type'    => $n['type'],
				'read'    => (bool) $n['is_read'],
			);
		}

		return $notifications;
	}

	// ─── Admin Page ──────────────────────────────────────────────────
	public function register_admin_page(): void {
		require_once APOLLO_NOTIF_PATH . 'src/Admin.php';
		\Apollo\Notif\Admin::register_menu();
	}

	// ─── Cleanup ─────────────────────────────────────────────────────
	public function cleanup_old_notifications(): void {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_notifications';
		// Delete old read notifications (90 days)
        $wpdb->query("DELETE FROM {$table} WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)"); // phpcs:ignore
		// Delete expired notifications
		if ( function_exists( 'apollo_cleanup_expired_notifications' ) ) {
			apollo_cleanup_expired_notifications();
		}
	}

	/**
	 * Scheduled weekly digest dispatcher.
	 *
	 * Keeps legacy hook compatibility and dispatches segmented digest hooks.
	 * Processes users in batches to avoid memory issues on large sites.
	 */
	public function dispatch_scheduled_digests(): void {
		$batch_size = (int) apply_filters( 'apollo/notif/digest/batch_size', 100 );
		$offset     = 0;

		do {
			$user_ids = get_users(
				array(
					'fields' => 'ids',
					'number' => $batch_size,
					'offset' => $offset,
				)
			);

			if ( empty( $user_ids ) ) {
				break;
			}

			foreach ( $user_ids as $user_id ) {
				$user_id = (int) $user_id;
				if ( $user_id <= 0 ) {
					continue;
				}

				if ( ! $this->user_wants_email_digest( $user_id ) ) {
					continue;
				}

				$segments = $this->build_digest_segments_for_user( $user_id, 7 );

				// Legacy hook: all notifications (backwards-compat)
				do_action( 'apollo/notif/digest', $user_id, $segments['notifications'] );

				// Segmented hooks — each segment dispatched independently.
				// NOTE: 'notifications' segment NOT fired here because the
				// legacy hook above already queues the catch-all email.
				do_action( 'apollo/email/digest/fav_events', $user_id, $segments['fav_events'] );
				do_action( 'apollo/email/digest/event_match', $user_id, $segments['event_match'] );
				do_action( 'apollo/email/digest/chat', $user_id, $segments['chat'] );
				do_action( 'apollo/email/digest/comuna', $user_id, $segments['comuna'] );
				do_action( 'apollo/email/digest/news', $user_id, $segments['news'] );
				do_action( 'apollo/email/digest/social', $user_id, $segments['social'] );
			}

			$offset += $batch_size;
		} while ( count( $user_ids ) >= $batch_size );
	}

	/**
	 * Build digest payloads by segment for one user.
	 *
	 * @param int $user_id Recipient.
	 * @param int $days    Lookback window in days.
	 * @return array<string, array>
	 */
	private function build_digest_segments_for_user( int $user_id, int $days = 7 ): array {
		$segments = array(
			'notifications' => array(),
			'fav_events'    => array(),
			'event_match'   => array(),
			'chat'          => array(),
			'comuna'        => array(),
			'news'          => array(),
			'social'        => array(),
		);

		$notifications = $this->get_recent_notifications_for_user( $user_id, $days );
		if ( empty( $notifications ) ) {
			return $segments;
		}

		foreach ( $notifications as $notification ) {
			$item = $this->notification_to_digest_item( $notification );

			$bucket_names = $this->segment_single_notification( $notification );
			if ( empty( $bucket_names ) ) {
				// Unclassified → goes to generic notifications only
				$segments['notifications'][] = $item;
			} else {
				foreach ( $bucket_names as $bucket_name ) {
					if ( isset( $segments[ $bucket_name ] ) ) {
						$segments[ $bucket_name ][] = $item;
					}
				}
			}
		}

		$segments['notifications'] = apply_filters( 'apollo/notif/digest/notifications_items', $segments['notifications'], $user_id );
		$segments['fav_events']    = apply_filters( 'apollo/notif/digest/fav_events_items', $segments['fav_events'], $user_id );
		$segments['event_match']   = apply_filters( 'apollo/notif/digest/event_match_items', $segments['event_match'], $user_id );
		$segments['chat']          = apply_filters( 'apollo/notif/digest/chat_items', $segments['chat'], $user_id );
		$segments['comuna']        = apply_filters( 'apollo/notif/digest/comuna_items', $segments['comuna'], $user_id );
		$segments['news']          = apply_filters( 'apollo/notif/digest/news_items', $segments['news'], $user_id );
		$segments['social']        = apply_filters( 'apollo/notif/digest/social_items', $segments['social'], $user_id );

		return $segments;
	}

	/**
	 * Query notifications from the last N days.
	 *
	 * @param int $user_id User ID.
	 * @param int $days    Lookback days.
	 * @return array<int, array>
	 */
	private function get_recent_notifications_for_user( int $user_id, int $days = 7 ): array {
		global $wpdb;

		$table = $wpdb->prefix . 'apollo_notifications';
		$days  = max( 1, $days );

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, type, title, message, link, created_at
				 FROM {$table}
				 WHERE user_id = %d
				 AND created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
				 ORDER BY created_at DESC
				 LIMIT 100",
				$user_id,
				$days
			),
			ARRAY_A
		);

		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * Normalize notification into digest item payload.
	 *
	 * @param array<string, mixed> $notification Notification row.
	 * @return array<string, string>
	 */
	private function notification_to_digest_item( array $notification ): array {
		$title   = (string) ( $notification['title'] ?? '' );
		$message = (string) ( $notification['message'] ?? '' );
		$type    = (string) ( $notification['type'] ?? '' );
		$url     = (string) ( $notification['link'] ?? '' );

		return array(
			'title'   => $title,
			'message' => $message !== '' ? $message : $title,
			'time'    => (string) ( $notification['created_at'] ?? '' ),
			'url'     => $url,
			'type'    => $type,
		);
	}

	/**
	 * Classify a notification into one or more digest segments.
	 *
	 * @param array<string, mixed> $notification Notification row.
	 * @return string[]
	 */
	private function segment_single_notification( array $notification ): array {
		$type    = strtolower( (string) ( $notification['type'] ?? '' ) );
		$title   = strtolower( (string) ( $notification['title'] ?? '' ) );
		$message = strtolower( (string) ( $notification['message'] ?? '' ) );

		$segments = array();

		if ( str_contains( $type, 'chat' ) || preg_match( '/\bmensagem\b/i', $message ) ) {
			$segments[] = 'chat';
		}

		if ( str_contains( $type, 'group' ) || str_contains( $type, 'comuna' ) || preg_match( '/\bcomuna\b/i', $title . ' ' . $message ) ) {
			$segments[] = 'comuna';
		}

		if ( str_contains( $type, 'wow' ) || str_contains( $type, 'depoimento' ) || str_contains( $type, 'follow' ) || str_contains( $type, 'social' ) || preg_match( '/\bvisitou\b/i', $title ) || preg_match( '/\breagiu\b/i', $message ) ) {
			$segments[] = 'social';
		}

		if ( str_contains( $type, 'event' ) || preg_match( '/\bevento\b/i', $title . ' ' . $message ) ) {
			$segments[] = 'fav_events';
		}

		if ( str_contains( $type, 'match' ) || preg_match( '/\bmatch\b/i', $title . ' ' . $message ) || preg_match( '/\bsom\b/i', $message ) ) {
			$segments[] = 'event_match';
		}

		if ( str_contains( $type, 'news' ) || preg_match( '/\bnews\b/i', $title . ' ' . $message ) || preg_match( '/\bnovidade\b/i', $message ) ) {
			$segments[] = 'news';
		}

		$segments = array_values( array_unique( $segments ) );

		return apply_filters( 'apollo/notif/digest/classify_segments', $segments, $notification );
	}

	/**
	 * Respect user email digest preference.
	 *
	 * @param int $user_id User ID.
	 * @return bool
	 */
	private function user_wants_email_digest( int $user_id ): bool {
		$prefs = get_user_meta( $user_id, '_apollo_email_prefs', true );
		if ( ! is_array( $prefs ) ) {
			return true;
		}

		return ! empty( $prefs['digest'] );
	}

	// ─── Web Push ─────────────────────────────────────────────────────

	/**
	 * Subscribe user to Web Push notifications.
	 * Stores PushSubscription JSON in user meta  _apollo_push_subscriptions (array of subscriptions).
	 */
	public function rest_push_subscribe( \WP_REST_Request $request ): \WP_REST_Response {
		$user_id  = get_current_user_id();
		$endpoint = esc_url_raw( $request->get_param( 'endpoint' ) );
		$keys     = $request->get_param( 'keys' );

		if ( empty( $endpoint ) || empty( $keys['p256dh'] ) || empty( $keys['auth'] ) ) {
			return new \WP_REST_Response( array( 'error' => 'Invalid subscription' ), 400 );
		}

		$subscription = array(
			'endpoint' => $endpoint,
			'keys'     => array(
				'p256dh' => sanitize_text_field( $keys['p256dh'] ),
				'auth'   => sanitize_text_field( $keys['auth'] ),
			),
		);

		$existing = get_user_meta( $user_id, '_apollo_push_subscriptions', true );
		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		// Remove duplicate endpoint, add new.
		$existing   = array_filter( $existing, fn( $s ) => $s['endpoint'] !== $endpoint );
		$existing[] = $subscription;

		update_user_meta( $user_id, '_apollo_push_subscriptions', array_values( $existing ) );

		do_action( 'apollo/notif/push_subscribed', $user_id, $subscription );

		return new \WP_REST_Response( array( 'subscribed' => true ), 200 );
	}

	/**
	 * Remove a Web Push subscription endpoint for the current user.
	 */
	public function rest_push_unsubscribe( \WP_REST_Request $request ): \WP_REST_Response {
		$user_id  = get_current_user_id();
		$endpoint = esc_url_raw( $request->get_param( 'endpoint' ) );

		$existing = get_user_meta( $user_id, '_apollo_push_subscriptions', true );
		if ( ! is_array( $existing ) ) {
			return new \WP_REST_Response( array( 'unsubscribed' => true ), 200 );
		}

		$filtered = array_filter( $existing, fn( $s ) => $s['endpoint'] !== $endpoint );
		update_user_meta( $user_id, '_apollo_push_subscriptions', array_values( $filtered ) );

		do_action( 'apollo/notif/push_unsubscribed', $user_id, $endpoint );

		return new \WP_REST_Response( array( 'unsubscribed' => true ), 200 );
	}

	/**
	 * Return the VAPID public key so the browser can build a subscription.
	 * Store keys via constant APOLLO_VAPID_PUBLIC_KEY / APOLLO_VAPID_PRIVATE_KEY
	 * or wp-config.php defines, or fall back to option 'apollo_vapid_public_key'.
	 */
	public function rest_push_vapid_key( \WP_REST_Request $request ): \WP_REST_Response {
		$public_key = defined( 'APOLLO_VAPID_PUBLIC_KEY' )
			? APOLLO_VAPID_PUBLIC_KEY
			: get_option( 'apollo_vapid_public_key', '' );

		if ( empty( $public_key ) ) {
			return new \WP_REST_Response( array( 'error' => 'VAPID key not configured' ), 503 );
		}

		return new \WP_REST_Response( array( 'public_key' => $public_key ), 200 );
	}

	/**
	 * Send a Web Push notification to all subscriptions of a user.
	 * Uses HTTP POST to the push endpoint (RFC 8030 — no external library needed for basic push).
	 * For encrypted payloads the site admin should install webpush-php or set up a worker.
	 *
	 * @param int    $user_id  Recipient.
	 * @param string $title    Notification title.
	 * @param string $body     Notification body.
	 * @param string $url      Click URL for the notification.
	 * @return void
	 */
	public function send_push_to_user( int $user_id, string $title, string $body, string $url = '' ): void {
		$subscriptions = get_user_meta( $user_id, '_apollo_push_subscriptions', true );
		if ( empty( $subscriptions ) || ! is_array( $subscriptions ) ) {
			return;
		}

		$payload = wp_json_encode(
			array(
				'title' => $title,
				'body'  => $body,
				'url'   => $url ?: home_url( '/' ),
				'icon'  => APOLLO_CDN_URL . 'img/icon-192.png',
			)
		);

		$vapid_subject = defined( 'APOLLO_VAPID_SUBJECT' ) ? APOLLO_VAPID_SUBJECT : 'mailto:admin@' . wp_parse_url( home_url(), PHP_URL_HOST );
		$public_key    = defined( 'APOLLO_VAPID_PUBLIC_KEY' ) ? APOLLO_VAPID_PUBLIC_KEY : get_option( 'apollo_vapid_public_key', '' );
		$private_key   = defined( 'APOLLO_VAPID_PRIVATE_KEY' ) ? APOLLO_VAPID_PRIVATE_KEY : get_option( 'apollo_vapid_private_key', '' );

		if ( empty( $public_key ) || empty( $private_key ) ) {
			// VAPID keys not configured — skip silently.
			return;
		}

		$stale_endpoints = array();

		foreach ( $subscriptions as $sub ) {
			$push_endpoint = $sub['endpoint'] ?? '';
			if ( empty( $push_endpoint ) ) {
				continue;
			}

			// Build minimal VAPID JWT (ES256) — header.payload.signature.
			$jwt = $this->build_vapid_jwt( $push_endpoint, $vapid_subject, $public_key, $private_key );
			if ( ! $jwt ) {
				continue;
			}

			$response = wp_remote_post(
				$push_endpoint,
				array(
					'headers' => array(
						'Authorization' => 'vapid t=' . $jwt . ', k=' . $public_key,
						'Content-Type'  => 'application/json',
						'TTL'           => '86400',
					),
					'body'    => $payload,
					'timeout' => 5,
				)
			);

			if ( is_wp_error( $response ) ) {
				continue;
			}

			$code = wp_remote_retrieve_response_code( $response );
			// 410 Gone = subscription expired/removed by browser.
			if ( 410 === $code ) {
				$stale_endpoints[] = $push_endpoint;
			}
		}

		// Prune stale subscriptions.
		if ( ! empty( $stale_endpoints ) ) {
			$clean = array_filter( $subscriptions, fn( $s ) => ! in_array( $s['endpoint'], $stale_endpoints, true ) );
			update_user_meta( $user_id, '_apollo_push_subscriptions', array_values( $clean ) );
		}
	}

	/**
	 * Build a minimal VAPID JWT for Web Push (ES256).
	 * Requires PHP ≥ 8.1 with OpenSSL extension.
	 *
	 * @param string $push_endpoint Full push service URL.
	 * @param string $subject       mailto: or https: VAPID subject.
	 * @param string $public_key    Base64url-encoded VAPID public key.
	 * @param string $private_key   Base64url-encoded VAPID private key.
	 * @return string|null JWT string or null on failure.
	 */
	private function build_vapid_jwt( string $push_endpoint, string $subject, string $public_key, string $private_key ): ?string {
		if ( ! function_exists( 'openssl_sign' ) ) {
			return null;
		}

		$parsed   = wp_parse_url( $push_endpoint );
		$audience = ( $parsed['scheme'] ?? 'https' ) . '://' . ( $parsed['host'] ?? '' );

		$header  = $this->base64url_encode(
			wp_json_encode(
				array(
					'typ' => 'JWT',
					'alg' => 'ES256',
				)
			)
		);
		$payload = $this->base64url_encode(
			wp_json_encode(
				array(
					'aud' => $audience,
					'exp' => time() + 43200,
					'sub' => $subject,
				)
			)
		);

		$signing_input = $header . '.' . $payload;

		// Recover PEM ECPrivateKey from raw base64url private key.
		$raw_private = $this->base64url_decode( $private_key );
		// Build SEC1 DER for P-256 EC private key prefix (fixed).
		$ec_pem = $this->raw_ec_private_to_pem( $raw_private, $this->base64url_decode( $public_key ) );
		if ( ! $ec_pem ) {
			return null;
		}

		$pkey = openssl_pkey_get_private( $ec_pem );
		if ( ! $pkey ) {
			return null;
		}

		openssl_sign( $signing_input, $raw_sig, $pkey, OPENSSL_ALGO_SHA256 );

		// DER-decode ASN.1 SEQUENCE(r, INTEGER; s, INTEGER) → 64-byte raw.
		$signature = $this->der_to_raw64( $raw_sig );
		if ( ! $signature ) {
			return null;
		}

		return $signing_input . '.' . $this->base64url_encode( $signature );
	}

	private function base64url_encode( string $data ): string {
		return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
	}

	private function base64url_decode( string $data ): string {
		return base64_decode( strtr( $data, '-_', '+/' ) . str_repeat( '=', ( 4 - strlen( $data ) % 4 ) % 4 ) );
	}

	/**
	 * Convert raw 32-byte EC private key + 65-byte uncompressed public key to PEM.
	 */
	private function raw_ec_private_to_pem( string $private_raw, string $public_raw ): ?string {
		if ( strlen( $private_raw ) !== 32 ) {
			return null;
		}
		// OID for prime256v1 (P-256): 1.2.840.10045.3.1.7
		$oid_p256 = "\x2a\x86\x48\xce\x3d\x03\x01\x07";
		// Minimal SEC1 ECPrivateKey DER with namedCurve + publicKey.
		$inner_der =
			"\x30" . // SEQUENCE
			$this->der_len(
				"\x02\x01\x01" . // version = 1
					"\x04\x20" . $private_raw . // privateKey (32 bytes)
					"\xa0\x0a\x06\x08" . $oid_p256 . // [0] namedCurve
					"\xa1" . $this->der_len( "\x03" . $this->der_len( "\x00" . $public_raw ) . "\x00" . $public_raw )
			);

		// Wrap in PKCS#8 ECParameters.
		$pkcs8 =
			"\x30" . $this->der_len(
				"\x02\x01\x00" . // version = 0
					"\x30" . $this->der_len(                                            // AlgorithmIdentifier
						"\x06\x07\x2a\x86\x48\xce\x3d\x02\x01" . // id-ecPublicKey OID
							"\x06\x08" . $oid_p256                                          // namedCurve P-256
					) .
					"\x04" . $this->der_len(
						"\x30" . $this->der_len(                   // privateKey OCTET STRING
							"\x02\x01\x01" .
							"\x04\x20" . $private_raw
						)
					)
			);

		return "-----BEGIN PRIVATE KEY-----\n" .
			chunk_split( base64_encode( $pkcs8 ), 64, "\n" ) .
			"-----END PRIVATE KEY-----\n";
	}

	private function der_len( string $content ): string {
		$len = strlen( $content );
		if ( $len < 0x80 ) {
			return chr( $len ) . $content;
		}
		if ( $len < 0x100 ) {
			return "\x81" . chr( $len ) . $content;
		}
		return "\x82" . chr( $len >> 8 ) . chr( $len & 0xff ) . $content;
	}

	/**
	 * Convert OpenSSL DER-encoded ECDSA signature (ASN.1 SEQUENCE) to 64-byte raw (r||s).
	 */
	private function der_to_raw64( string $der ): ?string {
		$offset = 0;
		if ( "\x30" !== $der[ $offset++ ] ) {
			return null;
		}
		// Skip seq length byte(s).
		$byte = ord( $der[ $offset++ ] );
		if ( $byte > 0x80 ) {
			$offset += ( $byte - 0x80 );
		}
		// r INTEGER.
		if ( "\x02" !== $der[ $offset++ ] ) {
			return null;
		}
		$r_len   = ord( $der[ $offset++ ] );
		$r_raw   = ltrim( substr( $der, $offset, $r_len ), "\x00" );
		$offset += $r_len;
		// s INTEGER.
		if ( "\x02" !== $der[ $offset++ ] ) {
			return null;
		}
		$s_len = ord( $der[ $offset++ ] );
		$s_raw = ltrim( substr( $der, $offset, $s_len ), "\x00" );

		return str_pad( $r_raw, 32, "\x00", STR_PAD_LEFT ) . str_pad( $s_raw, 32, "\x00", STR_PAD_LEFT );
	}
}
