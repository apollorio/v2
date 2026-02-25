<?php
/**
 * Fired during plugin activation.
 *
 * @package Apollo\Shortcode
 * @since   1.0.0
 */

declare(strict_types=1);

namespace Apollo\Shortcode;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class Activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since 1.0.0
 */
final class Activation {

	/**
	 * Activate the plugin.
	 *
	 * Creates necessary database tables, sets default options,
	 * and performs any other activation tasks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate(): void {
		// Check minimum requirements
		self::check_requirements();

		// Create default options
		self::create_options();

		// Clear any cached data
		self::clear_cache();

		// Set activation flag for redirect
		set_transient( 'apollo_shortcode_activated', true, 30 );

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Check plugin requirements.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function check_requirements(): void {
		// PHP version check
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			deactivate_plugins( plugin_basename( APOLLO_SHORTCODE_FILE ) );
			wp_die(
				esc_html__( 'Apollo Shortcodes requires PHP 7.4 or higher.', 'apollo-shortcodes' ),
				esc_html__( 'Plugin Activation Error', 'apollo-shortcodes' ),
				array( 'back_link' => true )
			);
		}

		// WordPress version check
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '<' ) ) {
			deactivate_plugins( plugin_basename( APOLLO_SHORTCODE_FILE ) );
			wp_die(
				esc_html__( 'Apollo Shortcodes requires WordPress 5.8 or higher.', 'apollo-shortcodes' ),
				esc_html__( 'Plugin Activation Error', 'apollo-shortcodes' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Create default plugin options.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function create_options(): void {
		$defaults = array(
			'version'          => APOLLO_SHORTCODE_VERSION,
			'enable_legacy'    => false,
			'cache_shortcodes' => true,
			'debug_mode'       => false,
		);

		if ( false === get_option( 'apollo_shortcode_settings' ) ) {
			add_option( 'apollo_shortcode_settings', $defaults );
		}
	}

	/**
	 * Clear plugin cache.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function clear_cache(): void {
		wp_cache_delete( 'apollo_shortcode_registered', 'apollo' );
		delete_transient( 'apollo_shortcode_list' );
	}
}
