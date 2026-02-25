<?php
/**
 * Plugin Name: Apollo Statistics
 * Plugin URI:  https://apollo.rio.br
 * Description: O Canhão de Métricas — Central de analytics e estatísticas do ecossistema Apollo. Consolida dados de WP Statistics, Events Manager, Apollo Users, BuddyPress, Apollo Fav e UIPress Lite em dashboards unificados.
 * Version:     1.0.0
 * Author:      Apollo RIO
 * Author URI:  https://apollo.rio.br
 * Text Domain: apollo-statistics
 * Domain Path: /languages
 * Requires PHP: 8.1
 * Requires at least: 6.4
 * License:     GPL-2.0+
 *
 * @package Apollo\Statistics
 *
 * COMPLIANCE: apollo-registry.json
 * - Namespace: Apollo\Statistics
 * - Layer: L7_admin
 * - Priority: 5
 * - Tables: apollo_stats_events, apollo_stats_users, apollo_stats_content
 * - Depends: apollo-core
 * - REST: /stats/overview, /stats/events, /stats/users, /stats/content, /stats/export
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─────────────────────────────────────────────────────────────
// Constantes do plugin
// ─────────────────────────────────────────────────────────────
define( 'APOLLO_STATISTICS_VERSION', '1.0.0' );
define( 'APOLLO_STATISTICS_PATH', plugin_dir_path( __FILE__ ) );
define( 'APOLLO_STATISTICS_URL', plugin_dir_url( __FILE__ ) );
define( 'APOLLO_STATISTICS_FILE', __FILE__ );

// ─────────────────────────────────────────────────────────────
// Autoloader
// ─────────────────────────────────────────────────────────────
spl_autoload_register(
	function ( string $class_name ): void {
		$prefix   = 'Apollo\\Statistics\\';
		$base_dir = APOLLO_STATISTICS_PATH . 'includes/';

		if ( strpos( $class_name, $prefix ) !== 0 ) {
				return;
		}

		$relative = substr( $class_name, strlen( $prefix ) );
		$file     = $base_dir . 'class-' . strtolower( str_replace( array( '\\', '_' ), array( '/', '-' ), $relative ) ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);

// ─────────────────────────────────────────────────────────────
// Ativação — cria tabelas de estatísticas
// ─────────────────────────────────────────────────────────────
register_activation_hook(
	__FILE__,
	function (): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Tabela: apollo_stats_events — métricas de eventos.
		$table_events = $wpdb->prefix . 'apollo_stats_events';
		dbDelta(
			"CREATE TABLE IF NOT EXISTS {$table_events} (
        id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        event_id        BIGINT UNSIGNED NOT NULL,
        metric_type     VARCHAR(50)     NOT NULL DEFAULT 'view',
        metric_value    BIGINT          NOT NULL DEFAULT 0,
        recorded_date   DATE            NOT NULL,
        created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uk_event_metric_date (event_id, metric_type, recorded_date),
        KEY idx_recorded_date (recorded_date),
        KEY idx_metric_type (metric_type)
    ) {$charset_collate};"
		);

		// Tabela: apollo_stats_users — métricas de usuários.
		$table_users = $wpdb->prefix . 'apollo_stats_users';
		dbDelta(
			"CREATE TABLE IF NOT EXISTS {$table_users} (
        id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id         BIGINT UNSIGNED NOT NULL DEFAULT 0,
        metric_type     VARCHAR(50)     NOT NULL DEFAULT 'registration',
        metric_value    BIGINT          NOT NULL DEFAULT 0,
        recorded_date   DATE            NOT NULL,
        created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uk_user_metric_date (user_id, metric_type, recorded_date),
        KEY idx_recorded_date (recorded_date),
        KEY idx_metric_type (metric_type)
    ) {$charset_collate};"
		);

		// Tabela: apollo_stats_content — métricas de conteúdo geral.
		$table_content = $wpdb->prefix . 'apollo_stats_content';
		dbDelta(
			"CREATE TABLE IF NOT EXISTS {$table_content} (
        id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        post_id         BIGINT UNSIGNED NOT NULL DEFAULT 0,
        post_type       VARCHAR(60)     NOT NULL DEFAULT 'post',
        metric_type     VARCHAR(50)     NOT NULL DEFAULT 'view',
        metric_value    BIGINT          NOT NULL DEFAULT 0,
        recorded_date   DATE            NOT NULL,
        created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uk_content_metric_date (post_id, metric_type, recorded_date),
        KEY idx_recorded_date (recorded_date),
        KEY idx_post_type (post_type),
        KEY idx_metric_type (metric_type)
    ) {$charset_collate};"
		);

		update_option( 'apollo_statistics_db_version', APOLLO_STATISTICS_VERSION );
		flush_rewrite_rules();
	}
);

// ─────────────────────────────────────────────────────────────
// Desativação
// ─────────────────────────────────────────────────────────────
register_deactivation_hook(
	__FILE__,
	function (): void {
		// Remove cron jobs.
		wp_clear_scheduled_hook( 'apollo_statistics_daily_collect' );
		flush_rewrite_rules();
	}
);

// ─────────────────────────────────────────────────────────────
// Verificação de dependência
// ─────────────────────────────────────────────────────────────
add_action(
	'admin_init',
	function (): void {
		if ( ! is_plugin_active( 'apollo-core/apollo-core.php' ) ) {
			add_action(
				'admin_notices',
				function (): void {
					echo '<div class="notice notice-error"><p>';
					echo '<strong>Apollo Statistics:</strong> Requer <code>apollo-core</code> ativo.';
					echo '</p></div>';
				}
			);
		}
	}
);

// ─────────────────────────────────────────────────────────────
// Inicialização principal
// ─────────────────────────────────────────────────────────────
add_action(
	'plugins_loaded',
	function (): void {

		// Carrega funções globais.
		require_once APOLLO_STATISTICS_PATH . 'includes/functions.php';

		// Coletor de dados — consome providers do apollo-fav e outros.
		$collector = new Apollo\Statistics\Data_Collector();
		$collector->init();

		// Processador de métricas — normaliza e persistência.
		$processor = new Apollo\Statistics\Metrics_Processor();
		$processor->init();

		// Renderer de relatórios.
		$reports = new Apollo\Statistics\Reports();
		$reports->init();

		// REST API endpoints.
		add_action( 'rest_api_init', array( new Apollo\Statistics\REST_Controller(), 'register_routes' ) );
	},
	25
); // Depois do apollo-fav (priority 20).
