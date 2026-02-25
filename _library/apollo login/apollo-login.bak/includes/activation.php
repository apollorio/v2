<?php
/**
 * Activation Handler
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Core;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activation class
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

		// Flush rewrite rules
		flush_rewrite_rules();

		// Set default options
		self::set_default_options();
	}

	/**
	 * Create database tables
	 *
	 * @return void
	 */
	private static function create_tables(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Table 1: apollo_quiz_results
		$table_quiz = $wpdb->prefix . \APOLLO_LOGIN_TABLE_QUIZ_RESULTS;
		$sql_quiz = "CREATE TABLE {$table_quiz} (
			id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			stage VARCHAR(50) NOT NULL COMMENT 'pattern|simon|ethics|reaction',
			score INT NOT NULL,
			answers LONGTEXT COMMENT 'JSON array of answers',
			completed_at DATETIME NOT NULL,
			INDEX user_id (user_id),
			INDEX stage (stage)
		) {$charset_collate};";

		dbDelta( $sql_quiz );

		// Table 2: apollo_simon_scores
		$table_simon = $wpdb->prefix . \APOLLO_LOGIN_TABLE_SIMON_SCORES;
		$sql_simon = "CREATE TABLE {$table_simon} (
			id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			level INT NOT NULL COMMENT '1-4',
			sequence LONGTEXT NOT NULL COMMENT 'JSON array of colors',
			success TINYINT(1) NOT NULL DEFAULT 0,
			attempts INT NOT NULL DEFAULT 1,
			created_at DATETIME NOT NULL,
			INDEX user_id (user_id),
			INDEX level (level)
		) {$charset_collate};";

		dbDelta( $sql_simon );

		// Table 3: apollo_login_attempts
		$table_attempts = $wpdb->prefix . \APOLLO_LOGIN_TABLE_LOGIN_ATTEMPTS;
		$sql_attempts = "CREATE TABLE {$table_attempts} (
			id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			username VARCHAR(255) NOT NULL,
			ip_address VARCHAR(45) NOT NULL,
			success TINYINT(1) NOT NULL DEFAULT 0,
			user_agent TEXT,
			attempted_at DATETIME NOT NULL,
			INDEX username (username),
			INDEX ip_address (ip_address),
			INDEX attempted_at (attempted_at)
		) {$charset_collate};";

		dbDelta( $sql_attempts );

		// Table 4: apollo_url_rewrites
		$table_rewrites = $wpdb->prefix . \APOLLO_LOGIN_TABLE_URL_REWRITES;
		$sql_rewrites = "CREATE TABLE {$table_rewrites} (
			id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			original_url VARCHAR(255) NOT NULL UNIQUE,
			rewrite_url VARCHAR(255) NOT NULL UNIQUE,
			type VARCHAR(50) NOT NULL COMMENT 'login|admin|author',
			active TINYINT(1) NOT NULL DEFAULT 1,
			created_at DATETIME NOT NULL,
			INDEX type (type),
			INDEX active (active)
		) {$charset_collate};";

		dbDelta( $sql_rewrites );

		// Insert default URL rewrites
		self::insert_default_rewrites();
	}

	/**
	 * Insert default URL rewrites
	 *
	 * @return void
	 */
	private static function insert_default_rewrites(): void {
		global $wpdb;

		$table = $wpdb->prefix . \APOLLO_LOGIN_TABLE_URL_REWRITES;

		// Check if already exists
		$exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE original_url = %s",
				'wp-login.php'
			)
		);

		if ( $exists ) {
			return;
		}

		// Insert default rewrites
		$wpdb->insert(
			$table,
			array(
				'original_url' => 'wp-login.php',
				'rewrite_url'  => \APOLLO_LOGIN_CUSTOM_LOGIN_SLUG,
				'type'         => 'login',
				'active'       => 1,
				'created_at'   => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%d', '%s' )
		);
	}

	/**
	 * Set default options
	 *
	 * @return void
	 */
	private static function set_default_options(): void {
		$defaults = array(
			'apollo_login_version'           => \APOLLO_LOGIN_VERSION,
			'apollo_login_quiz_mandatory'    => true,
			'apollo_login_max_attempts'      => \APOLLO_LOGIN_MAX_ATTEMPTS,
			'apollo_login_lockout_duration'  => \APOLLO_LOGIN_LOCKOUT_DURATION,
			'apollo_login_custom_login_slug' => \APOLLO_LOGIN_CUSTOM_LOGIN_SLUG,
			'apollo_login_hide_wp_login'     => true,
		);

		foreach ( $defaults as $key => $value ) {
			if ( false === get_option( $key ) ) {
				add_option( $key, $value );
			}
		}
	}
}
