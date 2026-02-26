<?php

/**
 * Apollo Core
 *
 * MASTER FOUNDATION for the entire Apollo ecosystem.
 * Central registry for CPTs, Taxonomies, Meta Keys.
 * Fallback system ensures all components exist even if owner plugins are inactive.
 *
 * @license GPL-2.0-or-later
 * Copyright (c) 2026 Apollo
 *
 * @package Apollo\Core
 *
 * Plugin Name: Apollo Core
 * Plugin URI: https://apollo.rio.br
 * Description: Core fundacional do ecossistema Apollo - MASTER REGISTRY de CPTs, Taxonomias e Meta Keys. Sistema de fallback para plugins inativos. Foundation com hooks, CDN e REST API.
 * Version: 6.0.0
 * Author: Apollo Team
 * Author URI: https://apollo.rio.br
 * License: GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: apollo-core
 * Domain Path: /languages
 * Requires at least: 6.4
 * Tested up to: 6.9
 * Requires PHP: 8.1
 */

declare(strict_types=1);

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
    exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════════════════════════════════

define('APOLLO_CORE_VERSION', '6.0.0');
define('APOLLO_CORE_PATH', plugin_dir_path(__FILE__));
define('APOLLO_CORE_URL', plugin_dir_url(__FILE__));
define('APOLLO_CORE_FILE', __FILE__);

// ═══════════════════════════════════════════════════════════════════════════
// AUTOLOADER (must load BEFORE constants — ConfigLoader needs it)
// ═══════════════════════════════════════════════════════════════════════════

// Composer autoloader (includes PSR-4 for Apollo\Core namespace)
if (file_exists(APOLLO_CORE_PATH . 'vendor/autoload.php')) {
    require_once APOLLO_CORE_PATH . 'vendor/autoload.php';
}

// Manual autoloader fallback for src/ classes
spl_autoload_register(
    function (string $class) {
        $prefix   = 'Apollo\\Core\\';
        $base_dir = APOLLO_CORE_PATH . 'src/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);
        $file           = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
);

// ═══════════════════════════════════════════════════════════════════════════
// CONSTANTS (loaded after autoloader so ConfigLoader is available)
// ═══════════════════════════════════════════════════════════════════════════

// Load additional constants
require_once APOLLO_CORE_PATH . 'includes/constants.php';

// ═══════════════════════════════════════════════════════════════════════════
// HELPER FUNCTIONS (must load early)
// ═══════════════════════════════════════════════════════════════════════════

require_once APOLLO_CORE_PATH . 'includes/functions.php';
require_once APOLLO_CORE_PATH . 'includes/search-helpers.php';

// ═══════════════════════════════════════════════════════════════════════════
// REPORT MODAL (shared component — loaded on demand)
// ═══════════════════════════════════════════════════════════════════════════

require_once APOLLO_CORE_PATH . 'includes/report-modal.php';

// ═══════════════════════════════════════════════════════════════════════════
// ACTIVATION / DEACTIVATION / UNINSTALL
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Activation Hook
 * Pattern: CHECK IF EXISTS → BUILD IF NOT
 */
register_activation_hook(
    __FILE__,
    function () {
        \Apollo\Core\ActivationHandler::activate();
    }
);

/**
 * Deactivation Hook
 * Keeps all data by default (soft deactivation)
 */
register_deactivation_hook(
    __FILE__,
    function () {
        \Apollo\Core\UninstallHandler::deactivate();
    }
);

/**
 * Add custom cron schedules
 */
add_filter('cron_schedules', array(\Apollo\Core\ActivationHandler::class, 'add_cron_schedules'));

// ═══════════════════════════════════════════════════════════════════════════
// BOOTSTRAP
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Bootstrap Apollo Core
 *
 * This function is called by plugins_loaded hook
 */
function apollo_core_bootstrap()
{
    // Check for database upgrade
    \Apollo\Core\ActivationHandler::upgrade();

    // ─────────────────────────────────────────────────────────────────────
    // CRITICAL: Initialize CPT, Taxonomy, Meta Registries
    // These MUST load early to provide fallback for missing plugins
    // ─────────────────────────────────────────────────────────────────────

    // Taxonomy Registry (priority 4 - before CPTs)
    \Apollo\Core\TaxonomyRegistry::init();

    // CPT Registry (priority 5)
    \Apollo\Core\CPTRegistry::init();

    // Meta Registry (priority 9 - after CPTs)
    \Apollo\Core\MetaRegistry::init();

    // ─────────────────────────────────────────────────────────────────────
    // Initialize other core components
    // ─────────────────────────────────────────────────────────────────────

    // Initialize CDN helper
    if (class_exists('\Apollo\Core\CDN')) {
        \Apollo\Core\CDN::init();
    }

    // Initialize REST API controllers
    add_action(
        'rest_api_init',
        function () {
            if (class_exists('\Apollo\Core\API\HealthController')) {
                new \Apollo\Core\API\HealthController();
            }
            if (class_exists('\Apollo\Core\API\RegistryController')) {
                new \Apollo\Core\API\RegistryController();
            }
            if (class_exists('\Apollo\Core\API\SoundController')) {
                new \Apollo\Core\API\SoundController();
            }
            if (class_exists('\Apollo\Core\API\SearchController')) {
                $search = new \Apollo\Core\API\SearchController();
                $search->register_routes();
            }
        }
    );

    // ─────────────────────────────────────────────────────────────────────
    // Initialize Admin Pages
    // ─────────────────────────────────────────────────────────────────────
    if (is_admin()) {
        require_once APOLLO_CORE_PATH . 'admin/SettingsPage.php';
    }

    // ─────────────────────────────────────────────────────────────────────
    // Fire initialized hook
    // ─────────────────────────────────────────────────────────────────────
    do_action(
        \Apollo\Core\Config\ApolloHook::CORE_INITIALIZED,
        array(
            'version'   => APOLLO_CORE_VERSION,
            'cpt_count' => count(\Apollo\Core\CPTRegistry::get_instance()->get_definitions()),
            'tax_count' => count(\Apollo\Core\TaxonomyRegistry::get_instance()->get_definitions()),
        )
    );

    // Define bootstrap constant
    if (! defined('APOLLO_CORE_BOOTSTRAPPED')) {
        define('APOLLO_CORE_BOOTSTRAPPED', true);
    }
}

/**
 * Initialize Apollo Core
 *
 * Priority: plugins_loaded with priority 1 (load first)
 */
add_action('plugins_loaded', 'apollo_core_bootstrap', 1);

// ═══════════════════════════════════════════════════════════════════════════
// ADMIN NOTICES
// ═══════════════════════════════════════════════════════════════════════════

add_action(
    'admin_notices',
    function () {
        // Show activation results
        $results = get_transient('apollo_activation_results');

        if ($results) {
            delete_transient('apollo_activation_results');

            $created_count = count($results['created'] ?? array());
            $error_count   = count($results['errors'] ?? array());

            if ($created_count > 0 && $error_count === 0) {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p><strong>Apollo Core:</strong> ' . sprintf(
                    __('%d tabelas criadas com sucesso.', 'apollo-core'),
                    $created_count
                ) . '</p>';
                echo '</div>';
            } elseif ($error_count > 0) {
                echo '<div class="notice notice-error">';
                echo '<p><strong>Apollo Core:</strong> ' . sprintf(
                    __('Erro ao criar %d tabelas. Verifique o log.', 'apollo-core'),
                    $error_count
                ) . '</p>';
                echo '</div>';
            }
        }
    }
);
