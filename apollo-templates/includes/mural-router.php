<?php

/**
 * Mural Router
 *
 * When a logged-in user visits the home page, serve page-mural.php
 * instead of page-home.php.
 *
 * Hook this file via:
 *   require_once APOLLO_TEMPLATES_DIR . 'includes/mural-router.php';
 *
 * @package Apollo\Templates
 * @since   1.1.0
 */

defined('ABSPATH') || exit;

/**
 * Intercept the template for the front page when user is logged in.
 *
 * Hooks into `template_include` at priority 99 (after theme/other plugins).
 */
add_filter(
    'template_include',
    function (string $template): string {

        // Match front page, blog index, OR WP page with slug 'home'
        if (! is_front_page() && ! is_home() && ! is_page('home')) {
            return $template;
        }

        // Only for logged-in users.
        if (! is_user_logged_in()) {
            return $template;
        }

        $mural_template = APOLLO_TEMPLATES_DIR . 'templates/page-mural.php';

        if (file_exists($mural_template)) {
            return $mural_template;
        }

        return $template;
    },
    99
);
