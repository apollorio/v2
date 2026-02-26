<?php

/**
 * Apollo Registry
 *
 * Loads and provides access to apollo-registry.json
 *
 * @package Apollo\Core
 * @since 6.0.0
 */

namespace Apollo\Core;

if (! defined('ABSPATH')) {
    exit;
}

class Registry
{

    private static ?array $registry  = null;
    private static string $cache_key = 'apollo_registry_data';
    private static int $cache_ttl    = 3600;

    public static function init(): void
    {
        self::load_registry();
    }

    private static function load_registry(): void
    {
        // Try cache first
        $cached = wp_cache_get(self::$cache_key, 'apollo');
        if ($cached !== false) {
            self::$registry = $cached;
            return;
        }

        // Load from file
        $registry_path = defined('APOLLO_REGISTRY_PATH')
            ? APOLLO_REGISTRY_PATH
            : WP_CONTENT_DIR . '/plugins/_inventory/apollo-registry.json';

        if (! file_exists($registry_path)) {
            self::$registry = array();
            return;
        }

        $json = file_get_contents($registry_path);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::$registry = array();
            return;
        }

        self::$registry = apply_filters('apollo/registry/data', $data);
        wp_cache_set(self::$cache_key, self::$registry, 'apollo', self::$cache_ttl);
    }

    public static function get_registry(): array
    {
        if (self::$registry === null) {
            self::load_registry();
        }
        return self::$registry ?? array();
    }

    public static function get_plugin(string $slug): ?array
    {
        $registry = self::get_registry();
        return $registry['plugins'][$slug] ?? null;
    }

    public static function get_cdn_config(): array
    {
        $registry = self::get_registry();
        return $registry['cdn'] ?? array();
    }

    public static function clear_cache(): void
    {
        wp_cache_delete(self::$cache_key, 'apollo');
        self::$registry = null;
    }
}
