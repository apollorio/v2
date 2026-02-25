<?php
/**
 * Security REST Controller
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SecurityController {

	public function register_routes(): void {
		$namespace = APOLLO_LOGIN_REST_NAMESPACE;

		register_rest_route( $namespace, '/security/rewrites', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_rewrites' ],
			'permission_callback' => [ $this, 'admin_check' ],
		]);

		register_rest_route( $namespace, '/security/attempts', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_attempts' ],
			'permission_callback' => [ $this, 'admin_check' ],
		]);
	}

	public function admin_check(): bool {
		return current_user_can( 'manage_options' );
	}

	public function get_rewrites( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$table    = $wpdb->prefix . APOLLO_LOGIN_TABLE_URL_REWRITES;
		$rewrites = $wpdb->get_results( "SELECT * FROM {$table} WHERE is_active = 1 ORDER BY id DESC" );
		return new \WP_REST_Response( $rewrites, 200 );
	}

	public function get_attempts( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$table    = $wpdb->prefix . APOLLO_LOGIN_TABLE_LOGIN_ATTEMPTS;
		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$per_page = 50;
		$offset   = ( $page - 1 ) * $per_page;

		$attempts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);

		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );

		return new \WP_REST_Response([
			'attempts' => $attempts,
			'total'    => $total,
			'page'     => $page,
			'pages'    => ceil( $total / $per_page ),
		], 200 );
	}
}
