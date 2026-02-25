<?php
/**
 * Plugin Activation Handler
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
 * Activation Handler
 */
class Activation {

	/**
	 * Run activation tasks
	 *
	 * @return void
	 */
	public static function activate(): void {
		// Create database tables
		self::create_tables();

		// Set default options
		self::set_defaults();

		// Set activation flag
		update_option( 'apollo_{slug}_activated', time() );
	}

	/**
	 * Create database tables
	 *
	 * @return void
	 */
	private static function create_tables(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Example table creation
		// $table_name = $wpdb->prefix . APOLLO_{CONST}_TABLE_EXAMPLE;
		// $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
		//     id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		//     created_at datetime DEFAULT CURRENT_TIMESTAMP,
		//     PRIMARY KEY (id)
		// ) {$charset_collate};";

		// require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		// dbDelta( $sql );
	}

	/**
	 * Set default options
	 *
	 * @return void
	 */
	private static function set_defaults(): void {
		// Set your default options here
		// add_option( 'apollo_{slug}_setting', 'default_value' );
	}
}
