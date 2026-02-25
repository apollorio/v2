<?php
/**
 * Plugin Name: Apollo Users
 * Plugin URI: https://apollo.rio.br/plugins/apollo-users
 * Description: Users: Roles, Capabilities, Profile page (/id/username), Minha Conta, Ratings, Depoimentos, Preferences, Matchmaking, Advanced Fields, Author Protection
 * Version: 2.0.0
 * Author: Apollo::Rio
 * Author URI: https://apollo.rio.br
 * License: Proprietary
 * Text Domain: apollo-users
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Network: false
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users;

// ═══════════════════════════════════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════════════════════════════════

define( 'APOLLO_USERS_VERSION', '2.0.0' );
define( 'APOLLO_USERS_FILE', __FILE__ );
define( 'APOLLO_USERS_DIR', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_USERS_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_USERS_BASENAME', plugin_basename( __FILE__ ) );

// ═══════════════════════════════════════════════════════════════════════════
// DEPENDENCY CHECK
// ═══════════════════════════════════════════════════════════════════════════

function apollo_users_check_dependencies(): void {
	$active_plugins = get_option( 'active_plugins', [] );

	if ( ! in_array( 'apollo-core/apollo-core.php', $active_plugins, true ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>Apollo Users:</strong> Requires Apollo Core plugin.</p></div>';
		});
		deactivate_plugins( APOLLO_USERS_BASENAME );
		return;
	}

	if ( ! in_array( 'apollo-login/apollo-login.php', $active_plugins, true ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>Apollo Users:</strong> Requires Apollo Login plugin.</p></div>';
		});
		deactivate_plugins( APOLLO_USERS_BASENAME );
		return;
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_users_check_dependencies', 5 );

// ═══════════════════════════════════════════════════════════════════════════
// TEXTDOMAIN
// ═══════════════════════════════════════════════════════════════════════════

function apollo_users_load_textdomain(): void {
	if ( ! is_textdomain_loaded( 'apollo-users' ) ) {
		load_plugin_textdomain( 'apollo-users', false, dirname( APOLLO_USERS_BASENAME ) . '/languages/' );
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_users_load_textdomain', 1 );

// ═══════════════════════════════════════════════════════════════════════════
// AUTOLOADER
// ═══════════════════════════════════════════════════════════════════════════

if ( file_exists( APOLLO_USERS_DIR . 'vendor/autoload.php' ) ) {
	require_once APOLLO_USERS_DIR . 'vendor/autoload.php';
}

spl_autoload_register( function( string $class ) {
	$prefix   = 'Apollo\\Users\\';
	$base_dir = APOLLO_USERS_DIR . 'src/';
	$len      = strlen( $prefix );

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

require_once APOLLO_USERS_DIR . 'includes/constants.php';
require_once APOLLO_USERS_DIR . 'includes/functions.php';

// ═══════════════════════════════════════════════════════════════════════════
// QUERY VARS & REWRITE RULES
// ═══════════════════════════════════════════════════════════════════════════

function apollo_users_register_query_vars( $vars ) {
	$vars[] = 'apollo_user_page';
	$vars[] = 'apollo_username';
	$vars[] = 'apollo_account_section';
	return $vars;
}
add_filter( 'query_vars', __NAMESPACE__ . '\\apollo_users_register_query_vars' );

function apollo_users_add_rewrite_rules(): void {
	// Profile: /id/{username}
	add_rewrite_rule( '^id/([^/]+)/?$', 'index.php?apollo_user_page=profile&apollo_username=$matches[1]', 'top' );

	// Perfil alias: /perfil/{username} -> redirects to /id/{username}
	add_rewrite_rule( '^perfil/([^/]+)/?$', 'index.php?apollo_user_page=perfil-redirect&apollo_username=$matches[1]', 'top' );

	// Minha Conta: /minha-conta and /minha-conta/{section}
	add_rewrite_rule( '^minha-conta/([^/]+)/?$', 'index.php?apollo_user_page=minha-conta&apollo_account_section=$matches[1]', 'top' );
	add_rewrite_rule( '^minha-conta/?$', 'index.php?apollo_user_page=minha-conta', 'top' );

	// Edit profile: /editar-perfil
	add_rewrite_rule( '^editar-perfil/?$', 'index.php?apollo_user_page=edit-profile', 'top' );

	// Radar: /radar
	add_rewrite_rule( '^radar/?$', 'index.php?apollo_user_page=radar', 'top' );
}
add_action( 'init', __NAMESPACE__ . '\\apollo_users_add_rewrite_rules', 1 );

// ═══════════════════════════════════════════════════════════════════════════
// VIRTUAL PAGES HANDLER
// ═══════════════════════════════════════════════════════════════════════════

function apollo_users_handle_virtual_pages(): void {
	$path = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );

	// ───── /perfil/{username} → redirect to /id/{username} ─────
	if ( preg_match( '#^perfil/([a-zA-Z0-9_.@-]+)/?$#', $path, $m ) ) {
		wp_redirect( home_url( '/id/' . $m[1] . '/' ), 301 );
		exit;
	}

	// ───── /id/{username} → Profile Page ─────
	if ( preg_match( '#^id/([a-zA-Z0-9_.@-]+)/?$#', $path, $m ) ) {
		$username = sanitize_user( $m[1] );
		$user     = get_user_by( 'login', $username ) ?: get_user_by( 'slug', $username );

		if ( $user && $user->ID > 0 ) {
			global $wp_query, $apollo_profile_user;
			$wp_query->is_404    = false;
			$apollo_profile_user = $user;
			status_header( 200 );

			// Record profile view
			apollo_record_profile_view( $user->ID, get_current_user_id() ?: null );

			apollo_users_enqueue_page_assets( 'profile' );
			include APOLLO_USERS_DIR . 'templates/single-profile.php';
			exit;
		}
		// Fall through to 404
	}

	// ───── /minha-conta → Account Page ─────
	if ( preg_match( '#^minha-conta(/([a-zA-Z0-9_-]+))?/?$#', $path, $m ) ) {
		if ( ! is_user_logged_in() ) {
			wp_redirect( home_url( '/acesso/?redirect=' . urlencode( home_url( '/minha-conta/' ) ) ) );
			exit;
		}

		global $wp_query, $apollo_account_section;
		$wp_query->is_404       = false;
		$apollo_account_section = $m[2] ?? 'account';
		status_header( 200 );

		apollo_users_enqueue_page_assets( 'account' );
		include APOLLO_USERS_DIR . 'templates/minha-conta.php';
		exit;
	}

	// ───── /editar-perfil → Edit Profile ─────
	if ( $path === 'editar-perfil' ) {
		if ( ! is_user_logged_in() ) {
			wp_redirect( home_url( '/acesso/' ) );
			exit;
		}

		global $wp_query;
		$wp_query->is_404 = false;
		status_header( 200 );

		apollo_users_enqueue_page_assets( 'edit-profile' );
		include APOLLO_USERS_DIR . 'templates/edit-profile.php';
		exit;
	}

	// ───── /radar → User Directory ─────
	if ( $path === 'radar' ) {
		global $wp_query;
		$wp_query->is_404 = false;
		status_header( 200 );

		apollo_users_enqueue_page_assets( 'radar' );
		include APOLLO_USERS_DIR . 'templates/user-radar.php';
		exit;
	}
}
add_action( 'template_redirect', __NAMESPACE__ . '\\apollo_users_handle_virtual_pages', 1 );

// ═══════════════════════════════════════════════════════════════════════════
// INITIALIZATION
// ═══════════════════════════════════════════════════════════════════════════

function apollo_users_init(): void {
	$current_url = $_SERVER['REQUEST_URI'] ?? '';
	if ( strpos( $current_url, '/acesso' ) !== false || strpos( $current_url, '/registre' ) !== false ) {
		return;
	}

	try {
		$plugin = Plugin::get_instance();
		$plugin->init();
	} catch ( \Exception $e ) {
		error_log( 'Apollo Users: ' . $e->getMessage() );
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\apollo_users_init', 20 );

// ═══════════════════════════════════════════════════════════════════════════
// ACTIVATION / DEACTIVATION
// ═══════════════════════════════════════════════════════════════════════════

register_activation_hook( __FILE__, function() {
	if ( ! is_plugin_active( 'apollo-core/apollo-core.php' ) ) {
		wp_die( 'Apollo Users requires Apollo Core.' );
	}
	if ( ! is_plugin_active( 'apollo-login/apollo-login.php' ) ) {
		wp_die( 'Apollo Users requires Apollo Login.' );
	}

	Activation::activate();
	apollo_users_add_rewrite_rules();
	flush_rewrite_rules();
});

register_deactivation_hook( __FILE__, function() {
	Deactivation::deactivate();
	flush_rewrite_rules();
});
