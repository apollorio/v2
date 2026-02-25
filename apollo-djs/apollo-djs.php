<?php
/**
 * Plugin Name: Apollo DJs
 * Plugin URI: https://apollo.rio.br/plugins/apollo-djs
 * Description: DJs CPT: Shared across all plugins. Profile pages, social links, sound genres, carousel/slider/grid views. Style: apollo-v1.
 * Version: 1.0.0
 * Author: Apollo::Rio
 * Author URI: https://apollo.rio.br
 * License: Proprietary
 * Text Domain: apollo-djs
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Network: false
 *
 * @package Apollo\DJs
 */

declare(strict_types=1);

namespace Apollo\DJs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════════════════════════════════

define( 'APOLLO_DJ_VERSION', '1.0.0' );
define( 'APOLLO_DJ_FILE', __FILE__ );
define( 'APOLLO_DJ_DIR', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_DJ_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_DJ_BASENAME', plugin_basename( __FILE__ ) );

// ═══════════════════════════════════════════════════════════════════════════
// DEPENDENCY CHECK — apollo-core é OBRIGATÓRIO
// ═══════════════════════════════════════════════════════════════════════════

function apollo_dj_check_dependencies(): void {
	$active = get_option( 'active_plugins', array() );

	if ( ! in_array( 'apollo-core/apollo-core.php', $active, true ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>';
				echo '<strong>Apollo DJs:</strong> ';
				esc_html_e( 'Requer Apollo Core ativo.', 'apollo-djs' );
				echo '</p></div>';
			}
		);
		deactivate_plugins( APOLLO_DJ_BASENAME );
		return;
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_dj_check_dependencies', 5 );

// ═══════════════════════════════════════════════════════════════════════════
// AUTOLOADER — PSR-4: Apollo\DJs\ → src/
// ═══════════════════════════════════════════════════════════════════════════

if ( file_exists( APOLLO_DJ_DIR . 'vendor/autoload.php' ) ) {
	require_once APOLLO_DJ_DIR . 'vendor/autoload.php';
}

spl_autoload_register(
	function ( string $class ) {
		$prefix   = 'Apollo\\DJs\\';
		$base_dir = APOLLO_DJ_DIR . 'src/';
		$len      = strlen( $prefix );

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
// INCLUDES
// ═══════════════════════════════════════════════════════════════════════════

require_once APOLLO_DJ_DIR . 'includes/constants.php';
require_once APOLLO_DJ_DIR . 'includes/functions.php';

// Frontend Editor field definitions (shared system via apollo-templates)
if ( file_exists( APOLLO_DJ_DIR . 'includes/frontend-fields.php' ) ) {
	require_once APOLLO_DJ_DIR . 'includes/frontend-fields.php';
}

// ═══════════════════════════════════════════════════════════════════════════
// INITIALIZATION — após apollo-core (priority 15)
// ═══════════════════════════════════════════════════════════════════════════

function apollo_dj_init(): void {
	$GLOBALS['apollo_dj'] = new Plugin();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_dj_init', 15 );

// ═══════════════════════════════════════════════════════════════════════════
// ACTIVATION / DEACTIVATION
// ═══════════════════════════════════════════════════════════════════════════

register_activation_hook(
	__FILE__,
	function () {
		Activation::activate();
		flush_rewrite_rules();
	}
);

register_deactivation_hook(
	__FILE__,
	function () {
		Deactivation::deactivate();
		flush_rewrite_rules();
	}
);
