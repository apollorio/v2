<?php

/**
 * Registry — CPT "local", taxonomies, meta keys, metaboxes, admin columns
 *
 * CPT slug = "local", rewrite = "gps", archive = "gps", rest_base = "locals"
 * Taxonomies: local_type, local_area
 *
 * @package Apollo\Local
 */

declare(strict_types=1);

namespace Apollo\Local;

if (! defined('ABSPATH')) {
    exit;
}

class Registry
{

    public function __construct()
    {
        add_action('init', array($this, 'register_cpt'), 5);
        add_filter('apollo_core_register_meta', array($this, 'register_meta'));
        add_action('add_meta_boxes', array($this, 'add_metaboxes'));
        add_action('save_post_' . APOLLO_LOCAL_CPT, array($this, 'save_metabox'), 10, 2);
        add_filter('manage_' . APOLLO_LOCAL_CPT . '_posts_columns', array($this, 'admin_columns'));
        add_action('manage_' . APOLLO_LOCAL_CPT . '_posts_custom_column', array($this, 'admin_column_content'), 10, 2);
    }

    /**
     * Registra CPT "loc" — fallback se apollo-core não registrou
     */
    public function register_cpt(): void
    {
        if (post_type_exists(APOLLO_LOCAL_CPT)) {
            $this->register_taxonomies_fallback();
            return;
        }

        $labels = array(
            'name'               => __('Locais', 'apollo-local'),
            'singular_name'      => __('Local', 'apollo-local'),
            'add_new'            => __('Novo Local', 'apollo-local'),
            'add_new_item'       => __('Adicionar Novo Local', 'apollo-local'),
            'edit_item'          => __('Editar Local', 'apollo-local'),
            'new_item'           => __('Novo Local', 'apollo-local'),
            'view_item'          => __('Ver Local', 'apollo-local'),
            'search_items'       => __('Buscar Locais', 'apollo-local'),
            'not_found'          => __('Nenhum local encontrado', 'apollo-local'),
            'not_found_in_trash' => __('Nenhum local na lixeira', 'apollo-local'),
        );

        register_post_type(
            APOLLO_LOCAL_CPT,
            array(
                'labels'              => $labels,
                'public'              => true,
                'has_archive'         => 'local',
                'rewrite'             => array(
                    'slug'       => 'local',
                    'with_front' => false,
                ),
                'rest_base'           => 'local',
                'show_in_rest'        => true,
                'supports'            => array('title', 'editor', 'thumbnail'),
                'menu_icon'           => 'dashicons-location',
                'menu_position'       => 8,
                'taxonomies'          => array(APOLLO_LOCAL_TAX_TYPE, APOLLO_LOCAL_TAX_AREA),
                'capability_type'     => 'post',
                'map_meta_cap'        => true,
                'show_in_admin_bar'   => true,
                'exclude_from_search' => false,
            )
        );

        $this->register_taxonomies_fallback();
    }

    /**
     * Registra taxonomias com fallback — conforme apollo-registry.json
     */
    private function register_taxonomies_fallback(): void
    {
        // local_type
        if (! taxonomy_exists(APOLLO_LOCAL_TAX_TYPE)) {
            register_taxonomy(
                APOLLO_LOCAL_TAX_TYPE,
                APOLLO_LOCAL_CPT,
                array(
                    'labels'       => array(
                        'name'          => 'Tipos de Local',
                        'singular_name' => 'Tipo de Local',
                    ),
                    'hierarchical' => true,
                    'public'       => true,
                    'show_in_rest' => true,
                    'rewrite'      => array('slug' => 'tipo-local'),
                )
            );
        }

        // local_area
        if (! taxonomy_exists(APOLLO_LOCAL_TAX_AREA)) {
            register_taxonomy(
                APOLLO_LOCAL_TAX_AREA,
                APOLLO_LOCAL_CPT,
                array(
                    'labels'       => array(
                        'name'          => 'Zonas',
                        'singular_name' => 'Zona',
                    ),
                    'hierarchical' => true,
                    'public'       => true,
                    'show_in_rest' => true,
                    'rewrite'      => array('slug' => 'zona'),
                )
            );
        }
    }

