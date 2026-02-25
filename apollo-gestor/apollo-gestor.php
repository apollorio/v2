<?php

/**
 * Plugin Name: Apollo Gestor
 * Plugin URI: https://apollo.rio.br/plugins/apollo-gestor
 * Description: Event production management — tasks, team, budget, suppliers, timeline, kanban. Gestor Apollo for Rio's culture industry.
 * Version: 1.0.0
 * Author: Apollo::Rio
 * Author URI: https://apollo.rio.br
 * License: Proprietary
 * Text Domain: apollo-gestor
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Network: false
 *
 * @package Apollo\Gestor
 */

declare(strict_types=1);

namespace Apollo\Gestor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════════════════════════════════

define( 'APOLLO_GESTOR_VERSION', '1.0.0' );
define( 'APOLLO_GESTOR_DB_VERSION', 1 );
define( 'APOLLO_GESTOR_FILE', __FILE__ );
define( 'APOLLO_GESTOR_DIR', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_GESTOR_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_GESTOR_BASENAME', plugin_basename( __FILE__ ) );

// ═══════════════════════════════════════════════════════════════════════════
// DEPENDENCY CHECK
// ═══════════════════════════════════════════════════════════════════════════

function apollo_gestor_check_dependencies(): void {
	$active = get_option( 'active_plugins', array() );

	if ( ! in_array( 'apollo-core/apollo-core.php', $active, true ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p><strong>Apollo Gestor:</strong> Requer Apollo Core ativo.</p></div>';
			}
		);
		deactivate_plugins( APOLLO_GESTOR_BASENAME );
		return;
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_gestor_check_dependencies', 5 );

// ═══════════════════════════════════════════════════════════════════════════
// AUTOLOADER (PSR-4)
// ═══════════════════════════════════════════════════════════════════════════

spl_autoload_register(
	function ( string $class ) {
		$prefix   = 'Apollo\\Gestor\\';
		$base_dir = APOLLO_GESTOR_DIR . 'src/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative = substr( $class, $len );
		$file     = $base_dir . str_replace( '\\', '/', $relative ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

// ═══════════════════════════════════════════════════════════════════════════
// INITIALIZATION
// ═══════════════════════════════════════════════════════════════════════════

function apollo_gestor_init(): void {
	if ( ! defined( 'APOLLO_CORE_VERSION' ) ) {
		return;
	}

	$plugin = Plugin::get_instance();
	$plugin->init();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_gestor_init', 15 );

// ═══════════════════════════════════════════════════════════════════════════
// ACTIVATION / DEACTIVATION
// ═══════════════════════════════════════════════════════════════════════════

register_activation_hook(
	__FILE__,
	function () {
		if ( ! is_plugin_active( 'apollo-core/apollo-core.php' ) ) {
			wp_die( 'Apollo Gestor requer Apollo Core ativo.' );
		}
		Database::install();
	}
);

register_deactivation_hook(
	__FILE__,
	function () {
		// Nothing to clean on deactivation
	}
);
