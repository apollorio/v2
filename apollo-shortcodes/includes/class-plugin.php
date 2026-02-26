<?php

/**
 * Main Plugin Class (Singleton)
 *
 * @package Apollo\Shortcodes
 */

declare(strict_types=1);

namespace Apollo\Shortcodes;

// Prevent direct access.
if (! \defined('ABSPATH')) {
    exit;
}

/**
 * Main Plugin class
 */
final class Plugin
{

    /**
     * Plugin instance
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    /**
     * Get plugin instance (Singleton)
     *
     * @return Plugin
     */
    public static function get_instance(): Plugin
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor
     */
    private function __construct()
    {
        // Singleton - use get_instance()
    }

    /**
     * Initialize plugin
     *
     * @return void
     */
    public function init(): void
    {
        // Load text domain
        $this->load_textdomain();

        // Register hooks
        add_action('init', array($this, 'load_shortcodes'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // Search assets — controller migrated to apollo-core
        add_action('wp_enqueue_scripts', array($this, 'enqueue_search_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_search_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_search_enhancer'));
    }

    /**
     * Load plugin textdomain
     *
     * @return void
     */
    private function load_textdomain(): void
    {
        load_plugin_textdomain(
            'apollo-shortcodes',
            false,
            \dirname(APOLLO_SHORTCODE_BASENAME) . '/languages'
        );
    }

    /**
     * Load shortcode classes
     *
     * @return void
     */
    public function load_shortcodes(): void
    {
        // Load shortcode classes from includes directory
        // Remaining shortcode classes (others migrated to proper plugins)
        $shortcode_files = array(
            'class-apollo-shortcode-registry.php',
        );

        foreach ($shortcode_files as $file) {
            $file_path = APOLLO_SHORTCODE_DIR . 'includes/' . $file;
            if (\file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }

    /**
     * Register REST API routes
     *
     * @return void
     */
    public function register_rest_routes(): void
    {
        // GET /shortcodes - List all shortcodes
        register_rest_route(
            'apollo/v1',
            '/shortcodes',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'rest_list_shortcodes'),
                'permission_callback' => '__return_true',
            )
        );

        // GET /shortcodes/{tag} - Get shortcode info
        register_rest_route(
            'apollo/v1',
            '/shortcodes/(?P<tag>[a-z_]+)',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'rest_get_shortcode'),
                'permission_callback' => '__return_true',
            )
        );

        // POST /shortcodes/render - Render shortcode
        register_rest_route(
            'apollo/v1',
            '/shortcodes/render',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'rest_render_shortcode'),
                'permission_callback' => array($this, 'check_edit_permission'),
            )
        );
    }

    /**
     * REST: List all registered shortcodes
     *
     * @return \WP_REST_Response
     */
    public function rest_list_shortcodes(): \WP_REST_Response
    {
        global $shortcode_tags;

        $apollo_shortcodes = array();
        foreach ($shortcode_tags as $tag => $callback) {
            if (\strpos($tag, 'apollo_') === 0) {
                $apollo_shortcodes[] = array(
                    'tag'      => $tag,
                    'callback' => \is_array($callback) ? \get_class($callback[0]) . '::' . $callback[1] : $callback,
                );
            }
        }

        return new \WP_REST_Response(
            array(
                'success'    => true,
                'shortcodes' => $apollo_shortcodes,
                'total'      => \count($apollo_shortcodes),
            ),
            200
        );
    }

    /**
     * REST: Get shortcode info
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function rest_get_shortcode(\WP_REST_Request $request): \WP_REST_Response
    {
        global $shortcode_tags;

        $tag = $request->get_param('tag');

        if (! isset($shortcode_tags[$tag])) {
            return new \WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Shortcode not found',
                ),
                404
            );
        }

        $callback = $shortcode_tags[$tag];

        return new \WP_REST_Response(
            array(
                'success'  => true,
                'tag'      => $tag,
                'callback' => \is_array($callback) ? \get_class($callback[0]) . '::' . $callback[1] : $callback,
            ),
            200
        );
    }

    /**
     * Allowed Apollo shortcodes whitelist.
     *
     * Only these tags can be executed via REST render.
     *
     * @var string[]
     */
    private const ALLOWED_SHORTCODES = array(
        'apollo_newsletter',
        'apollo_cena_submit_event',
        'apollo_top_sounds',
        'apollo_fav_dashboard',
        'apollo_user_stats',
        'apollo_wow_chart',
    );

    /**
     * REST: Render shortcode (whitelisted Apollo tags only)
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function rest_render_shortcode(\WP_REST_Request $request): \WP_REST_Response
    {
        $tag     = sanitize_key($request->get_param('tag') ?? '');
        $content = wp_kses_post($request->get_param('content') ?? '');

        if (empty($tag)) {
            return new \WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Shortcode tag is required.',
                ),
                400
            );
        }

        if (! \in_array($tag, self::ALLOWED_SHORTCODES, true)) {
            return new \WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Shortcode tag not allowed.',
                    'allowed' => self::ALLOWED_SHORTCODES,
                ),
                403
            );
        }

        $shortcode_string = empty($content)
            ? '[' . $tag . ']'
            : '[' . $tag . ']' . $content . '[/' . $tag . ']';

        $output = do_shortcode($shortcode_string);

        return new \WP_REST_Response(
            array(
                'success' => true,
                'tag'     => $tag,
                'html'    => $output,
            ),
            200
        );
    }

    /**
     * Enqueue admin-only search enhancer (auto-filter for list tables with 10+ rows).
     *
     * @return void
     */
    public function enqueue_admin_search_enhancer(): void
    {
        wp_enqueue_script(
            'apollo-search-admin',
            APOLLO_SHORTCODE_URL . 'assets/js/apollo-search-admin.js',
            array('apollo-search'),
            APOLLO_SHORTCODE_VERSION,
            true
        );
    }

    /**
     * Enqueue Apollo Search assets globally (frontend + admin).
     *
     * @return void
     */
    public function enqueue_search_assets(): void
    {
        wp_enqueue_style(
            'apollo-search',
            APOLLO_SHORTCODE_URL . 'assets/css/apollo-search.css',
            array(),
            APOLLO_SHORTCODE_VERSION
        );

        wp_enqueue_script(
            'apollo-search',
            APOLLO_SHORTCODE_URL . 'assets/js/apollo-search.js',
            array(),
            APOLLO_SHORTCODE_VERSION,
            true
        );

        wp_localize_script(
            'apollo-search',
            'apolloSearch',
            array(
                'restUrl' => esc_url_raw(rest_url('apollo/v1/')),
                'nonce'   => wp_create_nonce('wp_rest'),
            )
        );
    }

    /**
     * Check edit permission for REST routes
     *
     * @return bool
     */
    public function check_edit_permission(): bool
    {
        return current_user_can('edit_posts');
    }
}
