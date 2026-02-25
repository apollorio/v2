<?php

/**
 * Plugin Name: Apollo Classifieds
 * Plugin URI: https://apollo.rio.br
 * Description: Sistema de classificados marketplace para Apollo — tickets, acomodações, e anúncios gerais com modal de disclaimer obrigatório.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Apollo Team
 * Author URI: https://apollo.rio.br
 * License: Proprietary
 * Text Domain: apollo-classifieds
 * Domain Path: /languages
 *
 * @package Apollo\Classifieds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Constants
define( 'APOLLO_CLASSIFIEDS_VERSION', '1.0.0' );
define( 'APOLLO_CLASSIFIEDS_PATH', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_CLASSIFIEDS_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_CLASSIFIEDS_FILE', __FILE__ );

// PSR-4 Autoloader
spl_autoload_register(
	function ( string $class ) {
		$prefix   = 'Apollo\\Classifieds\\';
		$base_dir = APOLLO_CLASSIFIEDS_PATH . 'src/';

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

// Bootstrap plugin
add_action(
	'plugins_loaded',
	function () {
		if ( class_exists( 'Apollo\\Classifieds\\Plugin' ) ) {
			$plugin = new Apollo\Classifieds\Plugin();
			$plugin->init();
		}
	}
);

// Load demo data generator (admin only)
if ( is_admin() ) {
	require_once APOLLO_CLASSIFIEDS_PATH . 'includes/demo-data.php';
}

// Activation hook
register_activation_hook(
	__FILE__,
	function () {
		// Flush rewrite rules for custom URLs
		flush_rewrite_rules();
	}
);

// Deactivation hook
register_deactivation_hook(
	__FILE__,
	function () {
		flush_rewrite_rules();
	}
);
