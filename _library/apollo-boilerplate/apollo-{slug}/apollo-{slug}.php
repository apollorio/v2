<?php
/**
 * Plugin Name: Apollo {Name}
 * Plugin URI: https://apollo.rio.br/plugins/apollo-{slug}
 * Description: {Description}
 * Version: 1.0.0
 * Author: Apollo::Rio
 * Author URI: https://apollo.rio.br
 * License: Proprietary
 * Text Domain: apollo-{slug}
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Network: false
 *
 * @package Apollo\{Namespace}
 */

declare(strict_types=1);

namespace Apollo\{Namespace};

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════════════════════════════════

define( 'APOLLO_{CONST}_VERSION', '1.0.0' );
define( 'APOLLO_{CONST}_FILE', __FILE__ );
define( 'APOLLO_{CONST}_DIR', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_{CONST}_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_{CONST}_BASENAME', plugin_basename( __FILE__ ) );

// ═══════════════════════════════════════════════════════════════════════════
// DEPENDENCY CHECK
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Check if apollo-core is active (REQUIRED for ALL Apollo plugins)
 */
function apollo_{slug}_check_dependencies(): void {
	$active_plugins = get_option( 'active_plugins', [] );

	// Check apollo-core
	if ( ! in_array( 'apollo-core/apollo-core.php', $active_plugins, true ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>Apollo {Name}:</strong> Requires Apollo Core plugin to be active.</p></div>';
		});
		deactivate_plugins( APOLLO_{CONST}_BASENAME );
		return;
	}

	// Additional dependencies (modify as needed)
	// Example: apollo-login for user-related plugins
	// if ( ! in_array( 'apollo-login/apollo-login.php', $active_plugins, true ) ) { ... }
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_{slug}_check_dependencies', 5 );

// ═══════════════════════════════════════════════════════════════════════════
// AUTOLOADER
// ═══════════════════════════════════════════════════════════════════════════

// Composer autoloader
if ( file_exists( APOLLO_{CONST}_DIR . 'vendor/autoload.php' ) ) {
	require_once APOLLO_{CONST}_DIR . 'vendor/autoload.php';
}

// Manual PSR-4 autoloader fallback
spl_autoload_register( function( string $class ) {
	$prefix = 'Apollo\\{Namespace}\\';
	$base_dir = APOLLO_{CONST}_DIR . 'src/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
});

// ═══════════════════════════════════════════════════════════════════════════
// INCLUDES
// ═══════════════════════════════════════════════════════════════════════════

require_once APOLLO_{CONST}_DIR . 'includes/constants.php';
require_once APOLLO_{CONST}_DIR . 'includes/functions.php';

// ═══════════════════════════════════════════════════════════════════════════
// INITIALIZATION
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Initialize plugin after apollo-core loads
 */
function apollo_{slug}_init(): void {
	// Verify apollo-core is loaded
	if ( ! defined( 'APOLLO_CORE_VERSION' ) ) {
		return;
	}

	// Initialize main plugin class
	$plugin = Plugin::get_instance();
	$plugin->init();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_{slug}_init', 15 );

// ═══════════════════════════════════════════════════════════════════════════
// ACTIVATION / DEACTIVATION
// ═══════════════════════════════════════════════════════════════════════════

register_activation_hook( __FILE__, function() {
	// Verify dependencies
	if ( ! is_plugin_active( 'apollo-core/apollo-core.php' ) ) {
		wp_die( 'Apollo {Name} requires Apollo Core to be active.' );
	}

	// Run activation tasks
	Activation::activate();

	// Flush rewrite rules
	flush_rewrite_rules();
});

register_deactivation_hook( __FILE__, function() {
	Deactivation::deactivate();
	flush_rewrite_rules();
});
