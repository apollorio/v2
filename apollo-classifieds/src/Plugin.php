<?php

/**
 * Main Plugin Class
 *
 * @package Apollo\Classifieds
 */

namespace Apollo\Classifieds;

if (! defined('ABSPATH')) {
    exit;
}

class Plugin
{


    public function init()
    {
        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        // AJAX handlers
        add_action('wp_ajax_apollo_classifieds_init_chat', array($this, 'ajax_init_chat'));
        add_action('wp_ajax_nopriv_apollo_classifieds_init_chat', array($this, 'ajax_init_chat'));

        // CPT 'classified' is registered by apollo-core (fallback) or apollo-adverts (owner).
        // This plugin is a frontend complement only — no CPT registration needed.

        // Template redirect
        add_action('template_redirect', array($this, 'handle_template_redirect'));
    }

    /**
     * Enqueue CSS and JS
     */
    public function enqueue_assets()
    {
        // Only load on classifieds pages
        if (! is_post_type_archive('classified') && ! is_singular('classified') && ! is_page('marketplace')) {
            return;
        }

        // Apollo CDN — core.js bundles: CSS, GSAP 3.14.2, jQuery, Icons, page-layout
        // Registered by apollo-core CDN.php; relying on dependency graph via 'apollo-cdn' handle.
        // No separate GSAP enqueue needed — CDN already provides it.

        // Classifieds CSS
        wp_enqueue_style(
            'apollo-classifieds',
            APOLLO_CLASSIFIEDS_URL . 'assets/css/classifieds.css',
            array(),
            APOLLO_CLASSIFIEDS_VERSION
        );

        // Classifieds JS
        wp_enqueue_script(
            'apollo-classifieds',
            APOLLO_CLASSIFIEDS_URL . 'assets/js/classifieds.js',
            array('apollo-cdn'),
            APOLLO_CLASSIFIEDS_VERSION,
            true
        );

        // Localize script
        wp_localize_script(
            'apollo-classifieds',
            'apolloClassifieds',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('apollo_classifieds_nonce'),
            )
        );
    }

    /**
     * Serve custom template via template_redirect.
     */
    public function handle_template_redirect(): void
    {
        if (! is_page('marketplace') && ! is_post_type_archive('classified')) {
            return;
        }
        $tpl = APOLLO_CLASSIFIEDS_PATH . 'templates/classifieds-page.php';
        if (file_exists($tpl)) {
            include $tpl;
            exit;
        }
    }

    /**
     * AJAX: Initialize chat after disclaimer acceptance
     */
    public function ajax_init_chat()
    {
        check_ajax_referer('apollo_classifieds_nonce', 'nonce');

        if (! is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Você precisa estar logado.'));
        }

        $current_user_id = get_current_user_id();
        $target_user_id  = absint($_POST['user_id'] ?? 0);
        $classified_id   = absint($_POST['classified_id'] ?? 0);

        if (! $target_user_id || ! $classified_id) {
            wp_send_json_error(array('message' => 'Dados inválidos.'));
        }

        // Check if apollo-chat is active
        if (! function_exists('apollo_chat_create_thread')) {
            // Fallback: redirect to user profile
            $chat_url = home_url('/id/' . get_userdata($target_user_id)->user_login);
        } else {
            // Create chat thread
            $thread_id = apollo_chat_create_thread(
                $current_user_id,
                $target_user_id,
                array(
                    'context'    => 'classified',
                    'context_id' => $classified_id,
                )
            );

            $chat_url = home_url("/chat/{$thread_id}");
        }

        wp_send_json_success(array('chat_url' => $chat_url));
    }
}
