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
					'social_name'        => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => function ( $social_name ) {
							return strlen( $social_name ) >= 2;
						},
					),
					'instagram_username' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => function ( $username ) {
							return sanitize_user( str_replace( '@', '', $username ) );
						},
					),
					'username'           => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_user',
					),
					'email'              => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_email',
					),
					'password'           => array(
						'required' => true,
						'type'     => 'string',
					),
					'apollo_quiz_token'  => array(
						'required' => true,
						'type'     => 'string',
					),
					'sounds'             => array(
						'required'          => true,
						'type'              => 'array',
						'items'             => array( 'type' => 'integer' ),
						'sanitize_callback' => function ( $sounds ) {
							return array_map( 'absint', (array) $sounds );
						},
						'validate_callback' => function ( $sounds ) {
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

		// POST /auth/reset-confirm
		register_rest_route(
			$this->namespace,
			'/auth/reset-confirm',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'reset_confirm' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'token'    => array(
						'required' => true,
						'type'     => 'string',
					),
					'password' => array(
						'required' => true,
						'type'     => 'string',
					),
				),
			)
		);

		// POST /auth/verify-email
		register_rest_route(
			$this->namespace,
			'/auth/verify-email',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'verify_email' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'user_id' => array(
						'required' => true,
						'type'     => 'integer',
					),
					'token'   => array(
						'required' => true,
						'type'     => 'string',
					),
				),
			)
		);

		// POST /auth/resend-verification
		register_rest_route(
			$this->namespace,
			'/auth/resend-verification',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'resend_verification' ),
				'permission_callback' => 'is_user_logged_in',
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

		// Redirect target: home page (mural-router serves the mural for logged-in users at /)
		$redirect = apply_filters( 'apollo_login_redirect', home_url( '/' ), $user );

		return new WP_REST_Response(
			array(
				'success'  => true,
				'user_id'  => $user->ID,
				'message'  => __( 'Login successful', 'apollo-login' ),
				'redirect' => $redirect,
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
		$social_name = $request->get_param( 'social_name' );
		$instagram   = $request->get_param( 'instagram_username' );
		$username    = $request->get_param( 'username' );
		$email       = $request->get_param( 'email' );
		$password    = $request->get_param( 'password' );
		$quiz_token  = $request->get_param( 'apollo_quiz_token' );
		$sounds      = $request->get_param( 'sounds' );

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

	/**
	 * Password reset confirm endpoint
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function reset_confirm( WP_REST_Request $request ) {
		$token    = $request->get_param( 'token' );
		$password = $request->get_param( 'password' );

		// Find user by token
		$users = get_users(
			array(
				'meta_key'   => '_apollo_password_reset_token',
				'meta_value' => $token,
				'number'     => 1,
			)
		);

		if ( empty( $users ) ) {
			return new WP_Error(
				'invalid_token',
				__( 'Invalid reset token', 'apollo-login' ),
				array( 'status' => 400 )
			);
		}

		$user = $users[0];

		// Check expiration
		$expires = get_user_meta( $user->ID, '_apollo_password_reset_expires', true );
		if ( time() > $expires ) {
			return new WP_Error(
				'expired_token',
				__( 'Reset token expired', 'apollo-login' ),
				array( 'status' => 400 )
			);
		}

		// Update password
		wp_set_password( $password, $user->ID );

		// Clean up
		delete_user_meta( $user->ID, '_apollo_password_reset_token' );
		delete_user_meta( $user->ID, '_apollo_password_reset_expires' );

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Password reset successful', 'apollo-login' ),
			),
			200
		);
	}

	/**
	 * Verify email endpoint
	 *
	 * Uses the shared apollo_verify_email_token() which includes:
	 * - hash_equals() comparison (timing-safe)
	 * - 24-hour TTL check
	 * - Membership upgrade on success
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function verify_email( WP_REST_Request $request ) {
		$user_id = absint( $request->get_param( 'user_id' ) );
		$token   = sanitize_text_field( $request->get_param( 'token' ) );

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return new WP_Error(
				'user_not_found',
				__( 'Usuário não encontrado.', 'apollo-login' ),
				array( 'status' => 404 )
			);
		}

		// Already verified?
		if ( get_user_meta( $user_id, '_apollo_email_verified', true ) ) {
			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => __( 'Email já verificado.', 'apollo-login' ),
				),
				200
			);
		}

		// Check expiry explicitly for specific error message
		$expiry = (int) get_user_meta( $user_id, '_apollo_verification_token_expiry', true );
		if ( $expiry > 0 && time() > $expiry ) {
			delete_user_meta( $user_id, '_apollo_verification_token' );
			delete_user_meta( $user_id, '_apollo_verification_token_expiry' );
			return new WP_Error(
				'token_expired',
				__( 'Link expirado. Solicite um novo e-mail de verificação.', 'apollo-login' ),
				array( 'status' => 410 )
			);
		}

		// Verify token (hash_equals + TTL + marks verified + fires hook)
		if ( \Apollo\Login\apollo_verify_email_token( $user_id, $token ) ) {
			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => __( 'Email verificado com sucesso! Bem-vindo(a) ao Apollo.', 'apollo-login' ),
				),
				200
			);
		}

		return new WP_Error(
			'invalid_token',
			__( 'Token de verificação inválido.', 'apollo-login' ),
			array( 'status' => 400 )
		);
	}

	/**
	 * Resend verification email endpoint
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function resend_verification( WP_REST_Request $request ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return new WP_Error(
				'not_logged_in',
				__( 'Faça login para solicitar verificação.', 'apollo-login' ),
				array( 'status' => 401 )
			);
		}

		// Check if already verified
		if ( get_user_meta( $user_id, '_apollo_email_verified', true ) ) {
			return new WP_Error(
				'already_verified',
				__( 'Email já verificado.', 'apollo-login' ),
				array( 'status' => 400 )
			);
		}

		// Rate-limit: max 3 resends per hour
		$transient_key = 'apollo_resend_verify_' . $user_id;
		$resend_count  = (int) get_transient( $transient_key );
		if ( $resend_count >= 3 ) {
			return new WP_Error(
				'rate_limited',
				__( 'Limite de reenvios atingido. Aguarde 1 hora.', 'apollo-login' ),
				array( 'status' => 429 )
			);
		}
		set_transient( $transient_key, $resend_count + 1, HOUR_IN_SECONDS );

		// Generate new token (24h TTL)
		$token = \Apollo\Login\apollo_generate_verification_token( $user_id );

		$verify_url = add_query_arg(
			array(
				'user'  => $user_id,
				'token' => $token,
			),
			home_url( '/verificar-email/' )
		);

		// Fire hook for apollo-email to send
		do_action( 'apollo/login/verification_email', $user_id, $verify_url );

		// Fallback if apollo-email not active
		if ( ! class_exists( 'Apollo\\Email\\Plugin' ) ) {
			$user    = get_userdata( $user_id );
			$subject = __( 'Apollo::Rio — Confirme seu e-mail', 'apollo-login' );
			$message = sprintf(
				__( "Olá %1\$s,\n\nConfirme seu e-mail clicando no link abaixo:\n\n%2\$s\n\nEste link expira em 24 horas.\n\n— Apollo::Rio", 'apollo-login' ),
				$user->display_name,
				$verify_url
			);
			wp_mail( $user->user_email, $subject, $message, array( 'Content-Type: text/plain; charset=UTF-8' ) );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'E-mail de verificação reenviado.', 'apollo-login' ),
			),
			200
		);
	}
}
