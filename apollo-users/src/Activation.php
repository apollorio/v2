<?php
/**
 * Plugin Activation Handler
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users;

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

		// Setup roles and capabilities
		self::setup_roles();

		// Set default options
		self::set_defaults();

		// Create default pages
		self::create_pages();

		// Set activation flag
		update_option( 'apollo_users_activated', time() );
		update_option( 'apollo_users_version', APOLLO_USERS_VERSION );
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

		// Matchmaking table
		$table_matchmaking = $wpdb->prefix . APOLLO_USERS_TABLE_MATCHMAKING;
		$sql_matchmaking   = "CREATE TABLE IF NOT EXISTS {$table_matchmaking} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id bigint(20) UNSIGNED NOT NULL,
			preference_key varchar(100) NOT NULL,
			preference_value text,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY preference_key (preference_key)
		) {$charset_collate};";
		dbDelta( $sql_matchmaking );

		// User fields table
		$table_fields = $wpdb->prefix . APOLLO_USERS_TABLE_FIELDS;
		$sql_fields   = "CREATE TABLE IF NOT EXISTS {$table_fields} (
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
		) {$charset_collate};";
		dbDelta( $sql_fields );

		// Profile views table
		$table_views = $wpdb->prefix . APOLLO_USERS_TABLE_PROFILE_VIEWS;
		$sql_views   = "CREATE TABLE IF NOT EXISTS {$table_views} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			profile_user_id bigint(20) UNSIGNED NOT NULL,
			viewer_user_id bigint(20) UNSIGNED DEFAULT NULL,
			viewer_ip varchar(45) DEFAULT '',
			viewed_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY profile_user_id (profile_user_id),
			KEY viewer_user_id (viewer_user_id),
			KEY viewed_at (viewed_at)
		) {$charset_collate};";
		dbDelta( $sql_views );

		// ════════ User ratings table ════════
		$table_ratings = $wpdb->prefix . APOLLO_USERS_TABLE_RATINGS;
		$sql_ratings   = "CREATE TABLE IF NOT EXISTS {$table_ratings} (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			voter_id bigint(20) UNSIGNED NOT NULL,
			target_id bigint(20) UNSIGNED NOT NULL,
			category varchar(50) NOT NULL,
			score tinyint(1) UNSIGNED DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY unique_vote (voter_id, target_id, category),
			KEY target_id (target_id),
			KEY voter_id (voter_id)
		) {$charset_collate};";
		dbDelta( $sql_ratings );
	}

	/**
	 * Setup Apollo roles and capabilities (Registry compliance)
	 *
	 * @return void
	 */
	private static function setup_roles(): void {
		// Get default subscriber capabilities
		$subscriber      = get_role( 'subscriber' );
		$subscriber_caps = $subscriber ? $subscriber->capabilities : array();

		// Apollo Member - Default verified user
		add_role(
			'apollo_member',
			__( 'Apollo Member', 'apollo-users' ),
			array_merge(
				$subscriber_caps,
				array(
					'read'                    => true,
					'edit_posts'              => false,
					'apollo_view_profiles'    => true,
					'apollo_edit_own_profile' => true,
					'apollo_rate_users'       => true,
				)
			)
		);

		// Apollo Producer - Event/Content creator
		add_role(
			'apollo_producer',
			__( 'Apollo Producer', 'apollo-users' ),
			array_merge(
				$subscriber_caps,
				array(
					'read'                     => true,
					'edit_posts'               => true,
					'publish_posts'            => true,
					'apollo_view_profiles'     => true,
					'apollo_edit_own_profile'  => true,
					'apollo_rate_users'        => true,
					'apollo_create_events'     => true,
					'apollo_edit_own_events'   => true,
					'apollo_delete_own_events' => true,
				)
			)
		);

		// Apollo DJ - Music professional
		add_role(
			'apollo_dj',
			__( 'Apollo DJ', 'apollo-users' ),
			array_merge(
				$subscriber_caps,
				array(
					'read'                     => true,
					'edit_posts'               => true,
					'apollo_view_profiles'     => true,
					'apollo_edit_own_profile'  => true,
					'apollo_rate_users'        => true,
					'apollo_manage_dj_profile' => true,
				)
			)
		);

		// Apollo Venue - Venue/Location owner
		add_role(
			'apollo_venue',
			__( 'Apollo Venue', 'apollo-users' ),
			array_merge(
				$subscriber_caps,
				array(
					'read'                          => true,
					'edit_posts'                    => true,
					'apollo_view_profiles'          => true,
					'apollo_edit_own_profile'       => true,
					'apollo_rate_users'             => true,
					'apollo_manage_venue'           => true,
					'apollo_create_events_at_venue' => true,
				)
			)
		);

		// Apollo Moderator - Content moderator
		add_role(
			'apollo_moderator',
			__( 'Apollo Moderator', 'apollo-users' ),
			array_merge(
				$subscriber_caps,
				array(
					'read'                    => true,
					'edit_posts'              => true,
					'edit_others_posts'       => true,
					'delete_posts'            => true,
					'delete_others_posts'     => true,
					'apollo_view_profiles'    => true,
					'apollo_edit_own_profile' => true,
					'apollo_moderate_content' => true,
					'apollo_moderate_users'   => true,
					'apollo_view_reports'     => true,
				)
			)
		);

		// Add Apollo capabilities to Administrator
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$apollo_caps = array(
				'apollo_view_profiles',
				'apollo_edit_own_profile',
				'apollo_edit_all_profiles',
				'apollo_rate_users',
				'apollo_create_events',
				'apollo_edit_own_events',
				'apollo_edit_all_events',
				'apollo_delete_own_events',
				'apollo_delete_all_events',
				'apollo_manage_dj_profile',
				'apollo_manage_venue',
				'apollo_moderate_content',
				'apollo_moderate_users',
				'apollo_view_reports',
			);

			foreach ( $apollo_caps as $cap ) {
				$admin->add_cap( $cap );
			}
		}
	}

	/**
	 * Set default options
	 *
	 * @return void
	 */
	private static function set_defaults(): void {
		// Default settings
		add_option( 'apollo_users_profile_slug', 'id' );
		add_option( 'apollo_users_radar_slug', 'radar' );
		add_option( 'apollo_users_block_author_enum', true );
		add_option( 'apollo_users_default_privacy', 'public' );
	}

	/**
	 * Create default pages
	 *
	 * @return void
	 */
	private static function create_pages(): void {
		// Create radar page
		$radar_page = get_page_by_path( 'radar' );
		if ( ! $radar_page ) {
			wp_insert_post(
				array(
					'post_title'   => 'Radar de Usuários',
					'post_name'    => 'radar',
					'post_content' => '[apollo_radar]',
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);
		}
	}
}
