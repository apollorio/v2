<?php
/**
 * Plugin Activation Handler
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {

	public static function activate(): void {
		self::create_tables();
		self::set_defaults();
		update_option( 'apollo_users_activated', time() );
		update_option( 'apollo_users_version', APOLLO_USERS_VERSION );
	}

	private static function create_tables(): void {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Matchmaking table
		$t = $wpdb->prefix . APOLLO_USERS_TABLE_MATCHMAKING;
		dbDelta( "CREATE TABLE IF NOT EXISTS {$t} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id bigint(20) UNSIGNED NOT NULL,
			preference_key varchar(100) NOT NULL,
			preference_value text,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY preference_key (preference_key)
		) {$charset_collate};" );

		// User fields table
		$t = $wpdb->prefix . APOLLO_USERS_TABLE_FIELDS;
		dbDelta( "CREATE TABLE IF NOT EXISTS {$t} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id bigint(20) UNSIGNED NOT NULL,
			field_key varchar(100) NOT NULL,
			field_value longtext,
			field_type varchar(50) DEFAULT 'text',
			is_public tinyint(1) DEFAULT 1,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY user_field (user_id, field_key),
			KEY user_id (user_id)
		) {$charset_collate};" );

		// Profile views table
		$t = $wpdb->prefix . APOLLO_USERS_TABLE_PROFILE_VIEWS;
		dbDelta( "CREATE TABLE IF NOT EXISTS {$t} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			profile_user_id bigint(20) UNSIGNED NOT NULL,
			viewer_user_id bigint(20) UNSIGNED DEFAULT NULL,
			viewer_ip varchar(45) DEFAULT '',
			viewed_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY profile_user_id (profile_user_id),
			KEY viewer_user_id (viewer_user_id),
			KEY viewed_at (viewed_at)
		) {$charset_collate};" );

		// User ratings table (NEW)
		$t = $wpdb->prefix . APOLLO_USERS_TABLE_RATINGS;
		dbDelta( "CREATE TABLE IF NOT EXISTS {$t} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			voter_id bigint(20) UNSIGNED NOT NULL,
			target_id bigint(20) UNSIGNED NOT NULL,
			category varchar(50) NOT NULL,
			score tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY unique_vote (voter_id, target_id, category),
			KEY target_id (target_id),
			KEY voter_id (voter_id),
			KEY category (category)
		) {$charset_collate};" );
	}

	private static function set_defaults(): void {
		add_option( 'apollo_users_profile_slug', 'id' );
		add_option( 'apollo_users_radar_slug', 'radar' );
		add_option( 'apollo_users_block_author_enum', true );
		add_option( 'apollo_users_default_privacy', 'public' );
	}
}
