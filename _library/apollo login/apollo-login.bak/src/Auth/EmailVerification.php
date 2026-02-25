<?php
/**
 * Email Verification Handler
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
 * Email Verification class
 */
class EmailVerification {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'process_verification' ) );
	}

	/**
	 * Process email verification
	 *
	 * @return void
	 */
	public function process_verification(): void {
		// Only on verification page
		if ( ! is_page() || get_query_var( 'apollo_login_page' ) !== 'verify-email' ) {
			return;
		}

		$user_id = absint( $_GET['user'] ?? 0 );
		$token   = sanitize_text_field( $_GET['token'] ?? '' );

		if ( ! $user_id || ! $token ) {
			return;
		}

		// Verify token
		if ( \Apollo\Login\apollo_verify_email_token( $user_id, $token ) ) {
			// Success - redirect to dashboard or login
			wp_redirect( add_query_arg( 'verified', 'success', home_url( '/entre/' ) ) );
			exit;
		}
	}
}
