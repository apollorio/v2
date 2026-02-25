<?php
/**
 * Rate Limiter
 *
 * IP-based rate limiting for login attempts using transients.
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Security;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RateLimiter {

	private const TRANSIENT_PREFIX  = 'apollo_login_attempts_';
	private const LOCKOUT_PREFIX    = 'apollo_login_lockout_';

	public function __construct() {
		// Cleanup cron
		if ( ! wp_next_scheduled( 'apollo_login_cleanup_attempts' ) ) {
			wp_schedule_event( time(), 'hourly', 'apollo_login_cleanup_attempts' );
		}
		add_action( 'apollo_login_cleanup_attempts', [ __CLASS__, 'cleanup_old_attempts' ] );
	}

	public static function record_attempt( string $ip ): void {
		$key     = self::TRANSIENT_PREFIX . md5( $ip );
		$window  = (int) ( defined( 'APOLLO_LOGIN_RATE_LIMIT_WINDOW' ) ? APOLLO_LOGIN_RATE_LIMIT_WINDOW : 300 );
		$current = (int) get_transient( $key );
		set_transient( $key, $current + 1, $window );
	}

	public static function get_attempt_count( string $ip ): int {
		$key = self::TRANSIENT_PREFIX . md5( $ip );
		return (int) get_transient( $key );
	}

	public static function is_locked_out( string $ip ): bool {
		$key     = self::LOCKOUT_PREFIX . md5( $ip );
		$lockout = get_transient( $key );
		return (bool) $lockout;
	}

	public static function lock_out( string $ip ): void {
		$key      = self::LOCKOUT_PREFIX . md5( $ip );
		$duration = (int) get_option( 'apollo_login_lockout_duration', APOLLO_LOGIN_LOCKOUT_DURATION );
		set_transient( $key, time() + $duration, $duration );
	}

	public static function get_remaining_lockout( string $ip ): int {
		$key     = self::LOCKOUT_PREFIX . md5( $ip );
		$lockout = (int) get_transient( $key );
		if ( ! $lockout ) {
			return 0;
		}
		return max( 0, $lockout - time() );
	}

	public static function clear_attempts( string $ip ): void {
		delete_transient( self::TRANSIENT_PREFIX . md5( $ip ) );
		delete_transient( self::LOCKOUT_PREFIX . md5( $ip ) );
	}

	public static function cleanup_old_attempts(): void {
		global $wpdb;
		$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_LOGIN_ATTEMPTS;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table} WHERE created_at < %s",
				gmdate( 'Y-m-d H:i:s', strtotime( '-30 days' ) )
			)
		);
	}
}
