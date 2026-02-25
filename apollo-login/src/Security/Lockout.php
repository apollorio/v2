<?php

/**
 * Lockout Handler
 *
 * Manages both user-level lockouts (via user meta, handled in LoginHandler)
 * and IP-level lockouts (via transients + Firewall blacklist).
 *
 * Also provides admin utilities to inspect, unlock users and IPs.
 *
 * @package Apollo\Login\Security
 */

declare(strict_types=1);

namespace Apollo\Login\Security;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lockout class
 */
class Lockout {


	/** User meta key for lockout expiry */
	private const META_LOCKOUT = '_apollo_lockout_until';

	/** User meta key for failed attempt count */
	private const META_ATTEMPTS = '_apollo_login_attempts';

	/** Max allowed consecutive failures before user-level lockout */
	private int $max_attempts;

	/** Lockout duration in seconds */
	private int $lockout_duration;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->max_attempts     = (int) ( defined( 'APOLLO_LOGIN_MAX_ATTEMPTS' ) ? APOLLO_LOGIN_MAX_ATTEMPTS : get_option( 'apollo_lockout_max_attempts', 5 ) );
		$this->lockout_duration = (int) ( defined( 'APOLLO_LOGIN_LOCKOUT_DURATION' ) ? APOLLO_LOGIN_LOCKOUT_DURATION : get_option( 'apollo_lockout_duration', 3600 ) );

		// Admin AJAX handlers for managing lockouts
		add_action( 'wp_ajax_apollo_unlock_user', array( $this, 'ajax_unlock_user' ) );
		add_action( 'wp_ajax_apollo_unlock_ip', array( $this, 'ajax_unlock_ip' ) );

		// Action hook: auto-lockout on failed login (supplementary to LoginHandler)
		add_action( 'apollo/login/failed_attempt', array( $this, 'on_failed_attempt' ), 10, 2 );
	}

	// ─────────────────────────────────────────────────────────────────────────
	// USER LOCKOUT
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * Check if a user is currently locked out
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public static function is_user_locked( int $user_id ): bool {
		$until = (int) get_user_meta( $user_id, self::META_LOCKOUT, true );
		return $until > time();
	}

	/**
	 * Get remaining lockout seconds for a user
	 *
	 * @param int $user_id
	 * @return int
	 */
	public static function user_lockout_remaining( int $user_id ): int {
		$until = (int) get_user_meta( $user_id, self::META_LOCKOUT, true );
		return max( 0, $until - time() );
	}

	/**
	 * Lock a user
	 *
	 * @param int $user_id
	 * @param int $duration  Seconds. Default: plugin setting.
	 * @return void
	 */
	public function lock_user( int $user_id, int $duration = 0 ): void {
		$d = $duration > 0 ? $duration : $this->lockout_duration;
		update_user_meta( $user_id, self::META_LOCKOUT, time() + $d );
		update_user_meta( $user_id, self::META_ATTEMPTS, 0 );

		do_action( 'apollo/security/user_locked', $user_id, $d );
	}

	/**
	 * Unlock a user
	 *
	 * @param int $user_id
	 * @return void
	 */
	public function unlock_user( int $user_id ): void {
		delete_user_meta( $user_id, self::META_LOCKOUT );
		delete_user_meta( $user_id, self::META_ATTEMPTS );

		do_action( 'apollo/security/user_unlocked', $user_id );
	}

	/**
	 * Increment failed attempt counter; auto-lock when threshold exceeded
	 *
	 * @param int    $user_id
	 * @param string $ip
	 * @return void
	 */
	public function on_failed_attempt( int $user_id, string $ip ): void {
		$attempts = (int) get_user_meta( $user_id, self::META_ATTEMPTS, true );
		++$attempts;
		update_user_meta( $user_id, self::META_ATTEMPTS, $attempts );

		if ( $attempts >= $this->max_attempts ) {
			$this->lock_user( $user_id );
		}
	}

	/**
	 * Get locked users list (for admin view)
	 *
	 * @return \WP_User[]
	 */
	public static function get_locked_users(): array {
		$users = get_users(
			array(
				'meta_key'     => self::META_LOCKOUT,
				'meta_compare' => 'EXISTS',
				'number'       => 100,
			)
		);

		return array_filter( $users, fn( $u ) => self::is_user_locked( $u->ID ) );
	}

	// ─────────────────────────────────────────────────────────────────────────
	// IP LOCKOUT (delegates to Firewall)
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * Temporarily lock an IP address
	 *
	 * @param string $ip
	 * @param int    $duration  Seconds.
	 * @return void
	 */
	public static function lock_ip( string $ip, int $duration = 3600 ): void {
		Firewall::temp_blacklist_ip( $ip, $duration );
		do_action( 'apollo/security/ip_locked', $ip, $duration );
	}

	/**
	 * Permanently blacklist an IP
	 *
	 * @param string $ip
	 * @return void
	 */
	public static function blacklist_ip( string $ip ): void {
		Firewall::blacklist_ip( $ip );
	}

	/**
	 * Remove IP from blacklist
	 *
	 * @param string $ip
	 * @return void
	 */
	public static function unblacklist_ip( string $ip ): void {
		Firewall::unblacklist_ip( $ip );
	}

	// ─────────────────────────────────────────────────────────────────────────
	// AJAX HANDLERS
	// ─────────────────────────────────────────────────────────────────────────

	/**
	 * AJAX: unlock a user by ID
	 *
	 * @return void
	 */
	public function ajax_unlock_user(): void {
		check_ajax_referer( 'apollo_lockout_nonce', 'nonce' );

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$user_id = absint( $_POST['user_id'] ?? 0 );
		if ( $user_id ) {
			$this->unlock_user( $user_id );
			wp_send_json_success( "User #{$user_id} unlocked." );
		}

		wp_send_json_error( 'Invalid user ID.' );
	}

	/**
	 * AJAX: unlock / remove IP from blacklist
	 *
	 * @return void
	 */
	public function ajax_unlock_ip(): void {
		check_ajax_referer( 'apollo_lockout_nonce', 'nonce' );

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		$ip = sanitize_text_field( wp_unslash( $_POST['ip'] ?? '' ) );
		if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			self::unblacklist_ip( $ip );
			wp_send_json_success( "IP {$ip} removed from blacklist." );
		}

		wp_send_json_error( 'Invalid IP.' );
	}
}
