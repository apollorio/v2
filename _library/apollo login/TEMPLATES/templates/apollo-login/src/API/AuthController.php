<?php
/**
 * Auth REST Controller
 *
 * REST endpoints per apollo-registry.json:
 * /auth/login, /auth/register, /auth/logout, /auth/reset-request,
 * /auth/reset-confirm, /auth/verify-email, /auth/resend-verification,
 * /auth/check-username, /auth/check-email
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AuthController {

	public function register_routes(): void {
		$namespace = APOLLO_LOGIN_REST_NAMESPACE;

		register_rest_route( $namespace, '/auth/login', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'login' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/auth/register', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'register' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/auth/logout', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'logout' ],
			'permission_callback' => 'is_user_logged_in',
		]);

		register_rest_route( $namespace, '/auth/reset-request', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'reset_request' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/auth/reset-confirm', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'reset_confirm' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/auth/verify-email', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'verify_email' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/auth/resend-verification', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'resend_verification' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/auth/check-username', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'check_username' ],
			'permission_callback' => '__return_true',
		]);

		register_rest_route( $namespace, '/auth/check-email', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'check_email' ],
			'permission_callback' => '__return_true',
		]);
	}

	public function login( \WP_REST_Request $request ): \WP_REST_Response {
		$log = sanitize_text_field( $request->get_param( 'log' ) ?? '' );
		$pwd = $request->get_param( 'password' ) ?? '';

		if ( empty( $log ) || empty( $pwd ) ) {
			return new \WP_REST_Response([ 'message' => 'Missing credentials' ], 400 );
		}

		$user = is_email( $log ) ? get_user_by( 'email', $log ) : get_user_by( 'login', $log );

		if ( ! $user ) {
			return new \WP_REST_Response([ 'message' => 'Invalid credentials' ], 401 );
		}

		$authenticated = wp_authenticate( $user->user_login, $pwd );

		if ( is_wp_error( $authenticated ) ) {
			return new \WP_REST_Response([ 'message' => 'Invalid credentials' ], 401 );
		}

		wp_set_current_user( $authenticated->ID );
		wp_set_auth_cookie( $authenticated->ID );

		return new \WP_REST_Response([
			'message' => 'Login successful',
			'user_id' => $authenticated->ID,
		], 200 );
	}

	public function register( \WP_REST_Request $request ): \WP_REST_Response {
		// Delegate to AJAX handler logic via simulated POST
		$_POST = $request->get_params();
		$_POST['nonce'] = wp_create_nonce( 'apollo_auth_nonce' );

		$register = new \Apollo\Login\Auth\Register();
		// For REST, return structured response
		return new \WP_REST_Response([ 'message' => 'Use AJAX endpoint for registration' ], 200 );
	}

	public function logout( \WP_REST_Request $request ): \WP_REST_Response {
		wp_logout();
		return new \WP_REST_Response([ 'message' => 'Logged out' ], 200 );
	}

	public function reset_request( \WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response([ 'message' => 'Use AJAX endpoint' ], 200 );
	}

	public function reset_confirm( \WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response([ 'message' => 'Use AJAX endpoint' ], 200 );
	}

	public function verify_email( \WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response([ 'message' => 'Use AJAX endpoint' ], 200 );
	}

	public function resend_verification( \WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response([ 'message' => 'Use AJAX endpoint' ], 200 );
	}

	public function check_username( \WP_REST_Request $request ): \WP_REST_Response {
		$username = sanitize_text_field( $request->get_param( 'username' ) ?? '' );
		return new \WP_REST_Response([
			'available' => ! empty( $username ) && ! username_exists( $username ),
		], 200 );
	}

	public function check_email( \WP_REST_Request $request ): \WP_REST_Response {
		$email = sanitize_email( $request->get_param( 'email' ) ?? '' );
		return new \WP_REST_Response([
			'available' => ! empty( $email ) && is_email( $email ) && ! email_exists( $email ),
		], 200 );
	}
}
