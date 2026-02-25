<?php

/**
 * Plugin Activation — creates database tables, schedules cron, seeds defaults.
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

declare(strict_types=1);

namespace Apollo\Email;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {

	/**
	 * Run activation routine.
	 */
	public static function activate(): void {
		self::checkRequirements();
		self::createTables();
		self::seedDefaults();
		self::scheduleCron();
		self::seedDefaultTemplates();

		update_option( 'apollo_email_db_version', APOLLO_EMAIL_DB_VERSION );
		update_option( 'apollo_email_installed_at', current_time( 'mysql' ) );
		set_transient( 'apollo_email_activated', true, 30 );

		flush_rewrite_rules();
	}

	/**
	 * Verify system requirements.
	 */
	private static function checkRequirements(): void {
		if ( version_compare( PHP_VERSION, APOLLO_EMAIL_MIN_PHP, '<' ) ) {
			wp_die(
				sprintf(
					'Apollo Email requer PHP %s+. Atual: %s',
					APOLLO_EMAIL_MIN_PHP,
					PHP_VERSION
				),
				'Requisito não atendido',
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Create database tables.
	 */
	private static function createTables(): void {
		global $wpdb;
		$charset = $wpdb->get_charset_collate();
		$prefix  = $wpdb->prefix . 'apollo_';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// ── Email Queue ──────────────────────────────────────────
		$sql_queue = "CREATE TABLE {$prefix}email_queue (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            to_email VARCHAR(255) NOT NULL,
            to_name VARCHAR(255) DEFAULT '',
            subject VARCHAR(500) NOT NULL,
            body LONGTEXT NOT NULL,
            template VARCHAR(100) DEFAULT NULL,
            template_data JSON DEFAULT NULL,
            priority INT NOT NULL DEFAULT 5,
            status ENUM('pending','processing','sent','failed','cancelled') NOT NULL DEFAULT 'pending',
            attempts INT NOT NULL DEFAULT 0,
            max_attempts INT NOT NULL DEFAULT 3,
            scheduled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            sent_at DATETIME DEFAULT NULL,
            error_message TEXT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_status (status),
            KEY idx_scheduled (scheduled_at),
            KEY idx_priority (priority, scheduled_at),
            KEY idx_template (template),
            PRIMARY KEY (id)
        ) {$charset};";

		// ── Email Log ────────────────────────────────────────────
		$sql_log = "CREATE TABLE {$prefix}email_log (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            to_email VARCHAR(255) NOT NULL,
            subject VARCHAR(500) NOT NULL,
            template VARCHAR(100) DEFAULT NULL,
            email_type ENUM('transactional','marketing','digest') DEFAULT 'transactional',
            status ENUM('sent','failed','bounced','opened','clicked') NOT NULL DEFAULT 'sent',
            transport VARCHAR(50) DEFAULT 'wp_mail',
            sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            opened_at DATETIME DEFAULT NULL,
            clicked_at DATETIME DEFAULT NULL,
            error_message TEXT DEFAULT NULL,
            meta JSON DEFAULT NULL,
            KEY idx_email (to_email),
            KEY idx_status (status),
            KEY idx_sent (sent_at),
            KEY idx_type (email_type),
            KEY idx_template (template),
            PRIMARY KEY (id)
        ) {$charset};";

		dbDelta( $sql_queue );
		dbDelta( $sql_log );
	}

	/**
	 * Seed default settings.
	 */
	private static function seedDefaults(): void {
		$defaults = array(
			'from_name'        => get_bloginfo( 'name' ),
			'from_email'       => get_bloginfo( 'admin_email' ),
			'reply_to'         => get_bloginfo( 'admin_email' ),
			'transport'        => 'wp_mail',
			'smtp_host'        => '',
			'smtp_port'        => 587,
			'smtp_encryption'  => 'tls',
			'smtp_username'    => '',
			'smtp_password'    => '',
			'ses_region'       => 'us-east-1',
			'ses_access_key'   => '',
			'ses_secret_key'   => '',
			'sendgrid_api_key' => '',
			'track_opens'      => true,
			'track_clicks'     => true,
			'batch_size'       => 50,
			'max_retries'      => 3,
			'brand_color'      => '#6C3BF5',
			'brand_logo'       => '',
			'footer_text'      => '© ' . gmdate( 'Y' ) . ' Apollo Rio. Todos os direitos reservados.',
			'footer_address'   => 'Rio de Janeiro, RJ — Brasil',
			'wp_mail_override' => false,
		);

		if ( ! get_option( 'apollo_email_settings' ) ) {
			update_option( 'apollo_email_settings', $defaults );
		}
	}

	/**
	 * Schedule cron event for queue processing.
	 */
	private static function scheduleCron(): void {
		// Register custom interval
		add_filter(
			'cron_schedules',
			function ( array $schedules ): array {
				$schedules['apollo_five_minutes'] = array(
					'interval' => 300,
					'display'  => __( 'A cada 5 minutos', 'apollo-email' ),
				);
				return $schedules;
			}
		);

		if ( ! wp_next_scheduled( APOLLO_EMAIL_CRON_HOOK ) ) {
			wp_schedule_event( time(), 'apollo_five_minutes', APOLLO_EMAIL_CRON_HOOK );
		}
	}

	/**
	 * Seed default email templates as email_aprio CPT posts.
	 */
	private static function seedDefaultTemplates(): void {
		$templates = array(
			array(
				'slug'      => 'welcome',
				'title'     => 'Boas-vindas',
				'subject'   => 'Bem-vindo(a) ao {{site_name}}! 🎉',
				'type'      => 'transactional',
				'variables' => array( 'user_name', 'username', 'profile_url', 'site_name', 'site_url' ),
			),
			array(
				'slug'      => 'password-reset',
				'title'     => 'Recuperar Senha',
				'subject'   => 'Sua nova chave de acesso — {{site_name}}',
				'type'      => 'transactional',
				'variables' => array( 'user_name', 'reset_url', 'site_name', 'expires_in' ),
			),
			array(
				'slug'      => 'verification',
				'title'     => 'Verificação de Email',
				'subject'   => 'Verifique seu email — {{site_name}}',
				'type'      => 'transactional',
				'variables' => array( 'user_name', 'verify_url', 'site_name' ),
			),
			array(
				'slug'      => 'notification',
				'title'     => 'Notificação Geral',
				'subject'   => '{{title}} — {{site_name}}',
				'type'      => 'transactional',
				'variables' => array( 'user_name', 'title', 'message', 'action_url', 'action_text', 'site_name' ),
			),
			array(
				'slug'      => 'event-reminder',
				'title'     => 'Lembrete de Evento',
				'subject'   => 'Lembrete: {{event_title}} é hoje! 🎶',
				'type'      => 'transactional',
				'variables' => array( 'user_name', 'event_title', 'event_url', 'event_date', 'event_time', 'loc_name', 'site_name' ),
			),
			array(
				'slug'      => 'digest',
				'title'     => 'Resumo Semanal',
				'subject'   => 'Seu resumo semanal — {{site_name}}',
				'type'      => 'digest',
				'variables' => array( 'user_name', 'notifications', 'site_name', 'site_url' ),
			),
		);

		foreach ( $templates as $tpl ) {
			// Skip if already exists
			$existing = get_posts(
				array(
					'post_type'   => 'email_aprio',
					'name'        => $tpl['slug'],
					'post_status' => 'any',
					'numberposts' => 1,
				)
			);

			if ( ! empty( $existing ) ) {
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_type'    => 'email_aprio',
					'post_title'   => $tpl['title'],
					'post_name'    => $tpl['slug'],
					'post_status'  => 'publish',
					'post_content' => '',
				)
			);

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_email_subject', $tpl['subject'] );
				update_post_meta( $post_id, '_email_type', $tpl['type'] );
				update_post_meta( $post_id, '_email_variables', $tpl['variables'] );
			}
		}
	}
}
