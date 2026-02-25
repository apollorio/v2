<?php
/**
 * Plugin Name: Apollo Runtime
 * Plugin URI: https://apollo.rio.br
 * Description: Host layer do ecossistema Apollo. Carrega configurações de ambiente, orquestra módulos e integrações sobre o Apollo Core.
 * Version: 1.0.0
 * Author: Apollo Team
 * Text Domain: apollo-runtime
 * Requires at least: 6.4
 * Requires PHP: 8.1
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'APOLLO_RUNTIME_VERSION', '1.0.0' );
define( 'APOLLO_RUNTIME_PATH', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_RUNTIME_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_RUNTIME_FILE', __FILE__ );
define( 'APOLLO_RUNTIME_CONFIG_PATH', APOLLO_RUNTIME_PATH . 'config' );

require_once APOLLO_RUNTIME_PATH . 'bootstrap.php';
