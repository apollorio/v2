<?php
/**
 * Auth REST Controller
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\API;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Auth Controller class
 */
class AuthController extends WP_REST_Controller {

	/**
	 * Namespace
	 *
	 * @var string
	 */
	protected $namespace = APOLLO_LOGIN_REST_NAMESPACE;

	/**
	 * Register routes
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// POST /auth/login
		register_rest_route(
			$this->namespace,
			'/auth/login',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'login' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'username' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'password' => array(
						'required' => true,
						'type'     => 'string',
					),
				),
			)
		);

		// POST /auth/register
		register_rest_route(
			$this->namespace,
			'/auth/register',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'register' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'social_name'      => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => function( $social_name ) {
							return strlen( $social_name ) >= 2;
						},
					),
					'instagram_username' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => function( $username ) {
							return sanitize_user( str_replace( '@', '', $username ) );
						},
					),
					'username'         => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_user',
					),
					'email'            => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_email',
					),
					'password'         => array(
						'required' => true,
						'type'     => 'string',
					),
					'apollo_quiz_token' => array(
						'required' => true,
						'type'     => 'string',
					),
					'sounds'           => array(
						'required'          => true,
						'type'              => 'array',
						'items'             => array( 'type' => 'integer' ),
						'sanitize_callback' => function( $sounds ) {
							return array_map( 'absint', (array) $sounds );
						},
						'validate_callback' => function( $sounds ) {
							$sounds = (array) $sounds;
							if ( count( $sounds ) < 1 ) {
								return new \WP_Error( 'sounds_required', __( 'Select at least 1 sound preference.', 'apollo-login' ) );
							}
							if ( count( $sounds ) > 5 ) {
								return new \WP_Error( 'sounds_limit', __( 'Maximum 5 sound preferences allowed.', 'apollo-login' ) );
							}
							return true;
						},
					),
				),
			)
		);

		// POST /auth/logout
		register_rest_route(
			$this->namespace,
			'/auth/logout',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'logout' ),
				'permission_callback' => 'is_user_logged_in',
			)
		);

		// POST /auth/reset-request
		register_rest_route(
			$this->namespace,
			'/auth/reset-request',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'reset_request' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'email' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_email',
					),
				),
			)
		);

		// GET /auth/check-username
		register_rest_route(
			$this->namespace,
			'/auth/check-username',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'check_username' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'username' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_user',
					),
				),
			)
		);

		// GET /auth/check-email
		register_rest_route(
			$this->namespace,
			'/auth/check-email',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'check_email' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'email' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_email',
					),
				),
			)
		);
	}

	/**
	 * Login endpoint
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function login( WP_REST_Request $request ) {
		$username = $request->get_param( 'username' );
		$password = $request->get_param( 'password' );

		$user = wp_authenticate( $username, $password );

		if ( is_wp_error( $user ) ) {
			return new WP_Error(
				'login_failed',
				$user->get_error_message(),
				array( 'status' => 401 )
			);
		}

		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID );

		return new WP_REST_Response(
			array(
				'success' => true,
				'user_id' => $user->ID,
				'message' => __( 'Login successful', 'apollo-login' ),
			),
			200
		);
	}

	/**
	 * Register endpoint
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function register( WP_REST_Request $request ) {
		$social_name  = $request->get_param( 'social_name' );
		$instagram    = $request->get_param( 'instagram_username' );
		$username     = $request->get_param( 'username' );
		$email        = $request->get_param( 'email' );
		$password     = $request->get_param( 'password' );
		$quiz_token   = $request->get_param( 'apollo_quiz_token' );
		$sounds       = $request->get_param( 'sounds' );

		// Validate quiz token
		$quiz_data = get_transient( 'apollo_quiz_' . $quiz_token );
		if ( false === $quiz_data ) {
			return new WP_Error(
				'quiz_required',
				__( 'Quiz completion required', 'apollo-login' ),
				array( 'status' => 400 )
			);
		}

		// Inject data into $_POST for RegisterHandler hook
		$_POST['social_name']        = $social_name;
		$_POST['instagram_username'] = $instagram;
		$_POST['sounds']             = $sounds;
		$_POST['apollo_quiz_token']  = $quiz_token;

		// Create user
		$user_id = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			return new WP_Error(
				'registration_failed',
				$user_id->get_error_message(),
				array( 'status' => 400 )
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'user_id' => $user_id,
				'message' => __( 'Registration successful', 'apollo-login' ),
			),
			201
		);
	}

	/**
	 * Logout endpoint
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function logout( WP_REST_Request $request ): WP_REST_Response {
		wp_logout();

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Logout successful', 'apollo-login' ),
			),
			200
		);
	}

	/**
	 * Password reset request endpoint
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function reset_request( WP_REST_Request $request ) {
		$email = $request->get_param( 'email' );

		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			return new WP_Error(
				'user_not_found',
				__( 'User not found', 'apollo-login' ),
				array( 'status' => 404 )
			);
		}

		// Generate reset token
		$token = wp_generate_password( 32, false );
		update_user_meta( $user->ID, '_apollo_password_reset_token', $token );
		update_user_meta( $user->ID, '_apollo_password_reset_expires', time() + DAY_IN_SECONDS );

		// TODO: Send email with reset link

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Reset email sent', 'apollo-login' ),
			),
			200
		);
	}

	/**
	 * Check username availability
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function check_username( WP_REST_Request $request ): WP_REST_Response {
		$username  = $request->get_param( 'username' );
		$available = ! username_exists( $username );

		return new WP_REST_Response(
			array(
				'available' => $available,
				'username'  => $username,
			),
			200
		);
	}

	/**
	 * Check email availability
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function check_email( WP_REST_Request $request ): WP_REST_Response {
		$email     = $request->get_param( 'email' );
		$available = ! email_exists( $email );

		return new WP_REST_Response(
			array(
				'available' => $available,
				'email'     => $email,
			),
			200
		);
	}
}
