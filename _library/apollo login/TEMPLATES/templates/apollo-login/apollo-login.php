<?php
/**
 * Plugin Name: Apollo Login
 * Plugin URI: https://apollo.rio.br/plugins/apollo-login
 * Description: Auth: 7-Step Multi-Step Registration, Login, Password Reset, Mandatory Aptitude Quiz, WP Hide Ghost, Blank Canvas Templates, Rate Limiting, reCAPTCHA
 * Version: 1.0.0
 * Author: Apollo::Rio
 * Author URI: https://apollo.rio.br
 * License: Proprietary
 * Text Domain: apollo-login
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Network: false
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════════════════════════════════

define( 'APOLLO_LOGIN_VERSION', '1.0.0' );
define( 'APOLLO_LOGIN_FILE', __FILE__ );
define( 'APOLLO_LOGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_LOGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_LOGIN_BASENAME', plugin_basename( __FILE__ ) );

// ═══════════════════════════════════════════════════════════════════════════
// DEPENDENCY CHECK
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Check if apollo-core is active (REQUIRED for ALL Apollo plugins)
 */
function apollo_login_check_dependencies(): void {
	$active_plugins = get_option( 'active_plugins', [] );

	// Check apollo-core
	if ( ! in_array( 'apollo-core/apollo-core.php', $active_plugins, true ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>Apollo Login:</strong> Requires Apollo Core plugin to be active.</p></div>';
		});
		deactivate_plugins( APOLLO_LOGIN_BASENAME );
		return;
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_login_check_dependencies', 5 );

// ═══════════════════════════════════════════════════════════════════════════
// AUTOLOADER
// ═══════════════════════════════════════════════════════════════════════════

// Composer autoloader
if ( file_exists( APOLLO_LOGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once APOLLO_LOGIN_DIR . 'vendor/autoload.php';
}

// Manual PSR-4 autoloader fallback
spl_autoload_register( function( string $class ) {
	$prefix   = 'Apollo\\Login\\';
	$base_dir = APOLLO_LOGIN_DIR . 'src/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
});

// ═══════════════════════════════════════════════════════════════════════════
// INCLUDES
// ═══════════════════════════════════════════════════════════════════════════

require_once APOLLO_LOGIN_DIR . 'includes/constants.php';
require_once APOLLO_LOGIN_DIR . 'includes/functions.php';

// ═══════════════════════════════════════════════════════════════════════════
// INITIALIZATION
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Initialize plugin after apollo-core loads
 */
function apollo_login_init(): void {
	// Verify apollo-core is loaded
	if ( ! defined( 'APOLLO_CORE_VERSION' ) ) {
		return;
	}

	// Initialize main plugin class
	$plugin = Plugin::get_instance();
	$plugin->init();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_login_init', 15 );

// ═══════════════════════════════════════════════════════════════════════════
// ACTIVATION / DEACTIVATION
// ═══════════════════════════════════════════════════════════════════════════

register_activation_hook( __FILE__, function() {
	// Verify dependencies
	if ( ! is_plugin_active( 'apollo-core/apollo-core.php' ) ) {
		wp_die( 'Apollo Login requires Apollo Core to be active.' );
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
