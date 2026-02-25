<?php
declare(strict_types=1);
namespace Apollo\Groups;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Activation {
	public static function activate(): void {
		global $wpdb;
		$charset = $wpdb->get_charset_collate();
		$prefix  = $wpdb->prefix . 'apollo_';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Table: apollo_group_meta (supplements groups tables from core)
		dbDelta(
			"CREATE TABLE IF NOT EXISTS {$prefix}group_meta (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            group_id BIGINT UNSIGNED NOT NULL,
            meta_key VARCHAR(255) NOT NULL,
            meta_value LONGTEXT,
            PRIMARY KEY (id),
            KEY group_id (group_id),
            KEY meta_key (meta_key(191))
        ) {$charset}"
		);

		// Table: apollo_group_invitations (BuddyPress bp-groups invite pattern)
		dbDelta(
			"CREATE TABLE IF NOT EXISTS {$prefix}group_invitations (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            group_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL COMMENT 'Invitee',
            inviter_id BIGINT UNSIGNED NOT NULL,
            status ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
            message TEXT,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY group_user_invite (group_id,user_id),
            KEY group_id (group_id),
            KEY user_id (user_id),
            KEY inviter_id (inviter_id),
            KEY status (status)
        ) {$charset}"
		);

		// Table: apollo_group_bans (BuddyPress is_banned pattern, Apollo-native)
		dbDelta(
			"CREATE TABLE IF NOT EXISTS {$prefix}group_bans (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            group_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            banned_by BIGINT UNSIGNED NOT NULL,
            reason VARCHAR(500),
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY group_user_ban (group_id,user_id),
            KEY group_id (group_id),
            KEY user_id (user_id)
        ) {$charset}"
		);

		// Table: apollo_group_requests (membership requests for future private comunas)
		dbDelta(
			"CREATE TABLE IF NOT EXISTS {$prefix}group_requests (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            group_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            message TEXT,
            status ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
            handled_by BIGINT UNSIGNED,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY group_user_request (group_id,user_id),
            KEY group_id (group_id),
            KEY user_id (user_id),
            KEY status (status)
        ) {$charset}"
		);

		update_option( 'apollo_groups_version', APOLLO_GROUPS_VERSION );
		flush_rewrite_rules();
	}
}