    /**
     * Meta keys via apollo-core — conforme apollo-registry.json
     */
    public function register_meta(array $meta_config): array
    {
        $meta_config['local'] = array(
            '_local_name'        => array(
                'type'     => 'string',
                'sanitize' => 'sanitize_text_field',
            ),
            '_local_address'     => array(
                'type'     => 'string',
                'sanitize' => 'sanitize_text_field',
            ),
            '_local_city'        => array(
                'type'     => 'string',
                'sanitize' => 'sanitize_text_field',
            ),
            '_local_state'       => array(
                'type'     => 'string',
                'sanitize' => 'sanitize_text_field',
            ),
            '_local_country'     => array(
                'type'     => 'string',
                'sanitize' => 'sanitize_text_field',
            ),
            '_local_postal'      => array(
                'type'     => 'string',
                'sanitize' => 'sanitize_text_field',
            ),
            '_local_lat'         => array(
                'type'     => 'number',
                'sanitize' => 'floatval',
            ),
            '_local_lng'         => array(
                'type'     => 'number',
                'sanitize' => 'floatval',
            ),
            '_local_phone'       => array(
                'type'     => 'string',
                'sanitize' => 'sanitize_text_field',
            ),
            '_local_website'     => array(
                'type'     => 'string',
                'sanitize' => 'esc_url_raw',
            ),
            '_local_instagram'   => array(
                'type'     => 'string',
                'sanitize' => 'sanitize_text_field',
            ),
            '_local_capacity'    => array(
                'type'     => 'integer',
                'sanitize' => 'absint',
            ),
            '_local_price_range' => array(
                'type'     => 'string',
                'sanitize' => 'sanitize_text_field',
            ),
        );

        return $meta_config;
    }

    /**
     * Metabox
     */
    public function add_metaboxes(): void
    {
        add_meta_box(
            'apollo_local_details',
            __('Detalhes do Local', 'apollo-local'),
            array($this, 'render_metabox'),
            APOLLO_LOCAL_CPT,
            'normal',
            'high'
        );
    }

    /**
     * Renderiza metabox
     */
    public function render_metabox(\WP_Post $post): void
    {
        wp_nonce_field('apollo_local_metabox', 'apollo_local_nonce');

        $fields = array();
        foreach (APOLLO_LOCAL_META_KEYS as $key) {
            $fields[$key] = get_post_meta($post->ID, $key, true);
        }
?>
        <style>
            .apollo-local-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }

            .apollo-local-field {
                margin-bottom: 12px;
            }

            .apollo-local-field label {
                display: block;
                font-weight: 600;
                margin-bottom: 4px;
                font-size: 13px;
            }

            .apollo-local-field input,
            .apollo-local-field select {
                width: 100%;
                padding: 6px 8px;
            }

