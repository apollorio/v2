<?php

/**
 * Apollo Groups — Main Plugin Class
 *
 * Public communities (Comunas) adapted from BuddyPress bp-groups.
 * No private groups. Flat structure. No hierarchy.
 *
 * Registry compliance:
 *   REST: /groups, /groups/{id}, /groups/{id}/members, /groups/{id}/join, /groups/{id}/leave, /groups/comunas, /groups/nucleos, /groups/my
 *   Pages: /grupos, /comunas, /nucleos, /grupo/{slug}, /criar-grupo
 *   Shortcodes: [apollo_groups], [apollo_group], [apollo_my_groups]
 *   Tables: apollo_groups (core), apollo_group_members (core), apollo_group_meta (plugin)
 *
 * @package Apollo\Groups
 */

declare(strict_types=1);

namespace Apollo\Groups;

use Apollo\Core\Traits\BlankCanvasTrait;

if (! defined('ABSPATH')) {
    exit;
}

final class Plugin
{

    use BlankCanvasTrait;



    private static ?Plugin $instance = null;

    public static function instance(): Plugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('init', array($this, 'register_rewrite_rules'), 1);
        add_action('init', array($this, 'flush_rewrites_if_needed'), 99);
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_action('init', array($this, 'register_shortcodes'));
        add_filter('query_vars', array($this, 'register_query_vars'));
        add_action('template_redirect', array($this, 'handle_virtual_pages'), 5);

