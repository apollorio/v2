<?php

/**
 * Security Headers
 *
 * Sends HTTP security headers on every frontend response:
 * X-Content-Type-Options, X-Frame-Options, X-XSS-Protection,
 * Strict-Transport-Security, Content-Security-Policy, Referrer-Policy,
 * Permissions-Policy, and removes PHP/WP server signature headers.
 *
 * @package Apollo\Login\Security
 */

declare(strict_types=1);

namespace Apollo\Login\Security;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SecurityHeaders class
 */
class SecurityHeaders {


	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'send_headers', array( $this, 'send_security_headers' ) );
		add_action( 'login_init', array( $this, 'send_security_headers' ) );  // also fires on wp-login.php
		add_filter( 'wp_headers', array( $this, 'filter_headers' ) );

		// Remove X-Powered-By (PHP disclosure)
		$this->remove_php_exposure();
	}

	/**
	 * Send security headers via WordPress hooks
	 *
	 * @return void
	 */
	public function send_security_headers(): void {
		if ( headers_sent() ) {
			return;
		}

		// Prevent MIME-type sniffing
		header( 'X-Content-Type-Options: nosniff' );

		// Prevent clickjacking — allow only same-origin iframes
		header( 'X-Frame-Options: SAMEORIGIN' );

		// Legacy XSS protection for older browsers
		header( 'X-XSS-Protection: 1; mode=block' );

		// Referrer policy — don't leak URL path to third parties
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );

		// HSTS — only if HTTPS
		if ( is_ssl() ) {
			header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
		}

		// Permissions Policy — restrict sensitive browser APIs
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()' );

		// Content Security Policy — permissive but blocks known injections
		// Allows Apollo CDN + same-origin. Adapt per site as needed.
		$csp = implode(
			'; ',
			array(
				"default-src 'self'",
				"script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.apollo.rio.br https://www.google.com https://www.gstatic.com https://www.recaptcha.net",
				"style-src 'self' 'unsafe-inline' https://cdn.apollo.rio.br https://fonts.googleapis.com",
				"font-src 'self' https://cdn.apollo.rio.br https://fonts.gstatic.com data:",
				"img-src 'self' data: blob: https:",
				"connect-src 'self' https://cdn.apollo.rio.br https://assets.apollo.rio.br",
				"media-src 'self' https://assets.apollo.rio.br blob: data:",
				"frame-src 'self' https://www.google.com https://www.recaptcha.net https://www.youtube.com https://www.youtube-nocookie.com",
				"object-src 'none'",
				"base-uri 'self'",
				"form-action 'self'",
				'upgrade-insecure-requests',
			)
		);
		header( "Content-Security-Policy: {$csp}" );

		// Remove server info headers
		header_remove( 'X-Powered-By' );
		header_remove( 'Server' );
	}

	/**
	 * Filter WP headers array (adds security headers at wp_send_headers stage)
	 *
	 * @param array $headers
	 * @return array
	 */
	public function filter_headers( array $headers ): array {
		$headers['X-Content-Type-Options'] = 'nosniff';
		$headers['X-Frame-Options']        = 'SAMEORIGIN';
		$headers['X-XSS-Protection']       = '1; mode=block';
		$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';

		// Remove server leakage
		unset( $headers['X-Powered-By'] );

		return $headers;
	}

	/**
	 * Remove PHP version exposure from headers at PHP level
	 *
	 * @return void
	 */
	private function remove_php_exposure(): void {
		if ( function_exists( 'header_remove' ) ) {
			@header_remove( 'X-Powered-By' );
		}
		// Also via INI if available
		if ( function_exists( 'ini_set' ) ) {
			@ini_set( 'expose_php', '0' );
		}
	}
}
