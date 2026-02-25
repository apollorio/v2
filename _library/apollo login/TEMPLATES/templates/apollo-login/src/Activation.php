<?php
/**
 * Plugin Activation Handler
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {

	public static function activate(): void {
		self::create_tables();
		self::set_defaults();
		self::create_pages();
		update_option( 'apollo_login_activated', time() );
	}

	private static function create_tables(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// apollo_quiz_results
		$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_QUIZ_RESULTS;
		$sql   = "CREATE TABLE IF NOT EXISTS {$table} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id bigint(20) UNSIGNED NOT NULL,
			test_type varchar(50) NOT NULL DEFAULT '',
			score int(11) NOT NULL DEFAULT 0,
			answers longtext,
			passed tinyint(1) NOT NULL DEFAULT 0,
			ip_address varchar(45) DEFAULT '',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY test_type (test_type)
		) {$charset_collate};";
		dbDelta( $sql );

		// apollo_simon_scores
		$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_SIMON_SCORES;
		$sql   = "CREATE TABLE IF NOT EXISTS {$table} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id bigint(20) UNSIGNED NOT NULL,
			score int(11) NOT NULL DEFAULT 0,
			max_level int(11) NOT NULL DEFAULT 0,
			time_ms int(11) DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY score (score)
		) {$charset_collate};";
		dbDelta( $sql );

		// apollo_login_attempts
		$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_LOGIN_ATTEMPTS;
		$sql   = "CREATE TABLE IF NOT EXISTS {$table} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			ip_address varchar(45) NOT NULL DEFAULT '',
			username varchar(200) DEFAULT '',
			user_agent text,
			status enum('success','failed','locked') NOT NULL DEFAULT 'failed',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY ip_address (ip_address),
			KEY status (status),
			KEY created_at (created_at)
		) {$charset_collate};";
		dbDelta( $sql );

		// apollo_url_rewrites
		$table = $wpdb->prefix . APOLLO_LOGIN_TABLE_URL_REWRITES;
		$sql   = "CREATE TABLE IF NOT EXISTS {$table} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			original_url varchar(500) NOT NULL DEFAULT '',
			rewrite_url varchar(500) NOT NULL DEFAULT '',
			is_active tinyint(1) NOT NULL DEFAULT 1,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY is_active (is_active)
		) {$charset_collate};";
		dbDelta( $sql );
	}

	private static function set_defaults(): void {
		add_option( 'apollo_login_custom_url', APOLLO_LOGIN_PAGE_ACESSO );
		add_option( 'apollo_login_hide_wp_login', true );
		add_option( 'apollo_login_hide_wp_admin', true );
		add_option( 'apollo_login_max_attempts', APOLLO_LOGIN_MAX_ATTEMPTS );
		add_option( 'apollo_login_lockout_duration', APOLLO_LOGIN_LOCKOUT_DURATION );
		add_option( 'apollo_login_require_quiz', true );
		add_option( 'apollo_login_require_email_verification', true );
		add_option( 'apollo_login_redirect_after_login', '/mural/' );
		add_option( 'apollo_login_recaptcha_enabled', false );
		add_option( 'apollo_login_recaptcha_site_key', '' );
		add_option( 'apollo_login_recaptcha_secret_key', '' );
	}

	private static function create_pages(): void {
		// Virtual pages are handled by rewrite rules, no WP pages needed.
		// But flush rewrite rules to register our custom endpoints.
		flush_rewrite_rules();
	}
}
