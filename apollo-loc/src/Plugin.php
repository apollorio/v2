<?php

/**
 * Plugin — Singleton orquestrador
 *
 * @package Apollo\Local
 */

declare(strict_types=1);

namespace Apollo\Local;

if (! defined('ABSPATH')) {
    exit;
}

class Plugin
{

    private static ?Plugin $instance = null;

    private Registry $registry;
    private Shortcodes $shortcodes;
    private TemplateLoader $template_loader;
    private Integrations $integrations;

    public function __construct()
    {
        if (null !== self::$instance) {
            return;
        }
        self::$instance = $this;

        $this->registry        = new Registry();
        $this->shortcodes      = new Shortcodes();
        $this->template_loader = new TemplateLoader();
        $this->integrations    = new Integrations();

        if (is_admin()) {
            new Admin\Dashboard();
        }

        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    /**
     * Registra assets CSS/JS
     */
    public function register_assets(): void
    {
        // RemixIcon: loaded by CDN core.js — no separate registration needed

        // Leaflet
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

        // CSS principal
        wp_register_style(
            'apollo-local',
            APOLLO_LOCAL_URL . 'assets/css/apollo-local.css',
            array(),
            APOLLO_LOCAL_VERSION
        );

        // CSS estilo apollo-v1
        wp_register_style(
            'apollo-local-v1',
            APOLLO_LOCAL_URL . 'styles/apollo-v1/style.css',
            array('apollo-local'),
            APOLLO_LOCAL_VERSION
        );

        // JS principal
        wp_register_script(
            'apollo-local',
            APOLLO_LOCAL_URL . 'assets/js/apollo-local.js',
            array(),
            APOLLO_LOCAL_VERSION,
            true
        );

        // JS mapa
        wp_register_script(
            'apollo-local-map',
            APOLLO_LOCAL_URL . 'assets/js/apollo-local-map.js',
            array('leaflet'),
            APOLLO_LOCAL_VERSION,
            true
        );

        wp_localize_script(
            'apollo-local',
            'apolloLocal',
            array(
                'rest_url'       => esc_url_raw(rest_url(APOLLO_LOCAL_REST_NAMESPACE)),
                'nonce'          => wp_create_nonce('wp_rest'),
                'plugin_url'     => APOLLO_LOCAL_URL,
                'default_center' => array(-22.9068, -43.1729), // Rio de Janeiro
                'default_zoom'   => 12,
                'i18n'           => array(
                    'no_results'  => __('Nenhum local encontrado.', 'apollo-local'),
                    'nearby'      => __('Próximos', 'apollo-local'),
                    'loading'     => __('Carregando...', 'apollo-local'),
                    'search'      => __('Buscar locais...', 'apollo-local'),
                    'events_here' => __('eventos aqui', 'apollo-local'),
                ),
            )
        );
    }

    /**
     * Registra rotas REST
     */
    public function register_rest_routes(): void
    {
        $controller = new API\LocalsController();
        $controller->register_routes();
    }

    public function registry(): Registry
    {
        return $this->registry;
    }

    public function shortcodes(): Shortcodes
    {
        return $this->shortcodes;
    }

    public function template_loader(): TemplateLoader
    {
        return $this->template_loader;
    }

    public static function instance(): ?Plugin
    {
        return self::$instance;
    }
}