        // Frontend inline form (panel-forms.php hook)
        new FrontendForm();
    }

    public function flush_rewrites_if_needed(): void
    {
        $stored = get_option('apollo_groups_rewrite_version');
        if ($stored !== APOLLO_GROUPS_VERSION) {
            flush_rewrite_rules(true);
            update_option('apollo_groups_rewrite_version', APOLLO_GROUPS_VERSION);
        }
    }

    // ─── Rewrite Rules ──────────────────────────────────────────────
    public function register_rewrite_rules(): void
    {
        // Registry-compliant routes
        add_rewrite_rule('^grupos/?$', 'index.php?apollo_groups_page=directory', 'top');
        add_rewrite_rule('^comunas/?$', 'index.php?apollo_groups_page=comunas', 'top');
        add_rewrite_rule('^nucleos/?$', 'index.php?apollo_groups_page=nucleos', 'top');
        add_rewrite_rule('^grupo/([^/]+)/?$', 'index.php?apollo_groups_page=single&apollo_group_slug=$matches[1]', 'top');
        add_rewrite_rule('^criar-grupo/?$', 'index.php?apollo_groups_page=create', 'top');
    }

    public function register_query_vars(array $vars): array
    {
        $vars[] = 'apollo_groups_page';
        $vars[] = 'apollo_group_slug';
        return $vars;
    }

    public function handle_virtual_pages(): void
    {
        $page = get_query_var('apollo_groups_page');
        if (! $page) {
            return;
        }

        if ($page === 'create' && ! is_user_logged_in()) {
            wp_redirect(home_url('/acesso'));
            exit;
        }

        $templates = array(
            'directory' => 'groups.php',
            'comunas'   => 'comunas.php',
            'nucleos'   => 'nucleos.php',
            'single'    => 'single-group.php',
            'create'    => 'create-group.php',
        );

        $file = $templates[$page] ?? null;
        if ($file) {
            $template = APOLLO_GROUPS_PATH . 'templates/' . $file;
            $this->render_blank_canvas($template);
        }
    }

    // ─── REST API ──────────────────────────────────────────────────
    public function register_rest_routes(): void
    {
        $ns     = 'apollo/v1';
        $logged = function () {
            return is_user_logged_in();
        };

        register_rest_route(
            $ns,
            '/groups',
            array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'rest_list_groups'),
                    'permission_callback' => '__return_true',
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'rest_create_group'),
                    'permission_callback' => $logged,
                ),
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)',
            array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'rest_get_group'),
                    'permission_callback' => '__return_true',
                ),
                array(
                    'methods'             => 'PUT',
                    'callback'            => array($this, 'rest_update_group'),
                    'permission_callback' => $logged,
                ),
                array(
                    'methods'             => 'DELETE',
                    'callback'            => array($this, 'rest_delete_group'),
                    'permission_callback' => function () {
                        return current_user_can('apollo_moderate_content');
                    },
                ),
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/members',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'rest_get_members'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/join',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'rest_join_group'),
                'permission_callback' => $logged,
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/leave',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'rest_leave_group'),
                'permission_callback' => $logged,
            )
        );

        register_rest_route(
            $ns,
            '/groups/comunas',
            array(
                'methods'             => 'GET',
                'callback'            => function (\WP_REST_Request $request) {
                    $request->set_param('type', 'comuna');
                    return $this->rest_list_groups($request);
                },
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            $ns,
            '/groups/nucleos',
            array(
                'methods'             => 'GET',
                'callback'            => function (\WP_REST_Request $request) {
                    $request->set_param('type', 'nucleo');
                    return $this->rest_list_groups($request);
                },
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            $ns,
            '/groups/my',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'rest_my_groups'),
                'permission_callback' => $logged,
            )
        );

        // ── Member Management (BuddyPress bp-groups: promote/demote/ban/remove) ──

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/members/(?P<user_id>\d+)/promote',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'rest_promote_member'),
                'permission_callback' => $logged,
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/members/(?P<user_id>\d+)/demote',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'rest_demote_member'),
                'permission_callback' => $logged,
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/members/(?P<user_id>\d+)/ban',
            array(
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'rest_ban_member'),
                    'permission_callback' => $logged,
                ),
                array(
                    'methods'             => 'DELETE',
                    'callback'            => array($this, 'rest_unban_member'),
                    'permission_callback' => $logged,
                ),
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/members/(?P<user_id>\d+)',
            array(
                'methods'             => 'DELETE',
                'callback'            => array($this, 'rest_remove_member'),
                'permission_callback' => $logged,
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/bans',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'rest_get_bans'),
                'permission_callback' => $logged,
            )
        );

        // ── Invitations (BuddyPress bp-groups: invite/accept/reject) ──

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/invitations',
            array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'rest_get_group_invitations'),
                    'permission_callback' => $logged,
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'rest_invite_user'),
                    'permission_callback' => $logged,
                ),
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/invitations/accept',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'rest_accept_invitation'),
                'permission_callback' => $logged,
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/invitations/reject',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'rest_reject_invitation'),
                'permission_callback' => $logged,
            )
        );

        // My invitations (all pending)
        register_rest_route(
            $ns,
            '/my/group-invitations',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'rest_my_invitations'),
                'permission_callback' => $logged,
            )
        );

        // ── Membership Requests (for future private comunas) ──

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/requests',
            array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array($this, 'rest_get_requests'),
                    'permission_callback' => $logged,
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'rest_send_request'),
                    'permission_callback' => $logged,
                ),
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/requests/(?P<user_id>\d+)/accept',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'rest_accept_request'),
                'permission_callback' => $logged,
            )
        );

        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/requests/(?P<user_id>\d+)/reject',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'rest_reject_request'),
                'permission_callback' => $logged,
            )
        );

        // ── Group search ──
        register_rest_route(
            $ns,
            '/groups/search',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'rest_search_groups'),
                'permission_callback' => '__return_true',
            )
        );

        // ── Group Activity Feed (bp-groups activity screen) ──────────
        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/feed',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'rest_group_feed'),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'per_page' => array(
                        'default'           => 20,
                        'sanitize_callback' => 'absint',
                    ),
                    'page'     => array(
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                    ),
                ),
            )
        );

        // ── Group Avatar Upload (bp-groups avatar REST controller) ────
        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/avatar',
            array(
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'rest_upload_group_avatar'),
                    'permission_callback' => $logged,
                ),
                array(
                    'methods'             => 'DELETE',
                    'callback'            => array($this, 'rest_delete_group_avatar'),
                    'permission_callback' => $logged,
                ),
            )
        );

        // ── Group Cover Image (bp-groups cover image REST controller) ─
        register_rest_route(
            $ns,
            '/groups/(?P<id>\d+)/cover',
            array(
                array(
                    'methods'             => 'POST',
                    'callback'            => array($this, 'rest_upload_group_cover'),
                    'permission_callback' => $logged,
                ),
                array(
                    'methods'             => 'DELETE',
                    'callback'            => array($this, 'rest_delete_group_cover'),
                    'permission_callback' => $logged,
                ),
            )
        );
    }

    public function rest_list_groups(\WP_REST_Request $request): \WP_REST_Response
    {
        $search = sanitize_text_field($request->get_param('search') ?? '');
        $type   = sanitize_text_field($request->get_param('type') ?? '');
        $limit  = absint($request->get_param('per_page') ?? 20);
        $page   = absint($request->get_param('page') ?? 1);
        $offset = ($page - 1) * $limit;

        $groups = apollo_get_groups($limit, $offset, $search, $type);
        return new \WP_REST_Response($groups, 200);
    }

    public function rest_get_group(\WP_REST_Request $request): \WP_REST_Response
    {
        $group = apollo_get_group((int) $request->get_param('id'));
        if (! $group) {
            return new \WP_REST_Response(array('error' => 'Não encontrado'), 404);
        }
        return new \WP_REST_Response($group, 200);
    }

    public function rest_create_group(\WP_REST_Request $request): \WP_REST_Response
    {
        $name = sanitize_text_field($request->get_param('name') ?? '');
        if (empty($name)) {
            return new \WP_REST_Response(array('error' => 'Nome é obrigatório'), 400);
        }

        $group_id = apollo_create_group(
            array(
                'name'        => $name,
                'description' => wp_kses_post($request->get_param('description') ?? ''),
                'type'        => sanitize_text_field($request->get_param('type') ?? 'comuna'),
                'tags'        => sanitize_text_field($request->get_param('tags') ?? ''),
                'rules'       => sanitize_textarea_field($request->get_param('rules') ?? ''),
                'creator_id'  => get_current_user_id(),
            )
        );

        if ($group_id) {
            $group = apollo_get_group($group_id);
            return new \WP_REST_Response(
                array(
                    'id'   => $group_id,
                    'slug' => $group['slug'] ?? $group_id,
                ),
                201
            );
        }
        return new \WP_REST_Response(array('error' => 'Erro ao criar'), 500);
    }

    public function rest_update_group(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $id    = (int) $request->get_param('id');
        $group = apollo_get_group($id);
        if (! $group) {
            return new \WP_REST_Response(array('error' => 'Não encontrado'), 404);
        }

        // Only admin of group or site admin
        $uid = get_current_user_id();
        if ((int) $group['creator_id'] !== $uid && ! current_user_can('manage_options')) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }

        $data = array();
        if ($request->get_param('name')) {
            $data['name'] = sanitize_text_field($request->get_param('name'));
        }
        if ($request->get_param('description') !== null) {
            $data['description'] = wp_kses_post($request->get_param('description'));
        }
        if ($request->get_param('type')) {
            $data['type'] = sanitize_text_field($request->get_param('type'));
        }
        if ($request->get_param('tags') !== null) {
            $data['tags'] = sanitize_text_field($request->get_param('tags'));
        }
        if ($request->get_param('rules') !== null) {
            $data['rules'] = sanitize_textarea_field($request->get_param('rules'));
        }

        if (! empty($data)) {
            $wpdb->update($wpdb->prefix . 'apollo_groups', $data, array('id' => $id));
        }

        return new \WP_REST_Response(array('updated' => true), 200);
    }

    public function rest_delete_group(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $id     = (int) $request->get_param('id');
        $prefix = $wpdb->prefix . 'apollo_';

        $wpdb->delete("{$prefix}group_members", array('group_id' => $id));
        $wpdb->delete("{$prefix}group_meta", array('group_id' => $id));
        $wpdb->delete("{$prefix}groups", array('id' => $id));

        return new \WP_REST_Response(array('deleted' => true), 200);
    }

    public function rest_get_members(\WP_REST_Request $request): \WP_REST_Response
    {
        $members = apollo_get_group_members((int) $request->get_param('id'));
        return new \WP_REST_Response($members, 200);
    }

    public function rest_join_group(\WP_REST_Request $request): \WP_REST_Response
    {
        $success = apollo_join_group((int) $request->get_param('id'), get_current_user_id());
        return new \WP_REST_Response(array('joined' => $success), $success ? 200 : 500);
    }

    public function rest_leave_group(\WP_REST_Request $request): \WP_REST_Response
    {
        $success = apollo_leave_group((int) $request->get_param('id'), get_current_user_id());
        return new \WP_REST_Response(array('left' => $success), $success ? 200 : 500);
    }

    public function rest_my_groups(): \WP_REST_Response
    {
        $groups = apollo_get_user_groups(get_current_user_id());
        return new \WP_REST_Response($groups, 200);
    }

    // ── Member Management Callbacks ─────────────────────────────────

    public function rest_promote_member(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $user_id  = (int) $request->get_param('user_id');
        $role     = sanitize_key($request->get_param('role') ?? 'moderator');
        $by       = get_current_user_id();

        if (! apollo_is_group_member($group_id, $user_id)) {
            return new \WP_REST_Response(array('error' => 'Usuário não é membro'), 404);
        }
        if (! apollo_user_can_manage_group($group_id, $by)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }
        $ok = apollo_promote_group_member($group_id, $user_id, $role, $by);
        return new \WP_REST_Response(
            array(
                'promoted' => $ok,
                'role'     => $role,
            ),
            $ok ? 200 : 500
        );
    }

    public function rest_demote_member(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $user_id  = (int) $request->get_param('user_id');
        $by       = get_current_user_id();

        if (! apollo_user_can_manage_group($group_id, $by)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }
        $ok = apollo_demote_group_member($group_id, $user_id, $by);
        return new \WP_REST_Response(array('demoted' => $ok), $ok ? 200 : 500);
    }

    public function rest_ban_member(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $user_id  = (int) $request->get_param('user_id');
        $reason   = sanitize_text_field($request->get_param('reason') ?? '');
        $by       = get_current_user_id();

        if (! apollo_user_can_manage_group($group_id, $by)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }
        $ok = apollo_ban_group_member($group_id, $user_id, $by, $reason);
        return new \WP_REST_Response(array('banned' => $ok), $ok ? 200 : 400);
    }

    public function rest_unban_member(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $user_id  = (int) $request->get_param('user_id');
        $by       = get_current_user_id();

        if (! apollo_user_can_manage_group($group_id, $by)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }
        $ok = apollo_unban_group_member($group_id, $user_id, $by);
        return new \WP_REST_Response(array('unbanned' => $ok), $ok ? 200 : 400);
    }

    public function rest_remove_member(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $user_id  = (int) $request->get_param('user_id');
        $by       = get_current_user_id();

        // User can remove themselves, admin/mod can remove others
        if ($user_id !== $by && ! apollo_user_can_manage_group($group_id, $by)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }
        $ok = $user_id === $by
            ? apollo_leave_group($group_id, $user_id)
            : apollo_remove_group_member($group_id, $user_id, $by);
        return new \WP_REST_Response(array('removed' => $ok), $ok ? 200 : 400);
    }

    public function rest_get_bans(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $by       = get_current_user_id();

        if (! apollo_user_can_manage_group($group_id, $by)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }
        return new \WP_REST_Response(apollo_get_group_bans($group_id), 200);
    }

    // ── Invitation Callbacks ────────────────────────────────────────

    public function rest_get_group_invitations(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $by       = get_current_user_id();

        if (! apollo_user_can_manage_group($group_id, $by)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }
        return new \WP_REST_Response(apollo_get_group_invitations($group_id), 200);
    }

    public function rest_invite_user(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $user_id  = absint($request->get_param('user_id'));
        $message  = sanitize_textarea_field($request->get_param('message') ?? '');
        $by       = get_current_user_id();

        if (! $user_id) {
            return new \WP_REST_Response(array('error' => 'user_id obrigatório'), 400);
        }
        if (! get_userdata($user_id)) {
            return new \WP_REST_Response(array('error' => 'Usuário não encontrado'), 404);
        }
        if (! apollo_is_group_member($group_id, $by) && ! current_user_can('manage_options')) {
            return new \WP_REST_Response(array('error' => 'Apenas membros podem convidar'), 403);
        }
        $ok = apollo_invite_to_group($group_id, $user_id, $by, $message);
        return new \WP_REST_Response(array('invited' => $ok), $ok ? 201 : 400);
    }

    public function rest_accept_invitation(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $uid      = get_current_user_id();
        $ok       = apollo_accept_group_invite($group_id, $uid);
        return new \WP_REST_Response(array('accepted' => $ok), $ok ? 200 : 400);
    }

    public function rest_reject_invitation(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $uid      = get_current_user_id();
        $ok       = apollo_reject_group_invite($group_id, $uid);
        return new \WP_REST_Response(array('rejected' => $ok), $ok ? 200 : 400);
    }

    public function rest_my_invitations(): \WP_REST_Response
    {
        $invites = apollo_get_user_group_invitations(get_current_user_id());
        return new \WP_REST_Response(
            array(
                'invitations' => $invites,
                'count'       => count($invites),
            ),
            200
        );
    }

    // ── Membership Request Callbacks ────────────────────────────────

    public function rest_get_requests(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $by       = get_current_user_id();

        if (! apollo_user_can_manage_group($group_id, $by)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }
        return new \WP_REST_Response(apollo_get_group_requests($group_id), 200);
    }

    public function rest_send_request(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $uid      = get_current_user_id();
        $message  = sanitize_textarea_field($request->get_param('message') ?? '');
        $ok       = apollo_send_group_request($group_id, $uid, $message);
        return new \WP_REST_Response(array('requested' => $ok), $ok ? 201 : 400);
    }

    public function rest_accept_request(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id    = (int) $request->get_param('id');
        $target_user = (int) $request->get_param('user_id');
        $by          = get_current_user_id();
        $ok          = apollo_accept_group_request($group_id, $target_user, $by);
        return new \WP_REST_Response(array('accepted' => $ok), $ok ? 200 : 400);
    }

    public function rest_reject_request(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id    = (int) $request->get_param('id');
        $target_user = (int) $request->get_param('user_id');
        $by          = get_current_user_id();
        $ok          = apollo_reject_group_request($group_id, $target_user, $by);
        return new \WP_REST_Response(array('rejected' => $ok), $ok ? 200 : 400);
    }

    // ── Group Search ────────────────────────────────────────────────

    public function rest_search_groups(\WP_REST_Request $request): \WP_REST_Response
    {
        $term   = sanitize_text_field($request->get_param('q') ?? '');
        $limit  = absint($request->get_param('per_page') ?? 20);
        $type   = sanitize_text_field($request->get_param('type') ?? '');
        $groups = apollo_get_groups($limit, 0, $term, $type);
        $total  = apollo_get_total_group_count($type);
        return new \WP_REST_Response(
            array(
                'groups' => $groups,
                'total'  => $total,
                'term'   => $term,
            ),
            200
        );
    }

    // ─── Shortcodes ─────────────────────────────────────────────────
    public function register_shortcodes(): void
    {
        add_shortcode('apollo_groups', array($this, 'shortcode_groups'));
        add_shortcode('apollo_group', array($this, 'shortcode_group'));
        add_shortcode('apollo_my_groups', array($this, 'shortcode_my_groups'));
    }

    public function shortcode_groups(array $atts): string
    {
        $a      = shortcode_atts(
            array(
                'type'  => 'all',
                'limit' => 12,
            ),
            $atts
        );
        $groups = apollo_get_groups((int) $a['limit']);

        ob_start();
        echo '<div class="apollo-groups-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">';
        foreach ($groups as $g) {
?>
            <a href="<?php echo esc_url(home_url('/grupo/' . $g['slug'])); ?>" class="apollo-group-card" style="background:var(--card-bg,#fff);border:1px solid var(--glass-border,#e2e8f0);border-radius:12px;padding:1.25rem;text-decoration:none;color:inherit;">
                <h3 style="margin:0 0 .5rem;font-size:1rem;"><?php echo esc_html($g['name']); ?></h3>
                <p style="color:var(--ap-text-muted);font-size:.85rem;margin:0 0 .5rem;"><?php echo esc_html(wp_trim_words($g['description'], 15)); ?></p>
                <span style="font-size:.75rem;color:var(--ap-text-muted);"><?php echo esc_html($g['member_count']); ?> membros</span>
            </a>
        <?php
        }
        if (empty($groups)) {
            echo '<p style="grid-column:1/-1;text-align:center;color:var(--ap-text-muted);">Nenhum grupo encontrado.</p>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    public function shortcode_group(array $atts): string
    {
        $a = shortcode_atts(array('id' => 0), $atts);
        if (! $a['id']) {
            return '';
        }
        $g = apollo_get_group((int) $a['id']);
        if (! $g) {
            return '<p>Comuna não encontrada.</p>';
        }

        ob_start();
        ?>
        <div class="apollo-group-detail">
            <h2><?php echo esc_html($g['name']); ?></h2>
            <div><?php echo wp_kses_post($g['description']); ?></div>
            <p style="color:var(--ap-text-muted);font-size:.85rem;"><?php echo esc_html($g['member_count']); ?> membros</p>
        </div>
<?php
        return ob_get_clean();
    }

    public function shortcode_my_groups(array $atts): string
    {
        if (! is_user_logged_in()) {
            return '<p>Faça login para ver suas comunas.</p>';
        }
        $groups = apollo_get_user_groups(get_current_user_id());

        ob_start();
        echo '<div class="apollo-my-groups">';
        foreach ($groups as $g) {
            echo '<a href="' . esc_url(home_url('/grupo/' . $g['slug'])) . '" style="display:block;padding:.75rem 0;border-bottom:1px solid var(--glass-border);">';
            echo '<strong>' . esc_html($g['name']) . '</strong> <span style="color:var(--ap-text-muted);font-size:.8rem;">(' . esc_html($g['role']) . ')</span>';
            echo '</a>';
        }
        if (empty($groups)) {
            echo '<p style="color:var(--ap-text-muted);">Você não participa de nenhum grupo.</p>';
        }
        echo '</div>';
        return ob_get_clean();
    }

	// ─── Group Activity Feed ─────────────────────────────────────────

    /**
     * GET /groups/{id}/feed — Activity posts within a group.
     * Adapted from BuddyPress bp-groups screens/single/activity.php.
     */
    public function rest_group_feed(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $group_id = (int) $request->get_param('id');
        $per_page = min(50, absint($request->get_param('per_page') ?? 20));
        $page     = absint($request->get_param('page') ?? 1);
        $offset   = ($page - 1) * $per_page;

        if (! apollo_get_group($group_id)) {
            return new \WP_REST_Response(array('error' => 'Grupo não encontrado'), 404);
        }

        $table = $wpdb->prefix . 'apollo_activity';

        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT a.*, u.display_name, u.user_login
                 FROM {$table} a
                 LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID
                 WHERE a.component = 'groups' AND a.item_id = %d AND a.is_spam = 0 AND a.hide_sitewide = 0
                 ORDER BY a.created_at DESC
                 LIMIT %d OFFSET %d",
                $group_id,
                $per_page,
                $offset
            ),
            ARRAY_A
        );

        $total = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE component = 'groups' AND item_id = %d AND is_spam = 0",
                $group_id
            )
        );

        foreach ($posts as &$p) {
            $uid              = (int) $p['user_id'];
            $p['avatar_url']  = function_exists('apollo_get_user_avatar_url')
                ? apollo_get_user_avatar_url($uid)
                : get_avatar_url($uid, array('size' => 40));
            $p['profile_url'] = home_url('/id/' . $p['user_login']);
            $p['time_ago']    = function_exists('apollo_time_ago') ? apollo_time_ago($p['created_at']) : $p['created_at'];
        }

        return new \WP_REST_Response(
            array(
                'posts'    => $posts ?: array(),
                'total'    => $total,
                'pages'    => ceil($total / $per_page),
                'page'     => $page,
                'group_id' => $group_id,
            ),
            200
        );
    }

	// ─── Group Avatar / Cover Upload ─────────────────────────────────

    /**
     * POST /groups/{id}/avatar — Upload group avatar image.
     * Adapted from BuddyPress class-bp-groups-avatar-rest-controller.php.
     */
    public function rest_upload_group_avatar(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $uid      = get_current_user_id();

        $group = apollo_get_group($group_id);
        if (! $group) {
            return new \WP_REST_Response(array('error' => 'Grupo não encontrado'), 404);
        }
        if (! apollo_user_can_manage_group($group_id, $uid)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }

        $files = $request->get_file_params();
        if (empty($files['file'])) {
            return new \WP_REST_Response(array('error' => 'Arquivo obrigatório'), 400);
        }

        $allowed = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
        if (! in_array($files['file']['type'] ?? '', $allowed, true)) {
            return new \WP_REST_Response(array('error' => 'Apenas imagens aceitas (JPG, PNG, GIF, WebP)'), 415);
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $_FILES['group_avatar'] = $files['file'];
        $attachment_id          = media_handle_upload('group_avatar', 0);

        if (is_wp_error($attachment_id)) {
            return new \WP_REST_Response(array('error' => $attachment_id->get_error_message()), 500);
        }

        // Delete old avatar
        global $wpdb;
        $old = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM {$wpdb->prefix}apollo_group_meta WHERE group_id = %d AND meta_key = 'avatar_id'",
                $group_id
            )
        );
        if ($old) {
            wp_delete_attachment((int) $old, true);
        }

        // Save new avatar to group_meta
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}apollo_group_meta WHERE group_id = %d AND meta_key = 'avatar_id'",
                $group_id
            )
        );
        if ($exists) {
            $wpdb->update(
                $wpdb->prefix . 'apollo_group_meta',
                array('meta_value' => $attachment_id),
                array(
                    'group_id' => $group_id,
                    'meta_key' => 'avatar_id',
                )
            );
        } else {
            $wpdb->insert(
                $wpdb->prefix . 'apollo_group_meta',
                array(
                    'group_id'   => $group_id,
                    'meta_key'   => 'avatar_id',
                    'meta_value' => $attachment_id,
                )
            );
        }

        do_action('apollo/groups/avatar_updated', $group_id, $attachment_id, $uid);

        return new \WP_REST_Response(
            array(
                'success'    => true,
                'avatar_url' => wp_get_attachment_image_url($attachment_id, 'medium'),
                'group_id'   => $group_id,
            ),
            200
        );
    }

    /**
     * DELETE /groups/{id}/avatar — Remove group avatar.
     */
    public function rest_delete_group_avatar(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $uid      = get_current_user_id();

        if (! apollo_user_can_manage_group($group_id, $uid)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }

        global $wpdb;
        $avatar_id = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM {$wpdb->prefix}apollo_group_meta WHERE group_id = %d AND meta_key = 'avatar_id'",
                $group_id
            )
        );
        if ($avatar_id) {
            wp_delete_attachment($avatar_id, true);
            $wpdb->delete(
                $wpdb->prefix . 'apollo_group_meta',
                array(
                    'group_id' => $group_id,
                    'meta_key' => 'avatar_id',
                )
            );
        }

        return new \WP_REST_Response(array('deleted' => true), 200);
    }

    /**
     * POST /groups/{id}/cover — Upload group cover image.
     * Adapted from BuddyPress class-bp-groups-cover-rest-controller.php.
     */
    public function rest_upload_group_cover(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $uid      = get_current_user_id();

        $group = apollo_get_group($group_id);
        if (! $group) {
            return new \WP_REST_Response(array('error' => 'Grupo não encontrado'), 404);
        }
        if (! apollo_user_can_manage_group($group_id, $uid)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }

        $files = $request->get_file_params();
        if (empty($files['file'])) {
            return new \WP_REST_Response(array('error' => 'Arquivo obrigatório'), 400);
        }

        $allowed = array('image/jpeg', 'image/png', 'image/webp');
        if (! in_array($files['file']['type'] ?? '', $allowed, true)) {
            return new \WP_REST_Response(array('error' => 'Apenas JPG, PNG ou WebP'), 415);
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $_FILES['group_cover'] = $files['file'];
        $attachment_id         = media_handle_upload('group_cover', 0);

        if (is_wp_error($attachment_id)) {
            return new \WP_REST_Response(array('error' => $attachment_id->get_error_message()), 500);
        }

        // Save to groups table cover_image column
        global $wpdb;
        $old_cover = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT cover_image FROM {$wpdb->prefix}apollo_groups WHERE id = %d",
                $group_id
            )
        );
        if ($old_cover) {
            wp_delete_attachment($old_cover, true);
        }

        $wpdb->update(
            $wpdb->prefix . 'apollo_groups',
            array('cover_image' => $attachment_id),
            array('id' => $group_id)
        );

        do_action('apollo/groups/cover_updated', $group_id, $attachment_id, $uid);

        return new \WP_REST_Response(
            array(
                'success'   => true,
                'cover_url' => wp_get_attachment_image_url($attachment_id, 'full'),
                'group_id'  => $group_id,
            ),
            200
        );
    }

    /**
     * DELETE /groups/{id}/cover — Remove group cover image.
     */
    public function rest_delete_group_cover(\WP_REST_Request $request): \WP_REST_Response
    {
        $group_id = (int) $request->get_param('id');
        $uid      = get_current_user_id();

        if (! apollo_user_can_manage_group($group_id, $uid)) {
            return new \WP_REST_Response(array('error' => 'Sem permissão'), 403);
        }

        global $wpdb;
        $cover_id = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT cover_image FROM {$wpdb->prefix}apollo_groups WHERE id = %d",
                $group_id
            )
        );
        if ($cover_id) {
            wp_delete_attachment($cover_id, true);
            $wpdb->update(
                $wpdb->prefix . 'apollo_groups',
                array('cover_image' => 0),
                array('id' => $group_id)
            );
        }

        return new \WP_REST_Response(array('deleted' => true), 200);
    }
}
