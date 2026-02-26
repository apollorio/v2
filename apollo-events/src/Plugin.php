<?php

/**
 * Plugin principal — Singleton
 *
 * Orquestra todos os componentes: Registry, Expiration, Shortcodes, REST, Templates, Dashboard.
 * Segue a filosofia: "Each plugin = ONE focused responsibility. Connected via apollo-core hooks."
 *
 * @package Apollo\Event
 */

declare(strict_types=1);

namespace Apollo\Event;

use Apollo\Core\Traits\BlankCanvasTrait;

if (! defined('ABSPATH')) {
    exit;
}

final class Plugin
{

    use BlankCanvasTrait;


    private static ?Plugin $instance = null;

    public static function get_instance(): Plugin
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    /**
     * Inicializa todos os componentes do plugin
     */
    public function init(): void
    {
        // Carregar traduções
        add_action('init', array($this, 'load_textdomain'));

        // Virtual pages
        add_action('init', array($this, 'register_rewrite_rules'), 1);
        add_filter('query_vars', array($this, 'register_query_vars'));
        add_action('template_redirect', array($this, 'handle_virtual_pages'), 5);

        // Assets front-end
        add_action('init', array($this, 'register_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        // REST API
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // Componentes
        $this->init_components();
    }

    /**
     * Register rewrite rules for virtual event pages
     */
    public function register_rewrite_rules(): void
    {
        add_rewrite_rule('^novo-evento/?$', 'index.php?apollo_event_page=create', 'top');
        // Backward compat
        add_rewrite_rule('^criar-evento/?$', 'index.php?apollo_event_page=create', 'top');
    }

    public function register_query_vars(array $vars): array
    {
        $vars[] = 'apollo_event_page';
        return $vars;
    }

    public function handle_virtual_pages(): void
    {
        $page = get_query_var('apollo_event_page');
        if (! $page) {
            return;
        }

        if (! is_user_logged_in()) {
            wp_redirect(home_url('/acesso'));
            exit;
        }

        if ($page === 'create') {
            $template = APOLLO_EVENT_DIR . 'styles/base/create-event.php';
            $this->render_blank_canvas($template);
        }
    }

    /**
     * Inicializa componentes internos
     */
    private function init_components(): void
    {
        // Registro de CPT/meta via hooks do apollo-core
        new Registry();

        // Sistema de expiração (30 min após end_date + end_time)
        new Expiration();

        // Shortcode [a-eve]
        new Shortcodes();

        // Template Loader
        new TemplateLoader();

        // Schema.org JSON-LD para SEO / rich snippets
        new StructuredData();

        // Integrações com outros plugins Apollo
        new Integrations();

        // Frontend inline form (panel-forms.php hook)
        new FrontendForm();

        // Admin Dashboard (aba em Apollo Dashboard)
        if (is_admin()) {
            new Admin\Dashboard();
        }

        // CENA-RIO (migrated from apollo-shortcodes)
        CenaRio::init();
    }

    /**
     * Carrega traduções
     */
    public function load_textdomain(): void
    {
        load_plugin_textdomain(
            'apollo-events',
            false,
            dirname(APOLLO_EVENT_BASENAME) . '/languages'
        );
    }

    /**
     * Registra assets globais do plugin
     */
    public function register_assets(): void
    {
        // CSS principal
        wp_register_style(
            'apollo-events',
            APOLLO_EVENT_URL . 'assets/css/apollo-events.css',
            array(),
            APOLLO_EVENT_VERSION
        );

        // CSS do calendário
        wp_register_style(
            'apollo-events-calendar',
            APOLLO_EVENT_URL . 'assets/css/apollo-events-calendar.css',
            array('apollo-events'),
            APOLLO_EVENT_VERSION
        );

        // CSS de cards
        wp_register_style(
            'apollo-events-cards',
            APOLLO_EVENT_URL . 'assets/css/apollo-events-cards.css',
            array('apollo-events'),
            APOLLO_EVENT_VERSION
        );

        // Leaflet (OSM map)
        wp_register_style(
            'leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            array(),
            '1.9.4'
        );

        wp_register_script(
            'leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            array(),
            '1.9.4',
            true
        );

        // JS principal
        wp_register_script(
            'apollo-events',
            APOLLO_EVENT_URL . 'assets/js/apollo-events.js',
            array('jquery'),
            APOLLO_EVENT_VERSION,
            true
        );

        // JS do calendário
        wp_register_script(
            'apollo-events-calendar',
            APOLLO_EVENT_URL . 'assets/js/apollo-events-calendar.js',
            array('apollo-events'),
            APOLLO_EVENT_VERSION,
            true
        );

        // JS do mapa
        wp_register_script(
            'apollo-events-map',
            APOLLO_EVENT_URL . 'assets/js/apollo-events-map.js',
            array('leaflet'),
            APOLLO_EVENT_VERSION,
            true
        );
    }

    /**
     * Enfileira assets quando necessário
     */
    public function enqueue_assets(): void
    {
        // Variáveis JS globais para REST API
        wp_localize_script(
            'apollo-events',
            'apolloEvents',
            array(
                'rest_url'   => esc_url(rest_url(APOLLO_EVENT_REST_NAMESPACE)),
                'nonce'      => wp_create_nonce('wp_rest'),
                'plugin_url' => APOLLO_EVENT_URL,
                'i18n'       => array(
                    'loading'   => __('Carregando...', 'apollo-events'),
                    'no_events' => __('Nenhum evento encontrado.', 'apollo-events'),
                    'gone'      => __('Encerrado', 'apollo-events'),
                    'today'     => __('Hoje', 'apollo-events'),
                ),
            )
        );
    }

    /**
     * Registra rotas REST conforme apollo-registry.json
     */
    public function register_rest_routes(): void
    {
        $controller = new API\EventsController();
        $controller->register_routes();
    }
}
