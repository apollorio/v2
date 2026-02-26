<?php

/**
 * Block apollo-templates from interfering with apollo-login pages
 *
 * @package Apollo\Login
 */

// Priority 999 = Run AFTER apollo-login registers its hooks, but block apollo-templates scripts
add_action(
    'wp_enqueue_scripts',
    function () {
        $page = get_query_var('apollo_login_page', '');

        if (! empty($page)) {
            // Remove apollo-templates scripts on apollo-login pages
            wp_dequeue_script('apollo-templates-auth');
            wp_dequeue_script('new_auth-scripts');
            wp_deregister_script('apollo-templates-auth');
            wp_deregister_script('new_auth-scripts');
        }
    },
    999
);

// Force apollo-login template priority
add_filter(
    'template_include',
    function ($template) {
        if (get_query_var('apollo_login_page')) {
            // This is apollo-login's territory - remove all other filters
            remove_all_filters('template_include', 10);
            remove_all_filters('template_include', 99);
            remove_all_filters('template_include', 100);
        }
        return $template;
    },
    0
); // Priority 0 = EARLIEST
