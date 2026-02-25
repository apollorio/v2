<?php
/**
 * Plugin Name: Apollo Events
 * Plugin URI: https://apollo.rio.br/plugins/apollo-events
 * Description: Events CPT: Backend, listings, single event, multi-view calendar, card/list/map views, 4 style packs (base, apollo-v1, ui-thim, ui-lis), expiration system 30min. Adapted from WP Event Manager + Apollo Events Manager.
 * Version: 1.0.0
 * Author: Apollo::Rio
 * Author URI: https://apollo.rio.br
 * License: Proprietary
 * Text Domain: apollo-events
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Network: false
 *
 * @package Apollo\Event
 */

declare(strict_types=1);

namespace Apollo\Event;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════════════════════════════════

define( 'APOLLO_EVENT_VERSION', '1.0.0' );
define( 'APOLLO_EVENT_FILE', __FILE__ );
define( 'APOLLO_EVENT_DIR', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_EVENT_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_EVENT_BASENAME', plugin_basename( __FILE__ ) );

// ═══════════════════════════════════════════════════════════════════════════
// DEPENDENCY CHECK — apollo-core é OBRIGATÓRIO
// ═══════════════════════════════════════════════════════════════════════════

function apollo_event_check_dependencies(): void {
	$active = get_option( 'active_plugins', array() );

	if ( ! in_array( 'apollo-core/apollo-core.php', $active, true ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>';
				echo '<strong>Apollo Events:</strong> ';
				esc_html_e( 'Requer Apollo Core ativo.', 'apollo-events' );
				echo '</p></div>';
			}
		);
		deactivate_plugins( APOLLO_EVENT_BASENAME );
		return;
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_event_check_dependencies', 5 );

// ═══════════════════════════════════════════════════════════════════════════
// AUTOLOADER — PSR-4: Apollo\Event\ → src/
// ═══════════════════════════════════════════════════════════════════════════

if ( file_exists( APOLLO_EVENT_DIR . 'vendor/autoload.php' ) ) {
	require_once APOLLO_EVENT_DIR . 'vendor/autoload.php';
}

spl_autoload_register(
	function ( string $class ) {
		$prefix   = 'Apollo\\Event\\';
		$base_dir = APOLLO_EVENT_DIR . 'src/';
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

require_once APOLLO_EVENT_DIR . 'includes/constants.php';
require_once APOLLO_EVENT_DIR . 'includes/functions.php';

// ═══════════════════════════════════════════════════════════════════════════
// INITIALIZATION — após apollo-core (priority 15)
// ═══════════════════════════════════════════════════════════════════════════

function apollo_event_init(): void {
	if ( ! defined( 'APOLLO_CORE_VERSION' ) ) {
		return;
	}

	Plugin::get_instance()->init();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_event_init', 15 );

// ═══════════════════════════════════════════════════════════════════════════
// ACTIVATION / DEACTIVATION
// ═══════════════════════════════════════════════════════════════════════════

register_activation_hook(
	__FILE__,
	function () {
		if ( ! defined( 'APOLLO_CORE_VERSION' ) ) {
			return;
		}
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
