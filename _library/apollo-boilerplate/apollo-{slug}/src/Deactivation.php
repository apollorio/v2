<?php
/**
 * Plugin Deactivation Handler
 *
 * @package Apollo\{Namespace}
 */

declare(strict_types=1);

namespace Apollo\{Namespace};

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deactivation Handler
 */
class Deactivation {

	/**
	 * Run deactivation tasks
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// Clear scheduled events
		self::clear_cron();

		// Clear transients
		self::clear_transients();

		// Note: We do NOT delete data on deactivation
		// Data is only deleted on uninstall (uninstall.php)
	}

	/**
	 * Clear scheduled cron events
	 *
	 * @return void
	 */
	private static function clear_cron(): void {
		// wp_clear_scheduled_hook( 'apollo_{slug}_cron_event' );
	}

	/**
	 * Clear transients
	 *
	 * @return void
	 */
	private static function clear_transients(): void {
		// delete_transient( 'apollo_{slug}_cache' );
	}
}
