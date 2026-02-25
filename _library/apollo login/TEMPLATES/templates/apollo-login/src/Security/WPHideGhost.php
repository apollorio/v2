<?php
/**
 * WP Hide Ghost
 *
 * Hook-based protection - NO core WordPress file modification.
 * Per apollo-registry.json: 4 protection layers.
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Security;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPHideGhost {

	public function __construct() {
		if ( ! get_option( 'apollo_login_hide_wp_login', true ) ) {
			return;
		}

		// Layer 1: Hide /wp-login.php → 404
		add_action( 'login_init', [ $this, 'block_wp_login' ], 1 );

		// Layer 2: Hide /wp-admin for non-logged users → 404
		add_action( 'admin_init', [ $this, 'block_wp_admin' ], 1 );

		// Layer 3: Filter URLs to prevent exposure
		add_filter( 'site_url', [ $this, 'filter_site_url' ], 10, 4 );
		add_filter( 'wp_redirect', [ $this, 'filter_redirect' ], 10, 2 );
		add_filter( 'login_url', [ $this, 'custom_login_url' ], 999, 3 );
		add_filter( 'logout_url', [ $this, 'custom_logout_url' ], 999, 2 );
		add_filter( 'register_url', [ $this, 'custom_register_url' ] );
		add_filter( 'lostpassword_url', [ $this, 'custom_lostpassword_url' ], 999, 2 );
	}

	/**
	 * Layer 1: Block direct access to /wp-login.php
	 */
	public function block_wp_login(): void {
		$action = $_REQUEST['action'] ?? '';

		// Whitelist certain actions per registry
		$whitelist = [ 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'postpass' ];

		if ( in_array( $action, $whitelist, true ) ) {
			return;
		}

		// Allow WP-CLI
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return;
		}

		// Allow AJAX requests
		if ( wp_doing_ajax() ) {
			return;
		}

		// Allow cron
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		// Show real 404
		$this->show_404();
	}

	/**
	 * Layer 2: Block /wp-admin for non-logged users
	 */
	public function block_wp_admin(): void {
		if ( ! get_option( 'apollo_login_hide_wp_admin', true ) ) {
			return;
		}

		// Allow logged-in users
		if ( is_user_logged_in() ) {
			return;
		}

		// Allow AJAX
		if ( wp_doing_ajax() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			return;
		}

		$this->show_404();
	}

	/**
	 * Layer 3: Filter site_url to replace wp-login.php references
	 */
	public function filter_site_url( string $url, string $path, ?string $scheme, ?int $blog_id ): string {
		if ( false !== strpos( $url, 'wp-login.php' ) ) {
			$custom_url = home_url( '/' . APOLLO_LOGIN_PAGE_ACESSO . '/' );
			$url        = str_replace( site_url( 'wp-login.php' ), $custom_url, $url );
		}
		return $url;
	}

	public function filter_redirect( string $location, int $status ): string {
		if ( false !== strpos( $location, 'wp-login.php' ) ) {
			$location = home_url( '/' . APOLLO_LOGIN_PAGE_ACESSO . '/' );
		}
		return $location;
	}

	public function custom_login_url( string $login_url, string $redirect, bool $force_reauth ): string {
		$login_url = home_url( '/' . APOLLO_LOGIN_PAGE_ACESSO . '/' );
		if ( $redirect ) {
			$login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
		}
		return $login_url;
	}

	public function custom_logout_url( string $logout_url, string $redirect ): string {
		return home_url( '/' . APOLLO_LOGIN_PAGE_SAIR . '/' );
	}

	public function custom_register_url( string $register_url ): string {
		return home_url( '/' . APOLLO_LOGIN_PAGE_REGISTRE . '/' );
	}

	public function custom_lostpassword_url( string $lostpassword_url, string $redirect ): string {
		return home_url( '/' . APOLLO_LOGIN_PAGE_RESET . '/' );
	}

	/**
	 * Layer 4: Show real 404
	 */
	private function show_404(): void {
		status_header( 404 );
		nocache_headers();

		// Try to load theme's 404 template
		$template = get_query_template( '404' );
		if ( $template ) {
			include $template;
		} else {
			// Inline fallback
			echo '<!DOCTYPE html><html><head><title>404</title></head><body><h1>404 — Not Found</h1></body></html>';
		}
		exit;
	}
}
