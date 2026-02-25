<?php

/**
 * Main Plugin Singleton
 *
 * Orchestrates all Gestor components: admin menu, AJAX handlers, assets.
 *
 * @package Apollo\Gestor
 */

declare(strict_types=1);

namespace Apollo\Gestor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {


	private static ?Plugin $instance = null;

	public static function get_instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	/**
	 * Initialize all plugin components
	 */
	public function init(): void {
		// Ensure DB tables exist
		$this->maybe_upgrade_db();

		// Admin
		if ( is_admin() ) {
			$admin = new Admin\Controller();
			$admin->init();
		}

		// Hooks
		do_action( 'apollo/gestor/initialized' );
	}

	/**
	 * Check if DB needs upgrade
	 */
	private function maybe_upgrade_db(): void {
		$installed = (int) get_option( 'apollo_gestor_db_version', 0 );

		if ( $installed < APOLLO_GESTOR_DB_VERSION ) {
			Database::install();
			update_option( 'apollo_gestor_db_version', APOLLO_GESTOR_DB_VERSION );
		}
	}
}
