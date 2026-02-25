<?php
/**
 * Plugin Deactivation Handler
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Deactivation {

	public static function deactivate(): void {
		self::clear_cron();
		self::clear_transients();
	}

	private static function clear_cron(): void {
		wp_clear_scheduled_hook( 'apollo_login_cleanup_attempts' );
		wp_clear_scheduled_hook( 'apollo_login_cleanup_tokens' );
	}

	private static function clear_transients(): void {
		delete_transient( 'apollo_login_rate_limits' );
	}
}
