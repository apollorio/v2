<?php
/**
 * Login Handler
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Auth;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login Handler class
 */
class LoginHandler {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'authenticate', array( $this, 'check_lockout' ), 30, 3 );
		add_action( 'wp_login_failed', array( $this, 'on_login_failed' ) );
		add_action( 'wp_login', array( $this, 'on_login_success' ), 10, 2 );
	}

	/**
	 * Check if user is locked out before authentication
	 *
	 * @param \WP_User|\WP_Error|null $user     User object or error.
	 * @param string                   $username Username.
	 * @param string                   $password Password.
	 * @return \WP_User|\WP_Error
	 */
	public function check_lockout( $user, string $username, string $password ) {
		// Skip if already an error or no username
		if ( is_wp_error( $user ) || empty( $username ) ) {
			return $user;
		}

		// Get user by username or email
		$user_obj = get_user_by( 'login', $username );
		if ( ! $user_obj ) {
			$user_obj = get_user_by( 'email', $username );
		}

		if ( ! $user_obj ) {
			return $user;
		}

		// Check lockout status
		if ( \Apollo\Login\apollo_is_locked_out( $user_obj->ID ) ) {
			$remaining = \Apollo\Login\apollo_lockout_remaining( $user_obj->ID );

			return new \WP_Error(
				'locked_out',
				sprintf(
					__( 'Account locked. Try again in %d seconds.', 'apollo-login' ),
					$remaining
				)
			);
		}

		return $user;
	}

	/**
	 * Handle failed login
	 *
	 * @param string $username Username or email.
	 * @return void
	 */
	public function on_login_failed( string $username ): void {
		// Log the attempt
		\Apollo\Login\apollo_log_login_attempt( $username, false );

		// Get failed attempts count
		$attempts = \Apollo\Login\apollo_get_failed_attempts( $username );

		// Check if should lock out
		if ( $attempts >= APOLLO_LOGIN_MAX_ATTEMPTS ) {
			// Get user
			$user = get_user_by( 'login', $username );
			if ( ! $user ) {
				$user = get_user_by( 'email', $username );
			}

			if ( $user ) {
				// Set lockout
				$lockout_until = time() + APOLLO_LOGIN_LOCKOUT_DURATION;
				update_user_meta( $user->ID, '_apollo_lockout_until', $lockout_until );

				// Reset attempts counter
				update_user_meta( $user->ID, '_apollo_login_attempts', 0 );
			}
		}
	}

	/**
	 * Handle successful login
	 *
	 * @param string   $username Username.
	 * @param \WP_User $user     User object.
	 * @return void
	 */
	public function on_login_success( string $username, \WP_User $user ): void {
		// Log successful login
		\Apollo\Login\apollo_log_login_attempt( $username, true );

		// Clear lockout
		delete_user_meta( $user->ID, '_apollo_lockout_until' );
		delete_user_meta( $user->ID, '_apollo_login_attempts' );

		// Update last login time
		update_user_meta( $user->ID, '_apollo_last_login', current_time( 'mysql' ) );
	}
}
