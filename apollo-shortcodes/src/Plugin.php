<?php

/**
 * Main Plugin Class (Singleton)
 *
 * PSR-4 Autoloaded version in src/ directory
 *
 * @package Apollo\Shortcode
 */

declare(strict_types=1);

namespace Apollo\Shortcodes;

// Prevent direct access.
if (! defined('ABSPATH')) {
    exit;
}

// Include the main plugin class from includes directory
// This file exists for PSR-4 autoloader compatibility
if (! class_exists(__NAMESPACE__ . '\\Plugin')) {
    require_once APOLLO_SHORTCODE_DIR . 'includes/class-plugin.php';
}
