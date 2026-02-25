<?php

/**
 * Database — creates and upgrades custom tables
 *
 * Tables:
 *   apollo_gestor_tasks      — Tasks linked to events
 *   apollo_gestor_team       — Team assignments per event
 *   apollo_gestor_payments   — Financial records
 *   apollo_gestor_milestones — Milestone checkpoints
 *   apollo_gestor_activity   — Activity log
 *
 * @package Apollo\Gestor
 */

declare(strict_types=1);

namespace Apollo\Gestor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Database {


	/**
	 * Install all tables
	 */
	public static function install(): void {
		global $wpdb;

		$charset = $wpdb->get_charset_collate();
		$prefix  = $wpdb->prefix;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// ─── Tasks ────────────────────────────────────────────────
		$sql_tasks = "CREATE TABLE {$prefix}apollo_gestor_tasks (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            title VARCHAR(255) NOT NULL DEFAULT '',
            description TEXT,
            assignee_id BIGINT UNSIGNED DEFAULT 0,
            category VARCHAR(100) DEFAULT '',
            priority ENUM('urgent','high','medium','low') DEFAULT 'medium',
            status ENUM('pending','in_progress','done','cancelled') DEFAULT 'pending',
            due_date DATE DEFAULT NULL,
            sort_order INT UNSIGNED DEFAULT 0,
            parent_id BIGINT UNSIGNED DEFAULT 0,
            created_by BIGINT UNSIGNED DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_event (event_id),
            KEY idx_assignee (assignee_id),
            KEY idx_status (status),
            KEY idx_due (due_date),
            KEY idx_parent (parent_id)
        ) {$charset};";

		// ─── Team ─────────────────────────────────────────────────
		$sql_team = "CREATE TABLE {$prefix}apollo_gestor_team (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            role ENUM('adm','gestor','tgestor','team') DEFAULT 'team',
            job_function VARCHAR(255) DEFAULT '',
            pix_key VARCHAR(255) DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY idx_event_user (event_id, user_id),
            KEY idx_user (user_id),
            KEY idx_role (role)
        ) {$charset};";

		// ─── Payments ─────────────────────────────────────────────
		$sql_payments = "CREATE TABLE {$prefix}apollo_gestor_payments (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            payee_type ENUM('staff','supplier') DEFAULT 'staff',
            payee_id BIGINT UNSIGNED DEFAULT 0,
            description VARCHAR(255) DEFAULT '',
            category VARCHAR(100) DEFAULT '',
            amount DECIMAL(10,2) DEFAULT 0.00,
            pix_key VARCHAR(255) DEFAULT '',
            status ENUM('paid','pending','late') DEFAULT 'pending',
            due_date DATE DEFAULT NULL,
            paid_at DATETIME DEFAULT NULL,
            created_by BIGINT UNSIGNED DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_event (event_id),
            KEY idx_payee (payee_type, payee_id),
            KEY idx_status (status)
        ) {$charset};";

		// ─── Milestones ───────────────────────────────────────────
		$sql_milestones = "CREATE TABLE {$prefix}apollo_gestor_milestones (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            title VARCHAR(255) NOT NULL DEFAULT '',
            icon VARCHAR(100) DEFAULT 'ri-flag-2-line',
            due_date DATE DEFAULT NULL,
            status ENUM('pending','done') DEFAULT 'pending',
            sort_order INT UNSIGNED DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_event (event_id),
            KEY idx_status (status)
        ) {$charset};";

		// ─── Activity log ─────────────────────────────────────────
		$sql_activity = "CREATE TABLE {$prefix}apollo_gestor_activity (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            user_id BIGINT UNSIGNED DEFAULT 0,
            action VARCHAR(100) NOT NULL DEFAULT '',
            entity_type VARCHAR(50) DEFAULT '',
            entity_id BIGINT UNSIGNED DEFAULT 0,
            meta TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_event (event_id),
            KEY idx_user (user_id),
            KEY idx_created (created_at)
        ) {$charset};";

		dbDelta( $sql_tasks );
		dbDelta( $sql_team );
		dbDelta( $sql_payments );
		dbDelta( $sql_milestones );
		dbDelta( $sql_activity );
	}

	/**
	 * Drop all tables (used by uninstall)
	 */
	public static function uninstall(): void {
		global $wpdb;
		$prefix = $wpdb->prefix;

		$tables = array(
			'apollo_gestor_activity',
			'apollo_gestor_milestones',
			'apollo_gestor_payments',
			'apollo_gestor_team',
			'apollo_gestor_tasks',
		);

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$prefix}{$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		delete_option( 'apollo_gestor_db_version' );
	}
}
