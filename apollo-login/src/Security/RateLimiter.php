<?php

/**
 * Rate Limiter
 *
 * IP-based rate limiting using WordPress transients.
 * After N failed login attempts from one IP within a window,
 * the IP is temporarily blacklisted via Firewall::temp_blacklist_ip().
 *
 * Thresholds (configurable via constants or wp_options):
 *   APOLLO_RATE_LIMIT_MAX_HITS   — max requests in window (default 20)
 *   APOLLO_RATE_LIMIT_WINDOW     — window in seconds (default 60)
 *   APOLLO_RATE_LIMIT_BLOCK_TIME — block duration in seconds (default 3600 = 1h)
 *
 * @package Apollo\Login\Security
 */

declare(strict_types=1);

namespace Apollo\Login\Security;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rate Limiter class
 */
class RateLimiter {


	/** Max failed login attempts per window before temporary block */
	private int $max_login_failures;

	/** Window length in seconds */
	private int $window;

	/** Block duration in seconds after threshold exceeded */
	private int $block_time;

	/** Transient key prefix for hit counters */
	private const PREFIX = 'apollo_rl_';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->max_login_failures = (int) ( defined( 'APOLLO_RATE_LIMIT_MAX_HITS' ) ? APOLLO_RATE_LIMIT_MAX_HITS : get_option( 'apollo_rate_max_hits', 20 ) );
		$this->window             = (int) ( defined( 'APOLLO_RATE_LIMIT_WINDOW' ) ? APOLLO_RATE_LIMIT_WINDOW : get_option( 'apollo_rate_window', 60 ) );
		$this->block_time         = (int) ( defined( 'APOLLO_RATE_LIMIT_BLOCK_TIME' ) ? APOLLO_RATE_LIMIT_BLOCK_TIME : get_option( 'apollo_rate_block', 3600 ) );

		// Hook into failed logins to track by IP
		add_action( 'wp_login_failed', array( $this, 'on_login_failed' ) );

		// Hook into comment attempts (brute force on comment form)
		add_action( 'pre_comment_on_post', array( $this, 'on_comment_attempt' ) );

		// Also check on every login page load (too many page loads = bot)
		add_action( 'login_init', array( $this, 'on_login_page_load' ) );
	}

	/**
	 * Fired on every failed login — increment IP counter
	 *
	 * @param string $username
	 * @return void
	 */
	public function on_login_failed( string $username ): void {
		$ip  = Firewall::get_client_ip();
		$key = self::PREFIX . 'fail_' . md5( $ip );

		$hits = (int) get_transient( $key );
		++$hits;

		set_transient( $key, $hits, $this->window );

		if ( $hits >= $this->max_login_failures ) {
			Firewall::temp_blacklist_ip( $ip, $this->block_time );
			do_action( 'apollo/security/rate_limited', $ip, $username, $hits );
		}
	}

	/**
	 * Fired on every login page load — track repeated page hits by IP
	 * (bot mitigation: too many login page loads = suspicious)
	 *
	 * @return void
	 */
	public function on_login_page_load(): void {
		// Only count GET requests to avoid interfering with actual POST logins
		if ( ( $_SERVER['REQUEST_METHOD'] ?? '' ) !== 'GET' ) {
			return;
		}

		$ip  = Firewall::get_client_ip();
		$key = self::PREFIX . 'page_' . md5( $ip );

		$hits = (int) get_transient( $key );
		++$hits;

		set_transient( $key, $hits, $this->window );

		// Threshold: 3× the login failure threshold for page loads
		if ( $hits >= ( $this->max_login_failures * 3 ) ) {
			Firewall::temp_blacklist_ip( $ip, $this->block_time );
			do_action( 'apollo/security/rate_limited_page', $ip, $hits );
		}
	}

	/**
	 * Fired on comment submission — rate limit comment spam
	 *
	 * @param int $post_id
	 * @return void
	 */
	public function on_comment_attempt( int $post_id ): void {
		$ip  = Firewall::get_client_ip();
		$key = self::PREFIX . 'cmt_' . md5( $ip );

		$hits = (int) get_transient( $key );
		++$hits;

		set_transient( $key, $hits, $this->window );

		// 5 comment submissions per window is suspicious
		if ( $hits >= 5 ) {
			Firewall::temp_blacklist_ip( $ip, $this->block_time );
		}
	}

	/**
	 * Get current hit count for an IP (useful for debug/admin views)
	 *
	 * @param string $ip    IP address.
	 * @param string $type  'fail' | 'page' | 'cmt'
	 * @return int
	 */
	public function get_hits( string $ip, string $type = 'fail' ): int {
		$key = self::PREFIX . $type . '_' . md5( $ip );
		return (int) get_transient( $key );
	}

	/**
	 * Reset hit counters for an IP
	 *
	 * @param string $ip
	 * @return void
	 */
	public function reset( string $ip ): void {
		foreach ( array( 'fail', 'page', 'cmt' ) as $type ) {
			delete_transient( self::PREFIX . $type . '_' . md5( $ip ) );
		}
	}
}
