<?php
/**
 * Helper Functions
 *
 * @package Apollo\{Namespace}
 */

declare(strict_types=1);

namespace Apollo\{Namespace};

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get plugin instance
 *
 * @return Plugin
 */
function apollo_{slug}(): Plugin {
	return Plugin::get_instance();
}

// Add plugin-specific helper functions below
