<?php

/**
 * Apollo CDN
 *
 * Guarantees apollo CDN script from cdn.apollo.rio.br loads on EVERY
 * WordPress frontend page — via direct wp_head echo at priority 1.
 *
 * CDN core.js loads (in order):
 *   - Base Stylesheet (injected inline before </head>)
 *   - GSAP 3.14+ (ScrollTrigger, Observer, Morphism, etc.)
 *   - RemixIcon (all ri-* icons)
 *   - jQuery v4
 *   - Apollo Logo Morphing (<i class="apollo">)
 *   - Dark Theme, Reveal-Up, Translate i18n, ApolloTrack analytics
 *
 * RULES:
 *   - ALWAYS via https://cdn.apollo.rio.br/* — NEVER local file
 *   - Direct <script> echo in wp_head (priority 1) — not wp_enqueue_script
 *     so it cannot be blocked, dequeued or deferred by themes/plugins
 *   - wp_enqueue_script kept for WP script dependency graph only
 *   - No admin injection
 *
 * @package Apollo\Core
 * @since   6.0.0
 * @since   6.2.0  Direct wp_head echo — guaranteed injection on all pages
 */

namespace Apollo\Core;

if (! defined('ABSPATH')) {
    exit;
}

class CDN
{

    /** CDN base URL — always https://cdn.apollo.rio.br/* */
    const CDN_BASE = 'https://cdn.apollo.rio.br/v1.0.0/';

    /** Singleton flag — prevents double output even if init() called twice */
    private static bool $injected = false;

    public static function init(): void
    {
        if (is_admin()) {
            // Admin: only dequeue WP native fonts so admin stays independent
            add_action('admin_enqueue_scripts', array(self::class, 'dequeue_wp_fonts'), 5);
            return;
        }

        // ── PRIORITY 1: Direct echo in <head> ────────────────────────────
        // This fires BEFORE any theme or plugin can interfere.
        // It does NOT go through the wp_enqueue_script queue — immune to dequeue.
        add_action('wp_head', array(self::class, 'inject_script_tag'), 1);

        // ── WP Script registration (for dependency graph, no actual output) ─
        // Register the handle so other plugins can declare it as a dependency.
        add_action('wp_enqueue_scripts', array(self::class, 'register_cdn'), 1);

        // ── Inline config (apolloAnalytics JS global) ─────────────────────
        add_action('wp_head', array(self::class, 'inject_config'), 2);
    }

    /**
     * Direct <script> echo — runs in wp_head priority 1.
     * Guaranteed to appear in <head> on EVERY frontend page.
     */
    public static function inject_script_tag(): void
    {
        if (self::$injected) {
            return;
        }
        self::$injected = true;

        $url = self::CDN_BASE . 'core.js';
        // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
        echo '<script src="' . esc_url($url) . '" fetchpriority="high"></script>' . "\n";
    }

    /**
     * Inline apolloAnalytics config — runs in wp_head priority 2 (after script).
     */
    public static function inject_config(): void
    {
        $config = array(
            'siteId'  => get_current_blog_id(),
            'userId'  => get_current_user_id(),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('apollo/v1/'),
            'nonce'   => wp_create_nonce('apollo_nonce'),
            'version' => defined('APOLLO_VERSION') ? APOLLO_VERSION : '6.0.0',
        );
        echo '<script>window.apolloAnalytics=' . wp_json_encode($config) . ';</script>' . "\n";
    }

    /**
     * Register CDN handle in WP script queue (no enqueueing — already echoed).
     * This lets other plugins write: wp_enqueue_script('my-script', ..., ['apollo-cdn']).
     */
    public static function register_cdn(): void
    {
        $cdn_version = defined('APOLLO_CDN_VERSION') ? APOLLO_CDN_VERSION : '1.0.0';
        wp_register_script(
            'apollo-cdn',
            self::CDN_BASE . 'core.js',
            array(),
            $cdn_version,
            false
        );
        // Mark as done so WP doesn't output it a second time if something enqueues it
        wp_scripts()->done[] = 'apollo-cdn';
    }

    public static function dequeue_wp_fonts(): void
    {
        wp_dequeue_style('wp-fonts');
        wp_dequeue_style('wp-fonts-css');
    }
}
