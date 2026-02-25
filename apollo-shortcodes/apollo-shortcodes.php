<?php
/**
 * Plugin Name: Apollo Shortcodes
 * Plugin URI: https://apollo.rio.br/plugins/apollo-shortcode
 * Description: Shortcodes: ALL frontend shortcodes registry organized here
 * Version: 1.0.0
 * Author: Apollo::Rio
 * Author URI: https://apollo.rio.br
 * License: Proprietary
 * Text Domain: apollo-shortcodes
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Network: false
 *
 * @package Apollo\Shortcode
 */

declare(strict_types=1);

namespace Apollo\Shortcode;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════════════════════════════════

define( 'APOLLO_SHORTCODE_VERSION', '1.0.0' );
define( 'APOLLO_SHORTCODE_FILE', __FILE__ );
define( 'APOLLO_SHORTCODE_DIR', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_SHORTCODE_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_SHORTCODE_BASENAME', plugin_basename( __FILE__ ) );

// ═══════════════════════════════════════════════════════════════════════════
// DEPENDENCY CHECK
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Check if apollo-core is active (OPTIONAL - plugin can work independently)
 */
/*
function apollo_shortcode_check_dependencies(): void {
	$active_plugins = get_option( 'active_plugins', [] );

	// Check apollo-core
	if ( ! in_array( 'apollo-core/apollo-core.php', $active_plugins, true ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>Apollo Shortcodes:</strong> Requires Apollo Core plugin to be active.</p></div>';
		});
		deactivate_plugins( APOLLO_SHORTCODE_BASENAME );
		return;
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_shortcode_check_dependencies', 5 );
*/

// ═══════════════════════════════════════════════════════════════════════════
// AUTOLOADER
// ═══════════════════════════════════════════════════════════════════════════

// Composer autoloader
if ( file_exists( APOLLO_SHORTCODE_DIR . 'vendor/autoload.php' ) ) {
	require_once APOLLO_SHORTCODE_DIR . 'vendor/autoload.php';
}

// Manual PSR-4 autoloader fallback
spl_autoload_register(
	function ( string $class ) {
		$prefix   = 'Apollo\\Shortcode\\';
		$base_dir = APOLLO_SHORTCODE_DIR . 'src/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
				return;
		}

		$relative_class = substr( $class, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

// ═══════════════════════════════════════════════════════════════════════════
// INCLUDES
// ═══════════════════════════════════════════════════════════════════════════

if ( file_exists( APOLLO_SHORTCODE_DIR . 'includes/constants.php' ) ) {
	require_once APOLLO_SHORTCODE_DIR . 'includes/constants.php';
}
if ( file_exists( APOLLO_SHORTCODE_DIR . 'includes/functions.php' ) ) {
	require_once APOLLO_SHORTCODE_DIR . 'includes/functions.php';
}

// ═══════════════════════════════════════════════════════════════════════════
// INITIALIZATION
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Initialize plugin (works independently)
 */
function apollo_shortcode_init(): void {
	// No longer requires apollo-core
	// Initialize main plugin class
	$plugin = Plugin::get_instance();
	$plugin->init();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_shortcode_init', 15 );

// ═══════════════════════════════════════════════════════════════════════════
// ACTIVATION / DEACTIVATION
// ═══════════════════════════════════════════════════════════════════════════

register_activation_hook(
	__FILE__,
	function () {
		// No longer requires apollo-core
		// Run activation tasks
		if ( class_exists( __NAMESPACE__ . '\\Activation' ) ) {
			Activation::activate();
		}

		// Flush rewrite rules
		flush_rewrite_rules();
	}
);

register_deactivation_hook(
	__FILE__,
	function () {
		if ( class_exists( __NAMESPACE__ . '\\Deactivation' ) ) {
			Deactivation::deactivate();
		}
		flush_rewrite_rules();
	}
);
