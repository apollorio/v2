<?php
/**
 * Fired during plugin activation.
 *
 * @package Apollo\Core
 * @since   1.0.0
 */

declare(strict_types=1);

namespace Apollo\Core;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class Activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 * Wrapper for the existing ActivationHandler.
 *
 * @since 1.0.0
 */
final class Activation {

	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate(): void {
		// Delegate to existing ActivationHandler if it exists
		if ( class_exists( 'Apollo\\Core\\Core\\ActivationHandler' ) ) {
			Core\ActivationHandler::activate();
			return;
		}

		// Fallback activation logic
		self::check_requirements();
		self::create_tables();
		self::create_options();
		self::clear_cache();

		set_transient( 'apollo_core_activated', true, 30 );
		flush_rewrite_rules();
	}

	/**
	 * Check plugin requirements.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function check_requirements(): void {
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			deactivate_plugins( plugin_basename( APOLLO_CORE_FILE ) );
			wp_die(
				esc_html__( 'Apollo Core requires PHP 7.4 or higher.', 'apollo-core' ),
				esc_html__( 'Plugin Activation Error', 'apollo-core' ),
				array( 'back_link' => true )
			);
		}

		if ( version_compare( get_bloginfo( 'version' ), '5.8', '<' ) ) {
			deactivate_plugins( plugin_basename( APOLLO_CORE_FILE ) );
			wp_die(
				esc_html__( 'Apollo Core requires WordPress 5.8 or higher.', 'apollo-core' ),
				esc_html__( 'Plugin Activation Error', 'apollo-core' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Create database tables.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function create_tables(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Create audit log table
		$table_name = $wpdb->prefix . 'apollo_audit_log';

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT NULL,
            action varchar(100) NOT NULL,
            object_type varchar(50) DEFAULT NULL,
            object_id bigint(20) unsigned DEFAULT NULL,
            details longtext DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY object_type (object_type),
            KEY object_id (object_id),
            KEY created_at (created_at)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Create default options.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function create_options(): void {
		$defaults = array(
			'version'    => defined( 'APOLLO_CORE_VERSION' ) ? APOLLO_CORE_VERSION : '1.0.0',
			'debug_mode' => false,
			'audit_log'  => true,
		);

		if ( false === get_option( 'apollo_core_settings' ) ) {
			add_option( 'apollo_core_settings', $defaults );
		}
	}

	/**
	 * Clear cache.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function clear_cache(): void {
		wp_cache_flush();
		delete_transient( 'apollo_core_registry' );
	}
}