            .apollo-local-full {
                grid-column: 1 / -1;
            }
        </style>
        <div class="apollo-local-grid">
            <div class="apollo-local-field apollo-local-full">
                <label for="apollo_local_name"><?php esc_html_e('Nome alternativo', 'apollo-local'); ?></label>
                <input type="text" id="apollo_local_name" name="_local_name" value="<?php echo esc_attr($fields['_local_name']); ?>" placeholder="<?php esc_attr_e('Se diferente do título', 'apollo-local'); ?>">
            </div>
            <div class="apollo-local-field apollo-local-full">
                <label for="apollo_local_address"><?php esc_html_e('Endereço completo', 'apollo-local'); ?></label>
                <input type="text" id="apollo_local_address" name="_local_address" value="<?php echo esc_attr($fields['_local_address']); ?>">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_city"><?php esc_html_e('Cidade', 'apollo-local'); ?></label>
                <input type="text" id="apollo_local_city" name="_local_city" value="<?php echo esc_attr($fields['_local_city']); ?>">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_state"><?php esc_html_e('Estado', 'apollo-local'); ?></label>
                <input type="text" id="apollo_local_state" name="_local_state" value="<?php echo esc_attr($fields['_local_state']); ?>">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_country"><?php esc_html_e('País', 'apollo-local'); ?></label>
                <input type="text" id="apollo_local_country" name="_local_country" value="<?php echo esc_attr($fields['_local_country'] ?: 'Brasil'); ?>">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_postal"><?php esc_html_e('CEP', 'apollo-local'); ?></label>
                <input type="text" id="apollo_local_postal" name="_local_postal" value="<?php echo esc_attr($fields['_local_postal']); ?>" placeholder="00000-000">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_lat"><?php esc_html_e('Latitude', 'apollo-local'); ?></label>
                <input type="text" id="apollo_local_lat" name="_local_lat" value="<?php echo esc_attr($fields['_local_lat']); ?>" placeholder="-22.9068">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_lng"><?php esc_html_e('Longitude', 'apollo-local'); ?></label>
                <input type="text" id="apollo_local_lng" name="_local_lng" value="<?php echo esc_attr($fields['_local_lng']); ?>" placeholder="-43.1729">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_phone"><?php esc_html_e('Telefone', 'apollo-local'); ?></label>
                <input type="tel" id="apollo_local_phone" name="_local_phone" value="<?php echo esc_attr($fields['_local_phone']); ?>">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_capacity"><?php esc_html_e('Capacidade', 'apollo-local'); ?></label>
                <input type="number" id="apollo_local_capacity" name="_local_capacity" value="<?php echo esc_attr($fields['_local_capacity']); ?>" min="0">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_website"><?php esc_html_e('Website', 'apollo-local'); ?></label>
                <input type="url" id="apollo_local_website" name="_local_website" value="<?php echo esc_url($fields['_local_website']); ?>">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_instagram"><?php esc_html_e('Instagram', 'apollo-local'); ?></label>
                <input type="text" id="apollo_local_instagram" name="_local_instagram" value="<?php echo esc_attr($fields['_local_instagram']); ?>" placeholder="https://instagram.com/...">
            </div>
            <div class="apollo-local-field">
                <label for="apollo_local_price_range"><?php esc_html_e('Faixa de preço', 'apollo-local'); ?></label>
                <select id="apollo_local_price_range" name="_local_price_range">
                    <option value=""><?php esc_html_e('— Selecione —', 'apollo-local'); ?></option>
                    <option value="$" <?php selected($fields['_local_price_range'], '$'); ?>>$ (Econômico)</option>
                    <option value="$$" <?php selected($fields['_local_price_range'], '$$'); ?>>$$ (Moderado)</option>
                    <option value="$$$" <?php selected($fields['_local_price_range'], '$$$'); ?>>$$$ (Caro)</option>
                    <option value="$$$$" <?php selected($fields['_local_price_range'], '$$$$'); ?>>$$$$ (Luxo)</option>
                </select>
            </div>
        </div>
<?php
    }

    /**
     * Salva metabox
     */
    public function save_metabox(int $post_id, \WP_Post $post): void
    {
        if (! isset($_POST['apollo_local_nonce']) || ! wp_verify_nonce($_POST['apollo_local_nonce'], 'apollo_local_metabox')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        $text_fields = array('_local_name', '_local_address', '_local_city', '_local_state', '_local_country', '_local_postal', '_local_phone', '_local_instagram', '_local_price_range');
        $url_fields  = array('_local_website');
        $num_fields  = array('_local_lat', '_local_lng');
        $int_fields  = array('_local_capacity');

        foreach ($text_fields as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, sanitize_text_field(wp_unslash($_POST[$key])));
            }
        }

        foreach ($url_fields as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, esc_url_raw(wp_unslash($_POST[$key])));
            }
        }

        foreach ($num_fields as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, (float) $_POST[$key]);
            }
        }

        foreach ($int_fields as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, absint($_POST[$key]));
            }
        }
    }

    /**
     * Colunas admin
     */
    public function admin_columns(array $columns): array
    {
        $new = array();
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ('title' === $key) {
                $new['local_city']   = __('Cidade', 'apollo-local');
                $new['local_type']   = __('Tipo', 'apollo-local');
                $new['local_events'] = __('Eventos', 'apollo-local');
                $new['local_coords'] = __('Coords', 'apollo-local');
            }
        }
        return $new;
    }

    /**
     * Conteúdo das colunas
     */
    public function admin_column_content(string $column, int $post_id): void
    {
        switch ($column) {
            case 'local_city':
                echo esc_html(get_post_meta($post_id, '_local_city', true) ?: '—');
                break;

            case 'local_type':
                $types = apollo_local_get_types($post_id);
                echo esc_html(implode(', ', $types) ?: '—');
                break;

            case 'local_events':
                echo esc_html(apollo_local_count_upcoming_events($post_id));
                break;

            case 'local_coords':
                $coords = apollo_local_get_coords($post_id);
                if ($coords) {
                    printf('%.4f, %.4f', $coords['lat'], $coords['lng']);
                } else {
                    echo '—';
                }
                break;
        }
    }
}
