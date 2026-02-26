<?php

/**
 * Main Plugin Class (Singleton)
 *
 * Adapted from WPAdverts main class + Apollo Core pattern.
 * Handles script registration, REST API init, admin menus.
 *
 * @package Apollo\Adverts
 */

declare(strict_types=1);

namespace Apollo\Adverts;

use Apollo\Core\Traits\BlankCanvasTrait;

if (! defined('ABSPATH')) {
    exit;
}

final class Plugin
{

    use BlankCanvasTrait;



    private static ?Plugin $instance = null;

    public static string $version = '1.0.0';

    public string $directory_path;
    public string $directory_url;

    public static function get_instance(): Plugin
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->directory_path = APOLLO_ADVERTS_DIR;
        $this->directory_url  = APOLLO_ADVERTS_URL;
    }

    /**
     * Initialize plugin
     * Adapted from WPAdverts init hooks
     */
    public function init(): void
    {
        // Register scripts and styles
        add_action('init', array($this, 'register_scripts_and_styles'));
        add_action('init', array($this, 'register_image_sizes'));
        add_action('init', array($this, 'register_rewrite_rules'), 1);

        // Admin menu
        add_action('admin_menu', array($this, 'admin_menu'));

        // Admin scripts
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

        // Frontend scripts
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));

        // REST API
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // Admin columns for classified CPT
        add_filter('manage_' . APOLLO_CPT_CLASSIFIED . '_posts_columns', array($this, 'admin_columns'));
        add_action('manage_' . APOLLO_CPT_CLASSIFIED . '_posts_custom_column', array($this, 'admin_column_value'), 10, 2);

        // Save post meta from admin
        add_action('save_post_' . APOLLO_CPT_CLASSIFIED, array($this, 'save_meta_box'), 10, 2);

        // Meta box for classified data
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));

        // Single classified: increment views
        add_action('template_redirect', array($this, 'track_views'));

        // Initialize components (adapted from WPAdverts snippets).
        new ForceFeaturedImage();
        new LimitActiveListings();
        new RelatedAds();
        new SpamProtection();
        new FrontendForm();

        // Virtual pages (create classified)
        add_filter(
            'query_vars',
            function ($vars) {
                $vars[] = 'apollo_adverts_page';
                return $vars;
            }
        );
        add_action('template_redirect', array($this, 'handle_virtual_pages'), 5);
    }

    /**
     * Register rewrite rules for /novo-anuncio
     */
    public function register_rewrite_rules(): void
    {
        add_rewrite_rule('^novo-anuncio/?$', 'index.php?apollo_adverts_page=create', 'top');
        add_rewrite_rule('^criar-anuncio/?$', 'index.php?apollo_adverts_page=create', 'top');
    }

    /**
     * Handle virtual pages (create classified)
     */
    public function handle_virtual_pages(): void
    {
        $page = get_query_var('apollo_adverts_page');
        if (! $page) {
            return;
        }
        if (! is_user_logged_in()) {
            wp_redirect(home_url('/acesso'));
            exit;
        }
        if ($page === 'create') {
            $template = $this->directory_path . 'templates/form.php';
            $this->render_blank_canvas($template);
        }
    }

    /**
     * Register scripts and styles
     * Adapted from WPAdverts register_scripts_and_styles()
     */
    public function register_scripts_and_styles(): void
    {
        $v = self::$version;

        // Admin
        wp_register_script('apollo-adverts-admin', $this->directory_url . 'assets/js/admin.js', array('jquery'), $v, true);
        wp_register_style('apollo-adverts-admin', $this->directory_url . 'assets/css/admin.css', array(), $v);

        // Frontend
        wp_register_script('apollo-adverts', $this->directory_url . 'assets/js/classifieds.js', array('jquery'), $v, true);
        wp_register_script('apollo-adverts-gallery', $this->directory_url . 'assets/js/gallery.js', array('jquery', 'plupload-all'), $v, true);

        $front_css = file_exists(get_stylesheet_directory() . '/apollo-adverts.css')
            ? get_stylesheet_directory_uri() . '/apollo-adverts.css'
            : $this->directory_url . 'assets/css/classifieds.css';
        wp_register_style('apollo-adverts', $front_css, array(), $v);

        // Localize admin JS
        wp_localize_script(
            'apollo-adverts-admin',
            'apolloAdvertsAdmin',
            array(
                'ajax_url'   => admin_url('admin-ajax.php'),
                'rest_url'   => esc_url_raw(rest_url(APOLLO_ADVERTS_REST_NAMESPACE . '/')),
                'rest_nonce' => wp_create_nonce('wp_rest'),
                'nonce'      => wp_create_nonce('apollo_adverts_admin'),
            )
        );

        // Localize frontend JS
        wp_localize_script(
            'apollo-adverts',
            'apolloAdvertsData',
            array(
                'ajax_url'   => admin_url('admin-ajax.php'),
                'rest_url'   => esc_url_raw(rest_url(APOLLO_ADVERTS_REST_NAMESPACE . '/')),
                'nonce'      => wp_create_nonce('apollo_adverts_front'),
                'rest_nonce' => wp_create_nonce('wp_rest'),
                'max_images' => APOLLO_ADVERTS_MAX_IMAGES,
                'i18n'       => array(
                    'confirm_delete' => __('Tem certeza que deseja excluir?', 'apollo-adverts'),
                    'uploading'      => __('Enviando...', 'apollo-adverts'),
                    'upload_error'   => __('Erro no upload', 'apollo-adverts'),
                ),
            )
        );
    }

    /**
     * Register image sizes
     * Adapted from WPAdverts add_image_size calls
     */
    public function register_image_sizes(): void
    {
        foreach (APOLLO_ADVERTS_IMAGE_SIZES as $name => $size) {
            add_image_size($name, $size['width'], $size['height'], $size['crop']);
        }
    }

    /**
     * Admin menu
     * Adapted from WPAdverts admin_menu
     */
    public function admin_menu(): void
    {
        $cap = 'manage_options';

        add_submenu_page(
            'edit.php?post_type=' . APOLLO_CPT_CLASSIFIED,
            __('Configurações', 'apollo-adverts'),
            __('Configurações', 'apollo-adverts'),
            $cap,
            'apollo-adverts-settings',
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            'edit.php?post_type=' . APOLLO_CPT_CLASSIFIED,
            __('Dashboard', 'apollo-adverts'),
            __('Dashboard', 'apollo-adverts'),
            $cap,
            'apollo-adverts-dashboard',
            array($this, 'render_dashboard_page')
        );
    }

    /**
     * Admin scripts
     */
    public function admin_scripts(): void
    {
        $screen = get_current_screen();
        if (! $screen) {
            return;
        }

        if ($screen->post_type === APOLLO_CPT_CLASSIFIED || strpos($screen->id, 'apollo-adverts') !== false) {
            wp_enqueue_script('apollo-adverts-admin');
            wp_enqueue_style('apollo-adverts-admin');
        }
    }

    /**
     * Frontend scripts
     */
    public function frontend_scripts(): void
    {
        // Enqueued on-demand by shortcodes (not globally)
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes(): void
    {
        if (class_exists('\Apollo\Adverts\API\ClassifiedsController')) {
            (new API\ClassifiedsController())->register_routes();
        }
        if (class_exists('\Apollo\Adverts\API\SearchController')) {
            (new API\SearchController())->register_routes();
        }
    }

    /**
     * Add admin columns
     * Adapted from WPAdverts admin-post-type.php
     */
    public function admin_columns(array $columns): array
    {
        $new = array();
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ($key === 'title') {
                $new['classified_price']   = __('Valor Ref.', 'apollo-adverts');
                $new['classified_intent']  = __('Intenção', 'apollo-adverts');
                $new['classified_expires'] = __('Expira', 'apollo-adverts');
            }
        }
        return $new;
    }

    /**
     * Render admin column values
     */
    public function admin_column_value(string $column, int $post_id): void
    {
        switch ($column) {
            case 'classified_price':
                $price = apollo_adverts_get_the_price($post_id);
                echo $price ? esc_html($price) : '—';
                break;
            case 'classified_intent':
                echo esc_html(apollo_adverts_get_intent_label($post_id));
                break;
            case 'classified_expires':
                $exp = get_post_meta($post_id, '_classified_expires_at', true);
                if ($exp) {
                    $is_expired = apollo_adverts_is_expired($post_id);
                    $color      = $is_expired ? 'color:#d63638' : '';
                    printf('<span style="%s">%s</span>', esc_attr($color), esc_html($exp));
                } else {
                    echo '—';
                }
                break;
        }
    }

    /**
     * Add meta boxes
     * Adapted from WPAdverts adverts_data_box
     */
    public function add_meta_boxes(): void
    {
        add_meta_box(
            'apollo_classified_data',
            __('Dados do Anúncio', 'apollo-adverts'),
            array($this, 'render_meta_box'),
            APOLLO_CPT_CLASSIFIED,
            'normal',
            'high'
        );
    }

    /**
     * Render classified data meta box
     * Adapted from WPAdverts admin meta box
     */
    public function render_meta_box(\WP_Post $post): void
    {
        wp_nonce_field('apollo_adverts_save_meta', 'apollo_adverts_meta_nonce');

        $meta_keys  = APOLLO_ADVERTS_META_KEYS;
        $conditions = APOLLO_ADVERTS_CONDITIONS;
        $intents    = APOLLO_ADVERTS_INTENTS;

        echo '<table class="form-table">';

        // Reference value (informational only)
        $price = get_post_meta($post->ID, '_classified_price', true);
        printf(
            '<tr><th><label for="_classified_price">%s</label></th><td><input type="text" name="_classified_price" id="_classified_price" value="%s" class="regular-text" /><p class="description">%s</p></td></tr>',
            esc_html__('Valor de Referência (R$)', 'apollo-adverts'),
            esc_attr($price),
            esc_html__('Apenas informativo. Apollo conecta pessoas — não processa transações.', 'apollo-adverts')
        );

        // Currency
        $currency   = get_post_meta($post->ID, '_classified_currency', true) ?: 'BRL';
        $currencies = array(
            'BRL' => 'R$ — Real',
            'USD' => '$ — Dólar',
            'EUR' => '€ — Euro',
        );
        echo '<tr><th><label for="_classified_currency">' . esc_html__('Moeda', 'apollo-adverts') . '</label></th><td><select name="_classified_currency" id="_classified_currency">';
        foreach ($currencies as $val => $label) {
            printf('<option value="%s" %s>%s</option>', esc_attr($val), selected($currency, $val, false), esc_html($label));
        }
        echo '</select></td></tr>';

        // Negotiable
        $negotiable = get_post_meta($post->ID, '_classified_negotiable', true);
        printf(
            '<tr><th><label for="_classified_negotiable">%s</label></th><td><input type="checkbox" name="_classified_negotiable" id="_classified_negotiable" value="1" %s /></td></tr>',
            esc_html__('Negociável', 'apollo-adverts'),
            checked($negotiable, '1', false)
        );

        // Condition
        $condition = get_post_meta($post->ID, '_classified_condition', true);
        echo '<tr><th><label for="_classified_condition">' . esc_html__('Condição', 'apollo-adverts') . '</label></th><td><select name="_classified_condition" id="_classified_condition">';
        foreach ($conditions as $val => $label) {
            printf('<option value="%s" %s>%s</option>', esc_attr($val), selected($condition, $val, false), esc_html($label));
        }
        echo '</select></td></tr>';

        // Location
        $location = get_post_meta($post->ID, '_classified_location', true);
        printf(
            '<tr><th><label for="_classified_location">%s</label></th><td><input type="text" name="_classified_location" id="_classified_location" value="%s" class="regular-text" /></td></tr>',
            esc_html__('Localização', 'apollo-adverts'),
            esc_attr($location)
        );

        // Phone
        $phone = get_post_meta($post->ID, '_classified_contact_phone', true);
        printf(
            '<tr><th><label for="_classified_contact_phone">%s</label></th><td><input type="text" name="_classified_contact_phone" id="_classified_contact_phone" value="%s" class="regular-text" /></td></tr>',
            esc_html__('Telefone', 'apollo-adverts'),
            esc_attr($phone)
        );

        // WhatsApp
        $whatsapp = get_post_meta($post->ID, '_classified_contact_whatsapp', true);
        printf(
            '<tr><th><label for="_classified_contact_whatsapp">%s</label></th><td><input type="text" name="_classified_contact_whatsapp" id="_classified_contact_whatsapp" value="%s" class="regular-text" /></td></tr>',
            esc_html__('WhatsApp', 'apollo-adverts'),
            esc_attr($whatsapp)
        );

        // Expires at
        $expires = get_post_meta($post->ID, '_classified_expires_at', true);
        printf(
            '<tr><th><label for="_classified_expires_at">%s</label></th><td><input type="date" name="_classified_expires_at" id="_classified_expires_at" value="%s" /></td></tr>',
            esc_html__('Expira em', 'apollo-adverts'),
            esc_attr($expires)
        );

        // Featured
        $featured = get_post_meta($post->ID, '_classified_featured', true);
        printf(
            '<tr><th><label for="_classified_featured">%s</label></th><td><input type="checkbox" name="_classified_featured" id="_classified_featured" value="1" %s /></td></tr>',
            esc_html__('Destaque', 'apollo-adverts'),
            checked($featured, '1', false)
        );

        echo '</table>';
    }

    /**
     * Save meta box data
     * Adapted from WPAdverts save_post handler
     */
    public function save_meta_box(int $post_id, \WP_Post $post): void
    {
        if (! isset($_POST['apollo_adverts_meta_nonce']) || ! wp_verify_nonce($_POST['apollo_adverts_meta_nonce'], 'apollo_adverts_save_meta')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        $text_fields = array('_classified_location', '_classified_contact_phone', '_classified_contact_whatsapp', '_classified_expires_at', '_classified_condition');
        foreach ($text_fields as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, sanitize_text_field(wp_unslash($_POST[$key])));
            }
        }

        // Price
        if (isset($_POST['_classified_price'])) {
            $price = str_replace(array('.', ','), array('', '.'), sanitize_text_field(wp_unslash($_POST['_classified_price'])));
            update_post_meta($post_id, '_classified_price', (float) $price);
        }

        // Currency
        if (isset($_POST['_classified_currency'])) {
            $currency = sanitize_text_field(wp_unslash($_POST['_classified_currency']));
            if (in_array($currency, array('BRL', 'USD', 'EUR'), true)) {
                update_post_meta($post_id, '_classified_currency', $currency);
            }
        }

        // Checkboxes
        update_post_meta($post_id, '_classified_negotiable', isset($_POST['_classified_negotiable']) ? '1' : '');
        update_post_meta($post_id, '_classified_featured', isset($_POST['_classified_featured']) ? '1' : '');
    }

    /**
     * Track views on single classified
     */
    public function track_views(): void
    {
        if (is_singular(APOLLO_CPT_CLASSIFIED) && ! is_admin()) {
            $post_id = get_queried_object_id();
            if ($post_id && get_post_type($post_id) === APOLLO_CPT_CLASSIFIED) {
                apollo_adverts_increment_views($post_id);
            }
        }
    }

    /**
     * Render settings page
     */
    public function render_settings_page(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        include APOLLO_ADVERTS_DIR . 'templates/admin/settings.php';
    }

    /**
     * Render dashboard page
     */
    public function render_dashboard_page(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        include APOLLO_ADVERTS_DIR . 'templates/admin/dashboard.php';
    }
}
