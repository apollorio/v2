<?php

/**
 * Apollo Templates - Page Creation & Rewrite Setup
 *
 * Creates required pages with specific templates.
 * Handles /classificados route mapping.
 *
 * @package Apollo\Templates
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Create /classificados page on activation
 */
function apollo_templates_create_pages(): void
{

    // Classificados page
    $classificados_page = get_page_by_path('classificados');

    if (! $classificados_page) {
        $page_id = wp_insert_post(
            array(
                'post_title'     => 'Classificados',
                'post_name'      => 'classificados',
                'post_content'   => '',
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => 1,
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
            )
        );

        if ($page_id && ! is_wp_error($page_id)) {
            // Set page template
            update_post_meta($page_id, '_wp_page_template', 'page-classificados.php');
        }
    }
}

/**
 * Add rewrite rules for custom pages
 */
function apollo_templates_add_rewrite_rules(): void
{
    // /classificados → page-classificados.php
    add_rewrite_rule('^classificados/?$', 'index.php?pagename=classificados', 'top');

    // /classificados/novo → creation form (future)
    add_rewrite_rule('^classificados/novo/?$', 'index.php?pagename=classificados&action=new', 'top');

    // /home → page-home.php (guest) | page-mural.php (logged-in)
    add_rewrite_rule('^home/?$', 'index.php?apollo_home_page=1', 'top');

    // /sobre → page-sobre.php (institutional / old home page)
    add_rewrite_rule('^sobre/?$', 'index.php?apollo_sobre_page=1', 'top');

    // /about-us → redirect to /sobre
    add_rewrite_rule('^about-us/?$', 'index.php?apollo_about_redirect=1', 'top');

    // /test → page-test.php (routes spreadsheet, admin only)
    add_rewrite_rule('^test/?$', 'index.php?apollo_test_page=1', 'top');
}
add_action('init', 'apollo_templates_add_rewrite_rules', 10);

// Register query vars for custom routes
add_filter(
    'query_vars',
    function (array $vars): array {
        $vars[] = 'apollo_home_page';
        $vars[] = 'apollo_test_page';
        $vars[] = 'apollo_sobre_page';
        $vars[] = 'apollo_about_redirect';
        return $vars;
    }
);

/**
 * Fallback: Intercept custom routes via parse_request if rewrite rules fail (nginx compat)
 */
function apollo_templates_parse_request(\WP $wp): void
{
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    if ($path === 'home') {
        $wp->query_vars['apollo_home_page'] = '1';
    }

    if ($path === 'test') {
        $wp->query_vars['apollo_test_page'] = '1';
    }

    if ($path === 'sobre') {
        $wp->query_vars['apollo_sobre_page'] = '1';
    }

    if ($path === 'about-us') {
        $wp->query_vars['apollo_about_redirect'] = '1';
    }
}
add_action('parse_request', 'apollo_templates_parse_request', 1);

/**
 * Template loader for custom pages
 */
function apollo_templates_load_template($template): string
{
    global $wp_query;

    // /home → page-home.php (guest) | page-mural.php (logged-in)
    if (get_query_var('apollo_home_page')) {
        $wp_query->is_404  = false;
        $wp_query->is_home = false;
        status_header(200);

        if (is_user_logged_in()) {
            $mural = APOLLO_TEMPLATES_DIR . 'templates/page-mural.php';
            if (file_exists($mural)) {
                return $mural;
            }
        }

        $home = APOLLO_TEMPLATES_DIR . 'templates/page-home.php';
        if (file_exists($home)) {
            return $home;
        }
    }

    // /about-us → 301 redirect to /sobre
    if (get_query_var('apollo_about_redirect')) {
        wp_safe_redirect(home_url('/sobre'), 301);
        exit;
    }

    // /sobre → page-sobre.php (institutional / old home)
    if (get_query_var('apollo_sobre_page')) {
        $sobre_template = APOLLO_TEMPLATES_DIR . 'templates/page-sobre.php';
        if (file_exists($sobre_template)) {
            $wp_query->is_404  = false;
            $wp_query->is_home = false;
            status_header(200);
            return $sobre_template;
        }
    }

    // /test → page-test.php (admin spreadsheet)
    if (get_query_var('apollo_test_page')) {
        $test_template = APOLLO_TEMPLATES_DIR . 'templates/page-test.php';
        if (file_exists($test_template)) {
            // Prevent 404
            $wp_query->is_404  = false;
            $wp_query->is_home = false;
            status_header(200);
            return $test_template;
        }
    }

    // Check if page exists
    if (is_page()) {
        $page_template = get_page_template_slug();

        // Classificados
        if ($page_template === 'page-classificados.php' || is_page('classificados')) {
            $custom_template = APOLLO_TEMPLATES_DIR . 'templates/page-classificados.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
    }

    return $template;
}
add_filter('template_include', 'apollo_templates_load_template', 99);

/**
 * AJAX: Save test spreadsheet state
 */
function apollo_save_test_spreadsheet(): void
{
    check_ajax_referer('apollo_test_spreadsheet', 'nonce');

    if (! current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    $state = json_decode(stripslashes($_POST['state'] ?? '{}'), true);

    if (! is_array($state)) {
        wp_send_json_error('Invalid data');
    }

    // Sanitize
    $clean = array();
    foreach ($state as $key => $data) {
        $clean[sanitize_key($key)] = array(
            'checked'         => ! empty($data['checked']),
            'comment'         => sanitize_textarea_field($data['comment'] ?? ''),
            'comment_checked' => ! empty($data['comment_checked']),
            'done'            => ! empty($data['done']),
        );
    }

    update_option('apollo_test_spreadsheet', $clean, false);
    wp_send_json_success();
}
add_action('wp_ajax_apollo_save_test_spreadsheet', 'apollo_save_test_spreadsheet');

/**
 * Flush rewrite rules on activation
 */
function apollo_templates_activate_pages(): void
{
    apollo_templates_create_pages();
    apollo_templates_add_rewrite_rules();
    flush_rewrite_rules();
}

/**
 * Run on plugin init
 */
add_action(
    'apollo/templates/initialized',
    function () {
        // Check if pages exist
        $classificados = get_page_by_path('classificados');
        if (! $classificados) {
            apollo_templates_create_pages();
            flush_rewrite_rules();
        }
    },
    10
);

/**
 * Flush rewrite rules once when the /home route is missing from the ruleset.
 * Runs on admin_init to avoid performance impact on frontend requests.
 * Self-clears after one flush via transient.
 */
add_action(
    'admin_init',
    function (): void {
        if (get_transient('apollo_home_rewrite_flushed')) {
            return;
        }

        $rules = get_option('rewrite_rules', array());

        // Check if our /home rule is registered
        if (! isset($rules['home/?$']) && ! isset($rules['^home/?$'])) {
            flush_rewrite_rules(false);
        }

        // Mark as done regardless — prevents repeat checks
        set_transient('apollo_home_rewrite_flushed', '1', WEEK_IN_SECONDS);
    }
);
